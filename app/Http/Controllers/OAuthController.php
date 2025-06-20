<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\Client;
use Laravel\Passport\RefreshToken;
use Laravel\Passport\Token;
use App\Models\User;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\RefreshTokenRepository;

class OAuthController extends Controller
{
    /**
     * Handle OAuth authorization request
     */
    public function authorize(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|string',
            'redirect_uri' => 'required|url',
            'response_type' => 'required|string|in:code',
            'scope' => 'nullable|string',
            'state' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'invalid_request',
                'error_description' => $validator->errors()->first(),
            ], 400);
        }

        // Check if client exists
        $client = Client::where('id', $request->client_id)->first();
        if (!$client) {
            return response()->json([
                'error' => 'invalid_client',
                'error_description' => 'Client not found',
            ], 400);
        }

        // Check if redirect URI matches
        if (!str_contains($client->redirect, $request->redirect_uri)) {
            return response()->json([
                'error' => 'invalid_redirect_uri',
                'error_description' => 'Redirect URI mismatch',
            ], 400);
        }

        // Return authorization data for frontend to handle
        return response()->json([
            'client' => [
                'id' => $client->id,
                'name' => $client->name,
                'redirect_uri' => $request->redirect_uri,
            ],
            'scopes' => $this->parseScopes($request->scope),
            'state' => $request->state,
        ]);
    }

    /**
     * Handle OAuth token request for first-party applications
     */
    public function tokenFirstParty(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'grant_type' => 'required|string|in:password,refresh_token',
            'client_id' => 'required|string',
            'client_secret' => 'required|string',
            'username' => 'required_if:grant_type,password|email',
            'password' => 'required_if:grant_type,password|string',
            'refresh_token' => 'required_if:grant_type,refresh_token|string',
            'scope' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'invalid_request',
                'error_description' => $validator->errors()->first(),
            ], 400);
        }

        // Verify client credentials
        $client = $this->verifyClient($request->client_id, $request->client_secret);
        if (!$client) {
            return response()->json([
                'error' => 'invalid_client',
                'error_description' => 'Invalid client credentials',
            ], 401);
        }

        if ($request->grant_type === 'password') {
            return $this->handlePasswordGrant($request, $client);
        } elseif ($request->grant_type === 'refresh_token') {
            return $this->handleRefreshTokenGrant($request, $client);
        }

        return response()->json([
            'error' => 'unsupported_grant_type',
            'error_description' => 'The grant type is not supported',
        ], 400);
    }

    /**
     * Get user information
     */
    public function userInfo(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'error' => 'unauthorized',
                'error_description' => 'Invalid access token',
            ], 401);
        }

        // Check token scopes
        $token = $request->user()->token();
        $scopes = $token->scopes ?? [];

        return response()->json([
            'sub' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'email_verified' => $user->email_verified_at !== null,
            'role' => $user->role,
            'nema' => in_array('read-profile', $scopes) ? $user->nema : null,
            'number' => in_array('read-profile', $scopes) ? $user->number : null,
            'practicing' => in_array('read-profile', $scopes) ? $user->practicing : null,
        ]);
    }

    /**
     * Revoke access token
     */
    public function revokeToken(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'token_type_hint' => 'nullable|string|in:access_token,refresh_token',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'invalid_request',
                'error_description' => $validator->errors()->first(),
            ], 400);
        }

        $tokenRepository = app(TokenRepository::class);
        $refreshTokenRepository = app(RefreshTokenRepository::class);

        // Try to revoke as access token first
        $token = $tokenRepository->find($request->token);
        if ($token) {
            $tokenRepository->revokeAccessToken($token->id);
            
            // Also revoke associated refresh tokens
            $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($token->id);
            
            return response()->json(['message' => 'Token revoked successfully']);
        }

        // Try to revoke as refresh token
        $refreshToken = $refreshTokenRepository->find($request->token);
        if ($refreshToken) {
            $refreshTokenRepository->revokeRefreshToken($refreshToken->id);
            return response()->json(['message' => 'Token revoked successfully']);
        }

        return response()->json([
            'error' => 'invalid_token',
            'error_description' => 'Token not found',
        ], 400);
    }

    /**
     * Get client information
     */
    public function clientInfo(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'invalid_request',
                'error_description' => $validator->errors()->first(),
            ], 400);
        }

        $client = Client::where('id', $request->client_id)->first();
        if (!$client) {
            return response()->json([
                'error' => 'invalid_client',
                'error_description' => 'Client not found',
            ], 404);
        }

        return response()->json([
            'client_id' => $client->id,
            'client_name' => $client->name,
            'redirect_uris' => explode(',', $client->redirect),
            'grant_types' => [
                'authorization_code',
                'refresh_token',
                'password', // For first-party apps
            ],
            'scopes' => array_keys(config('passport.scopes', [])),
        ]);
    }

    /**
     * Handle password grant
     */
    private function handlePasswordGrant(Request $request, Client $client): JsonResponse
    {
        $credentials = [
            'email' => $request->username,
            'password' => $request->password,
        ];

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'error' => 'invalid_grant',
                'error_description' => 'Invalid username or password',
            ], 400);
        }

        $user = Auth::user();
        
        // Check if user is verified (if required)
        if ($user->role !== 'Admin' && !$user->email_verified_at) {
            return response()->json([
                'error' => 'access_denied',
                'error_description' => 'Account pending admin approval',
            ], 403);
        }

        // Parse scopes
        $scopes = $this->parseScopes($request->scope);
        
        // Create access token
        $tokenResult = $user->createOAuthToken('API Access Token', $scopes);
        
        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_in' => config('passport.token_expiration.access_token', 60) * 60,
            'refresh_token' => $tokenResult->refreshToken,
            'scope' => implode(' ', $scopes),
        ]);
    }

    /**
     * Handle refresh token grant
     */
    private function handleRefreshTokenGrant(Request $request, Client $client): JsonResponse
    {
        $refreshTokenRepository = app(RefreshTokenRepository::class);
        $tokenRepository = app(TokenRepository::class);

        $refreshToken = $refreshTokenRepository->find($request->refresh_token);
        
        if (!$refreshToken || $refreshToken->revoked) {
            return response()->json([
                'error' => 'invalid_grant',
                'error_description' => 'Invalid refresh token',
            ], 400);
        }

        // Get the access token
        $accessToken = $tokenRepository->find($refreshToken->access_token_id);
        if (!$accessToken) {
            return response()->json([
                'error' => 'invalid_grant',
                'error_description' => 'Invalid refresh token',
            ], 400);
        }

        // Get user
        $user = User::find($accessToken->user_id);
        if (!$user) {
            return response()->json([
                'error' => 'invalid_grant',
                'error_description' => 'User not found',
            ], 400);
        }

        // Revoke old tokens
        $tokenRepository->revokeAccessToken($accessToken->id);
        $refreshTokenRepository->revokeRefreshToken($refreshToken->id);

        // Create new token
        $scopes = $accessToken->scopes ?? [];
        $tokenResult = $user->createOAuthToken('API Access Token', $scopes);

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_in' => config('passport.token_expiration.access_token', 60) * 60,
            'refresh_token' => $tokenResult->refreshToken,
            'scope' => implode(' ', $scopes),
        ]);
    }

    /**
     * Verify client credentials
     */
    private function verifyClient(string $clientId, string $clientSecret): ?Client
    {
        $client = Client::where('id', $clientId)->first();
        
        if (!$client) {
            return null;
        }

        // Check if client secret matches
        if (config('passport.hash_client_secrets', false)) {
            if (!password_verify($clientSecret, $client->secret)) {
                return null;
            }
        } else {
            if ($client->secret !== $clientSecret) {
                return null;
            }
        }

        return $client;
    }

    /**
     * Parse scopes from request
     */
    private function parseScopes(?string $scope): array
    {
        if (!$scope) {
            return [config('passport.default_scope', 'read-user')];
        }

        $requestedScopes = explode(' ', $scope);
        $availableScopes = array_keys(config('passport.scopes', []));
        
        return array_intersect($requestedScopes, $availableScopes);
    }
} 
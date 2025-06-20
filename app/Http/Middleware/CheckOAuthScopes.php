<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckOAuthScopes
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$scopes): Response
    {
        if (!$request->user()) {
            return response()->json([
                'error' => 'unauthorized',
                'error_description' => 'Access token required',
            ], 401);
        }

        $token = $request->user()->token();
        
        if (!$token) {
            return response()->json([
                'error' => 'unauthorized',
                'error_description' => 'Invalid access token',
            ], 401);
        }

        $userScopes = $token->scopes ?? [];

        foreach ($scopes as $scope) {
            if (!in_array($scope, $userScopes)) {
                return response()->json([
                    'error' => 'insufficient_scope',
                    'error_description' => "Required scope: {$scope}",
                ], 403);
            }
        }

        return $next($request);
    }
} 
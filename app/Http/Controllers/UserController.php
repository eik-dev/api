<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\LoginRequest;

use App\Models\User;
use App\Models\Individual;
use App\Models\Firm;
use App\Models\Admin;

/**
 * Handle user registration, login, and session management.
 */
class UserController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function login(LoginRequest $request): Response
    {
        try{
            $request->authenticate();

            $request->session()->regenerate();

            return response()->noContent();
        } catch (\Exception $e) {
            return response()->json([ 'error' => true, 'message' => $e->getMessage() ],401);
        }
    }
    /**
     * Login and authenticate a user.
     */
    public function store(Request $request)
    {
        try{
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);


            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $token = $user->createToken('auth_token')->plainTextToken;


                return response()->json([
                    'user' => [
                        'role' => $user->role,
                        'token'=> $token,
                    ],
                ]);
            }

            return response()->json(['error' => 'Invalid credentials'], 401);
        } catch (\Exception $e) {
            return response()->json([ 'error' => true, 'message' => $e->getMessage() ],401);
        }
    }

    /**
     * Get the authenticated user.
     */
    public function show(Request $request)
{
    $user = $request->user(); // Get the authenticated user

    if ($user) {
        return response()->json([
            'user' => [
                'role' => $user->role,
            ],
        ]);
    } else {
        // return a 401 error response if there's no authenticated user
        return response()->json(['error' => 'Not Authenticated'], 401);
    }
}

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }

    /**
     * Register new user
     */
    public function onboard(Request $request)
    {
        try{
            $credentials = $request->validate([
                'name' => 'required',
                'role' => 'required',
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $user = User::create($credentials);

            return response()->json([
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            return response()->json([ 'error' => true, 'message' => $e->getMessage() ]);
        }
    }
}
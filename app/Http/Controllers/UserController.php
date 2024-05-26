<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\EmailController;

use App\Models\User;
use App\Models\Individual;
use App\Models\Firm;
use App\Models\Files;

/**
 * Handle user registration, login, and session management.
 */
class UserController extends Controller
{
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

            //check if email exists
            if (!User::where('email',$credentials['email'])->exists()) {
                return response()->json(['error' => 'Account not found'], 401);
            }

            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                //check if email is verified
                if ($user->role!='Admin' && !$user->email_verified_at) {
                    return response()->json(['error' => 'Pending admin approval'], 401);
                }
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
            return response()->json([ 'error' => $e->getMessage() ],401);
        }
    }

    /**
     * Get the authenticated user.
     */
    public function show(Request $request)
    {
        $user = $request->user(); // Get the authenticated user
        $photo = Files::where('user_id',$user->id)->where('folder','profile')->first();

        if ($user) {
            return response()->json([
                'user' => [
                    'role' => $user->role,
                    'name' => $user->name,
                    'photo' => $photo ? $photo->url : null,
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
                'username' => '',
                'nema' => '',
            ]);

            User::create($credentials);
            //create email and password credentials to authenticate user
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                if ($request->role=='Individual') {
                    Individual::create($request->profile,$user->id,$request->education,$request->profession);
                } else if ($request->role=='Firm') {
                    Firm::create($request->profile, $user->id);
                }
    
                $token = $user->createToken('auth_token')->plainTextToken;
    
                return response()->json([
                    'token' => $token
                ]);
            }
            else {
                throw_if(true, \Exception::class, 'User not created');
            }
        } catch (\Exception $e) {
            return response()->json([ 
                'error' => $e->getMessage(),
                'request'=>$request->all()
            ], 401);
        }
    }
}
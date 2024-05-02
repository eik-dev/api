<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

use App\Models\User;

class RegistrationController extends Controller
{
    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request)
    {
        try{
            $credentials = $request->validate([
                'name' => 'required',
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
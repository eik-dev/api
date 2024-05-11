<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Reset;

class ResetController extends Controller
{
    /**
     * Send one time link to reset password
     */
    public function store(Request $request)
    {
        try{
            $user = User::where('email',$request->email)->first();
            if ($user) {
                $emailController = new EmailController();
                $record = Reset::create($request->email);
                $emailController->sendRecoveryEmail($request->email, $record->token);
                return response()->json([
                    'success' => 'Email sent successfully'
                ]);
            } else {
                return response()->json([
                    'error' => 'User not found',
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([ 
                'error' => $e->getMessage(),
                'request' => $request->all()
            ], 401);
        }
    }

    /**
     * Reset password
     */
    public function update(Request $request)
    {
        try{
            $record = Reset::where('token', $request->token)->first();
            if (strtotime($record->created_at) < strtotime('-1 hour')) {
                return response()->json([
                    'error' => 'Token expired',
                ], 401);
            }
            if ($record) {
                $user = User::where('email', $record->email)->first();
                $user->password = bcrypt($request->password);
                $user->save();
                $record->delete();
                return response()->json([
                    'success' => 'Password reset successfully'
                ]);
            } else {
                return response()->json([
                    'error' => 'Invalid token',
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([ 
                'error' => $e->getMessage(),
                'request' => $request->all()
            ], 401);
        }
    }

    /**
     * Check if token is valid
     */
    public function show(Request $request)
    {
        try{
            $record = Reset::where('token', $request->token)->first();
            if (strtotime($record->created_at) < strtotime('-1 hour')) {
                return response()->json([
                    'error' => 'Token expired',
                ], 401);
            }
            if ($record) {
                return response()->json([
                    'success' => 'Token is valid'
                ]);
            } else {
                return response()->json([
                    'error' => 'Invalid token',
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([ 
                'error' => $e->getMessage(),
                'request' => $request->all()
            ], 401);
        }
    }
}

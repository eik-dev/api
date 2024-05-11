<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Individual;
use App\Models\Firm;
use App\Models\Certificates;

//populates dashboard with stats
class AdminController extends Controller
{
    /**
     * Add new admins
     */
    public function store(Request $request){
        $user = $request->user();
        if ($user) {
            if ($user->role=='Admin') {
                $admin = User::create([
                    'name' => $request->name,
                    'username' => $request->username,
                    'role' => 'Admin',
                    'email' => $request->email,
                    'password' => bcrypt('Admin123'),
                ]);
                return response()->json([
                    'admin' => $admin
                ]);
            } else {
                return response()->json([
                    'error' => 'Unauthorized',
                ], 401);
            }
        } else {
            return response()->json([
                'error' => 'Unauthorized',
            ], 401);
        }
    }

    /**
     * List all admins
     */
    public function index(Request $request){
        $user = $request->user();
        if ($user) {
            if ($user->role=='Admin') {
                $admins = User::where('role', 'Admin')->get();
                return response()->json($admins);
            } else {
                return response()->json([
                    'error' => 'Unauthorized',
                ], 401);
            }
        } else {
            return response()->json([
                'error' => 'Unauthorized',
            ], 401);
        }
    }

    /**
     * Get all members
     */
    public function members(Request $request){
        try{
            $user = $request->user();
            if ($user) {
                if ($user->role=='Admin') {
                    $members = User::where('role', 'Individual')
                    ->with('certificates:user_id,number')
                    ->get();
                    return response()->json($members);
                } else {
                    return response()->json([
                        'error' => 'Unauthorized',
                    ], 401);
                }
            } else {
                return response()->json([
                    'error' => 'Unauthorized',
                ], 401);
            }
        }
        catch (\Exception $e) {
            return response()->json([ 'error' => $e->getMessage() ],401);
        }
    }

    /**
     * Verify user
     */
    public function verify(Request $request){
        try{
            $user = $request->user();
            if ($user) {
                if ($user->role=='Admin') {
                    $member = User::find($request->user);
                    if ($request->verify == 'true'){
                        $member->email_verified_at = now();
                    }else{
                        $member->email_verified_at = null;
                    }
                    $member->save();
                    return response()->json([
                        'message' => $request->verify == 'true' ? 'User verified' : 'User unverified',
                    ]);
                } else {
                    return response()->json([
                        'error' => 'Unauthorized',
                    ], 401);
                }
            } else {
                return response()->json([
                    'error' => 'Unauthorized',
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all firms
     */
    public function firms(Request $request){
        $user = $request->user();
        if ($user) {
            if ($user->role=='Admin') {
                $firms = User::where('role', 'Firm')
                ->with('certificates:user_id,number')
                ->with('firm:user_id,kra')
                ->get();
                return response()->json($firms);
            } else {
                return response()->json([
                    'error' => 'Unauthorized',
                ], 401);
            }
        } else {
            return response()->json([
                'error' => 'Unauthorized',
            ], 401);
        }
    }
}
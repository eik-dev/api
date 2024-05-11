<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Certificates;
use App\Models\Individual;
use App\Models\Firm;

//populates dashboard with stats
class ProfileController extends Controller
{
    /**
     * Get user profile information
     */
    public function show(Request $request)
    {
        try{
            $user = $request->user();
            if ($user) {
                if ($user->role=='Individual') {
                    $profile = Individual::where('user_id',$user->id)->first();
                } else if ($user->role=='Firm') {
                    $profile = Firm::where('user_id',$user->id)->first();
                }
                $profile->name = $user->name;
                $profile->email = $user->email;
                $cert = Certificates::where('user_id',$user->id)->first();
                return response()->json([
                    'profile' => $profile,
                    'certificate' => $cert
                ]);
            } else {
                return response()->json([
                    'error' => 'User not found',
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([ 
                'error' => $e->getMessage(),
            ], 401);
        }
    }

    /**
     * Modify user information
     */
    public function update(Request $request, $section){
        try{
            $user = $request->user();
            if ($user) {
                if ($user->role=='Individual') {
                    $profile = Individual::where('user_id',$user->id)->first();
                } else if ($user->role=='Firm') {
                    $profile = Firm::where('user_id',$user->id)->first();
                }
                $profile->name = $request->name;
                $profile->email = $request->email;
                $profile->save();
                return response()->json([
                    'profile' => $profile
                ]);
            } else {
                return response()->json([
                    'error' => 'User not found',
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([ 
                'error' => $e->getMessage(),
            ], 401);
        }
    }    
}
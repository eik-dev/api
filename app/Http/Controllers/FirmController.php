<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Firm;

//populates dashboard with stats
class FirmController extends Controller
{
    /**
     *Get all firm members
     */
    public function members(Request $request){
        $user = $request->user();
        if ($user) {
            if ($user->role=='Firm') {
                $members = User::where('firm', $user->name)->get();
                return response()->json([
                    'members' => $members
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
}
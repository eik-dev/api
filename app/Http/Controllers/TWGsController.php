<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TWG;

class TWGsController extends Controller
{
    public function index(Request $request){
        try{
            $user = $request->user();
            if ($user) {
                $twgs = TWG::where('user_id', $user->id)->first();
                return response()->json($twgs ? json_decode($twgs->twgs) : [], 200);
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
    public function join(Request $request){
        try{
            $user = $request->user();
            if ($user) {
                $twg = TWG::where('user_id', $user->id)->first();
                if ($twg) {
                    $groups = json_decode($twg->twgs);
                    throw_if(count($groups) > 2, \Exception::class, 'You can only have 2 TWGs');
                    $groups[] = $request->twg;
                    $twg->twgs = json_encode($groups);
                    $twg->save();
                } else {
                    $twg = TWG::create($user->id, $request->twg);
                }
                return response()->json([
                    'message' => 'TWGs updated successfully',
                ], 200);
            } else {
                return response()->json([
                    'error' => 'Unauthorized',
                    'request' => $request->all(),
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function exit(Request $request){
        try{
            $user = $request->user();
            if ($user) {
                $twg = TWG::where('user_id', $user->id)->first();
                if ($twg) {
                    $groups = json_decode($twg->twgs);
                    $groups = array_filter($groups, function($group) use ($request){
                        return $group != $request->twg;
                    });
                    $twg->twgs = json_encode($groups);
                    $twg->save();
                }
                return response()->json([
                    'message' => 'TWGs updated successfully',
                ], 200);
            } else {
                return response()->json([
                    'error' => 'Unauthorized',
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'request' => $request->all(),
            ], 500);
        }
    }
    
}

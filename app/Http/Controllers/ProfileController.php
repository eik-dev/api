<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Certificates;
use App\Models\Individual;
use App\Models\Firm;
use App\Models\Education;
use App\Models\Profession;
use App\Models\User;
use App\Models\Training;

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
            $id = ($request->id && $user->role=='Admin')?$request->id:$user->id;
            $cert = Certificates::where('user_id',$id)->first();
            if ($user) {
                if ($user->role=='Individual' || $request->role=='Individual') {
                    $profile = Individual::where('user_id',$id)->first();
                    $education = Education::where('user_id',$id)->get();
                    $profession = Profession::where('user_id',$id)->get();
                    if ($request->id && $user->role=='Admin'){
                        $userDetails = User::where('id',$id)->first();
                        $profile->name = $userDetails->name;
                        $profile->email = $userDetails->email;
                        $profile->nema = $userDetails->nema;
                    } else {
                        $profile->name = $user->name;
                        $profile->email = $user->email;
                        $profile->nema = $user->nema;
                    }
                    return response()->json([
                        'profile' => $profile,
                        'certificate' => $cert,
                        'education' => $education,
                        'profession' => $profession,
                        'id'=> $id
                    ]);
                } else if ($user->role=='Firm' || $request->role=='Firm') {
                    $profile = Firm::where('user_id',$id)->first();
                    if ($request->id && $user->role=='Admin'){
                        $userDetails = User::where('id',$id)->first();
                        $profile->name = $userDetails->name;
                        $profile->email = $userDetails->email;
                        $profile->nema = $userDetails->nema;
                    } else {
                        $profile->name = $user->name;
                        $profile->email = $user->email;
                        $profile->nema = $user->nema;
                    }
                    return response()->json([
                        'profile' => $profile,
                        'certificate' => $cert,
                        'id'=> $id
                    ]);
                }
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
     * Get user information
     */
    public function get(Request $request, $section){
        try{
            $user = $request->user();
            if ($user) {
                $profile = Individual::where('user_id',$user->id)->first();
                if ($section=='bio'){
                    return response()->json([
                        'bio' => $profile->bio,
                    ]);
                } else if ($section=='education'){
                    $education = Education::where('user_id',$user->id)->get();
                    return response()->json(['education'=>$education]);
                } else if ($section=='profession'){
                    $profession = Profession::where('user_id',$user->id)->get();
                    return response()->json(['profession'=>$profession]);
                } else if ($section=='files'){
                }
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
            $id = $user->id;
            if ($user) {
                $profile = Individual::where('user_id',$id)->first();
                if ($section=='bio'){
                    $profile->update($request->all());
                    return response()->json(['message' => 'Profile Bio updated successfully']);
                } else if ($section=='education'){
                    $educations = Education::where('user_id',$id)->get();
                    if($educations){
                        foreach ($educations as $education) {
                            $education->delete();
                        }
                        Education::create($request->education,$id);
                    } else {
                        Education::create($request->education,$id);
                    }
                    return response()->json([
                        'message' => 'Profile education updated successfully',
                        'education' => Education::where('user_id',$id)->get(),
                        'request' => $request->all()
                    ]);
                } else if ($section=='profession'){
                    $professions = Profession::where('user_id',$id)->get();
                    if($professions){
                        foreach ($professions as $profession) {
                            $profession->delete();
                        }
                        Profession::create($request->profession,$id);
                    } else {
                        Profession::create($request->profession,$id);
                    }
                    return response()->json(['message' => 'Profile experience updated successfully']);
                } else if ($section=='files'){
                }
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
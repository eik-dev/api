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
use App\Models\Certificates;

use App\Events\SaveLog;

/**
 * Handle user registration, login, and session management.
 */
class UserController extends Controller
{
    /**
     * RSVP for AGM
     */
    public function rsvp(Request $request)
    {
        $user = $request->user();
        if ($user->agm) {
            $user->agm->update(['rsvp' => true]);
        } else {
            $user->agm()->create(['rsvp' => true]);
        }
        return response()->json([
            'success' => true,
            'message' => 'Reservation successful'
        ]);
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
    
            //check if email exists
            if (!User::where('email',$credentials['email'])->exists()) {
                return response()->json(['error' => 'Account not found'], 401);
            }
    
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                //check if email is verified
                if ($user->role!='Admin' && !$user->email_verified_at) {
                    SaveLog::dispatch([
                        'name' => $user->name,
                        'email' => $user->email,
                        'action' => 'Failed login attempt'
                    ]);
                    return response()->json(['error' => 'Pending admin approval'], 401);
                }
                $token = $user->createToken('auth_token')->plainTextToken;
                
                SaveLog::dispatch([
                    'name' => $user->name,
                    'email' => $user->email,
                    'action' => 'Successful login'
                ]);
    
                return response()->json([
                    'user' => [
                        'role' => $user->role,
                        'token'=> $token,
                    ],
                ]);
            }
    
            return response()->json(['error' => 'Invalid credentials'], 401);
        } 
        catch (\RuntimeException $e) {
            if ($e->getMessage() === 'This password does not use the Bcrypt algorithm.') {
                //check if password==md5($request->password)
                $user = User::where('email',$credentials['email'])->first();
                if ($user && $user->password==md5($credentials['password'])) {
                    //update password to bcrypt
                    $user->password = bcrypt($credentials['password']);
                    $user->save();
                    //login user
                    Auth::login($user);
                    $token = $user->createToken('auth_token')->plainTextToken;
                    return response()->json([
                        'user' => [
                            'role' => $user->role,
                            'token'=> $token,
                        ],
                    ]);
                } else return response()->json(['error' => 'Password not recognized, recover password'], 401);
                // return response()->json(['error' => 'Recover password'], 401);
            }
        } 
        catch (\Exception $e) {
            return response()->json([ 'error' => $e->getMessage() ],401);
        }
    }

    /**
     * Get the authenticated user.
     */
    public function show(Request $request)
    {
        $user = $request->user()->load('agm'); // Get the authenticated user
        $photo = Files::where('user_id',$user->id)->where('folder','profile')->first();
        $certificate = Certificates::where('user_id',$user->id)
        ->where('year', date('Y'))
        ->first();
        if($certificate) $isActive = $certificate->verified!=null?true:false;
        else $isActive = false;
        $points = 0;

        if ($user) {
            return response()->json([
                'user' => [
                    'role' => $user->role,
                    'name' => $user->name,
                    'photo' => $photo ? $photo->url : null,
                    'active' => $isActive,
                    'points' => $points,
                    'RSVP' => $user->agm ? true : false,
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
                'practicing' => 'required',
                'email' => 'required|email',
                'password' => 'required',
                'username' => '',
                'nema' => '',
            ]);

            User::create($credentials);
            SaveLog::dispatch([
                'name' => $request->name,
                'email' => $request->email,
                'action' => 'New user created'
            ]);
            //create email and password credentials to authenticate user
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                if ($request->role=='Individual') {
                    Individual::create($request->profile,$user->id,$request->education,$request->profession);
                } else if ($request->role=='Firm') {
                    Firm::create($request->profile, $user->id);
                }
                //add number to user
                User::where('id',$user->id)->update(['number'=>Certificates::generateNumber($request->profile['category'],$user->id)]);
    
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
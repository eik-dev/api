<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Individual;
use App\Models\Firm;
use App\Models\Files;
use App\Models\Certificates;
use App\Http\Controllers\EmailController;

//populates dashboard with stats
class AdminController extends Controller
{
    /**
     * Read an admin based on id
     */
    public function show(Request $request){
        $user = $request->user();
        if ($user) {
            if ($user->role=='Admin') {
                $admin = User::find($request->id);
                return response()->json($admin);
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
     * Add new admins
     */
    public function store(Request $request){
        $user = $request->user();
        if ($user) {
            if ($user->role=='Admin') {
                $admin = User::create([
                    'name' => $request->fullName,
                    'username' => $request->username,
                    'role' => 'Admin',
                    'email' => $request->email,
                    'password' => '@Admin123',
                ]);
                EmailController::sendNewAdminEmail($request->email);
                return response()->json([
                    'message' => 'Admin added successfully',
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
     * Modify existing admin
     */
    public function update(Request $request){
        $user = $request->user();
        if ($user) {
            if ($user->role=='Admin') {
                $admin = User::find($request->id);
                $admin->name = $request->fullName;
                $admin->username = $request->username;
                $admin->email = $request->email;
                $admin->save();
                return response()->json([
                    'message' => 'Admin updated successfully',
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
     * Delete admin
     */
    public function destroy(Request $request){
        $user = $request->user();
        if ($user) {
            if ($user->role=='Admin') {
                $admin = User::find($request->id);
                $admin->delete();
                return response()->json([
                    'message' => 'Admin deleted successfully',
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
     * Get all members
     */
    public function members(Request $request){
        try{
            $user = $request->user();
            if ($user) {
                if ($user->role=='Admin') {
                    $members = $request->id?
                    User::where('id', $request->id)
                    ->with('certificates:user_id,number')
                    ->with('individual:user_id,category,firm,alternate,nationality,nationalID,postal,town,county,kra,phone')
                    ->get()
                    :
                    User::where('role', 'Individual')
                    ->with('certificates:user_id,number')
                    ->orderByDesc('id')
                    ->take($request->limit)
                    ->whereAny(['name','email','nema'],'LIKE' , '%'.$request->search.'%')
                    ->get();
                    return response()->json($members);
                } else if ($user->role=='Firm'){
                    $members = User::where('role', 'Individual')
                    ->with('certificates:user_id,number')
                    ->with(['individual' => function ($query) use ($user) {
                        $kra = Firm::where('user_id', $user->id)->first()->kra;
                        $query->where('firm', $kra);
                    }, 'individual:user_id,category,firm,alternate'])
                    ->orderByDesc('id')
                    ->take($request->limit)
                    ->whereAny(['name'],'LIKE' , '%'.$request->search.'%')
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
                    'role' => $user->role
                ], 401);
            }
        }
        catch (\Exception $e) {
            return response()->json([ 'error' => $e->getMessage() ],401);
        }
    }

    /**
     * Update member details
     */
    public function updateMember(Request $request){
        try{
            $user = $request->user();
            if ($user) {
                $member = $user->role=='Admin'?User::find($request->id):$user;
                $member->name = $request->fullName;
                $member->email = $request->email;
                $member->nema = $request->nema;
                $member->save();
                $individual = $user->role=='Admin'?Individual::where('user_id', $request->id)->first():Individual::where('user_id', $user->id)->first();
                $individual->category = $request->individual['category'];
                $individual->firm = $request->individual['firm'];
                $individual->alternate = $request->individual['alternate'];
                $individual->nationality = $request->individual['nationality'];
                $individual->nationalID = $request->individual['nationalID'];
                $individual->postal = $request->individual['postal'];
                $individual->town = $request->individual['town'];
                $individual->county = $request->individual['county'];
                $individual->kra = $request->individual['kra'];
                $individual->phone = $request->individual['phone'];
                $individual->save();
                return response()->json([
                    'message' => 'Member updated successfully',
                ]);
            } else {
                return response()->json([
                    'error' => 'Unauthorized',
                ], 401);
            }
        }
        catch (\Exception $e) {
            return response()->json([ 'error' => $e->getMessage(), 'request' => $request->all() ],401);
        }
    }

    /**
     * Delete member
     */
    public function deleteMember(Request $request){
        try{
            $user = $request->user();
            if ($user) {
                if ($user->role=='Admin') {
                    $individual = Individual::where('user_id', $request->id)->first();
                    if ($individual) $individual->delete();
                    $certificates = Certificates::where('user_id', $request->id)->first();
                    if ($certificates) $certificates->delete();
                    $files = Files::where('user_id', $request->id)->get();
                    foreach ($files as $file) {
                        $file->delete();
                    }
                    $member = User::find($request->id);
                    $email = $member->email;
                    $member->delete();
                    //delete folder with name user id from public/uploads
                    $path = public_path('uploads/'.$request->id);
                    // // if (is_dir($path)) {
                    // //     $files = scandir($path);
                    // //     foreach ($files as $file) {
                    // //         if (is_file($path.'/'.$file)) {
                    // //             unlink($path.'/'.$file);
                    // //         }
                    // //     }
                    // //     rmdir($path);
                    // // }
                    EmailController::sendDeleteUserEmail($email);
                    return response()->json([
                        'message' => 'Member deleted successfully',
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
                        EmailController::sendVerifyUserEmail($member->email);
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
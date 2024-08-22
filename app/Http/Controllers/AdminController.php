<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Individual;
use App\Models\Firm;
use App\Models\Files;
use App\Models\Education;
use App\Models\Profession;
use App\Models\Certificates;
use App\Models\Logs;
use App\Models\Mpesa;
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
                $admins = User::where('role', 'Admin')
                ->orderByDesc('id')
                ->take($request->limit)
                ->whereAny(['name','email','nema'],'LIKE' , '%'.$request->search.'%')
                ->get();
                if($request->count){
                    $count = User::where('role', 'Admin')->count();
                    return response()->json([
                        'admins' => $admins,
                        'count' => $count,
                    ]);
                }
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
                    $members =  User::where('role', 'Individual')
                    ->whereAny(['name','email','nema','number'],'LIKE' , '%'.$request->search.'%')
                    ->skip($request->Genesis)
                    ->orderByDesc('id')
                    ->take($request->limit)
                    ->get();
                    if($request->count){
                        $count = User::where('role', 'Individual')->count();
                        return response()->json([
                            'members' => $members,
                            'count' => $count,
                        ]);
                    }
                    return response()->json($members);
                } else if ($user->role=='Firm'){
                    $members = User::where('role', 'Individual')
                    ->with('certificates:user_id,number')
                    ->whereHas('individual', function ($query) use ($user) {
                        $kra = Firm::where('user_id', $user->id)->first()->kra;
                        $query->where('firm', $kra);
                    })
                    ->orderByDesc('id')
                    ->take($request->limit)
                    ->whereAny(['name'],'LIKE' , '%'.$request->search.'%')
                    ->skip($request->Genesis)
                    ->get();
                    if($request->count){
                        $count = User::where('role', 'Individual')
                        ->whereHas('individual', function ($query) use ($user) {
                            $kra = Firm::where('user_id', $user->id)->first()->kra;
                            $query->where('firm', $kra);
                        })
                        ->count();
                        return response()->json([
                            'members' => $members,
                            'count' => $count,
                        ]);
                    }
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
     * Get a member
     */
    public function member(Request $request){
        try{
            $user = $request->user();
            if ($user) {
                $id = $user->role=='Admin'?$request->id:$user->id;
                $member = User::find($id);
                $individual = Individual::where('user_id', $id)->first();
                $certificates = Certificates::where('user_id', $id)->first();
                $files = Files::where('user_id', $id)->get();
                $photo = Files::where('user_id',$id)->where('folder','profile')->first();
                return response()->json([
                    'member' => $member,
                    'individual' => $individual,
                    'certificates' => $certificates,
                    'files' => $files,
                    'photo' => $photo ? $photo->url : null,
                ]);
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
     * Get a member
     */
    public function firm(Request $request){
        try{
            $user = $request->user();
            if ($user) {
                $id = $user->role=='Admin'?$request->id:$user->id;
                $member = User::find($id);
                $firm = Firm::where('user_id', $id)->first();
                $certificates = Certificates::where('user_id', $id)->first();
                $files = Files::where('user_id', $id)->get();
                $photo = Files::where('user_id',$id)->where('folder','profile')->first();
                return response()->json([
                    'member' => $member,
                    'firm' => $firm,
                    'certificates' => $certificates,
                    'files' => $files,
                    'photo' => $photo ? $photo->url : null,
                ]);
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
                $member->practicing = $request->practicing;
                $member->save();
                if($member->role=='Individual'){
                    $individual = Individual::where('user_id', $user->role=='Admin'?$request->id:$user->id)->first();
                    $individual->category = $user->role=='Admin'?$request->individual['category']:$individual->category;
                    $individual->firm = $request->individual['firm'];
                    $individual->alternate = $request->individual['alternate'];
                    $individual->nationality = $request->individual['nationality'];
                    $individual->nationalID = $request->individual['nationalID'];
                    $individual->postal = $request->individual['postal'];
                    $individual->town = $request->individual['town'];
                    $individual->county = $request->individual['county'];
                    $individual->phone = $request->individual['phone'];
                    $individual->save();
                } else if($member->role=='Firm'){
                    $firm = Firm::where('user_id', $user->role=='Admin'?$request->id:$user->id)->first();
                    $firm->kra = $request->firm['kra'];
                    $firm->nationality = $request->firm['nationality'];
                    $firm->alternate = $request->firm['alternate'];
                    $firm->postal = $request->firm['postal'];
                    $firm->town = $request->firm['town'];
                    $firm->county = $request->firm['county'];
                    $firm->phone = $request->firm['phone'];
                    $firm->save();
                } else{
                    return response()->json(['error' => 'Invalid Member category',], 401);
                }
                return response()->json([
                    'message' => 'Member updated successfully',
                ]);
            } else {
                return response()->json(['error' => 'Unauthorized',], 401);
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
                    $education = Education::where('user_id', $request->id)->get();
                    foreach ($education as $edu) {
                        $edu->delete();
                    }
                    $profession = Profession::where('user_id', $request->id)->get();
                    foreach ($profession as $prof) {
                        $prof->delete();
                    }
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
     * Delete firm
     */
    public function deleteFirm(Request $request){
        try{
            $user = $request->user();
            if ($user) {
                if ($user->role=='Admin') {
                    $firm = Firm::where('user_id', $request->id)->first();
                    if ($firm) $firm->delete();
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
                    EmailController::sendDeleteUserEmail($email);
                    return response()->json([
                        'message' => 'Firm deleted successfully',
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
                ->whereAny(['name','email','nema','number'],'LIKE' , '%'.$request->search.'%')
                ->skip($request->Genesis)
                ->with('firm:user_id,kra')
                ->orderByDesc('id')
                ->take($request->limit)
                ->get();
                if($request->count){
                    $count = User::where('role', 'Firm')->count();
                    return response()->json([
                        'firms' => $firms,
                        'count' => $count,
                    ]);
                }
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
    /**
     * Get all logs
     */
    public function logs(Request $request){
        try{
            $user = $request->user();
            if ($user) {
                if ($user->role=='Admin') {
                    $logs = Logs::orderByDesc('id')
                    ->take($request->limit)
                    ->whereAny(['user','email','action'],'LIKE' , '%'.$request->search.'%')
                    ->skip($request->Genesis)
                    ->get(['created_at', 'user', 'email', 'action']);
                    if($request->count){
                        $count = Logs::count();
                        return response()->json([
                            'logs' => $logs,
                            'count' => $count,
                        ]);
                    }
                    return response()->json($logs);
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
     * Get all payments
     */
    public function payments(Request $request){
        try{
            $user = $request->user();
            if ($user) {
                if ($user->role=='Admin') {
                    $payments = Mpesa::orderByDesc('id')
                    ->take($request->limit)
                    ->whereAny(['CheckoutRequestID','email','phone','amount'],'LIKE' , '%'.$request->search.'%')
                    ->skip($request->Genesis)
                    ->get(['ResultCode','amount', 'email', 'phone', 'MpesaReceiptNumber','created_at']);
                    if($request->count){
                        $count = Mpesa::count();
                        return response()->json([
                            'payments' => $payments,
                            'count' => $count,
                        ]);
                    }
                    return response()->json($payments);
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
}
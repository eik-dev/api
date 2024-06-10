<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Individual;
use App\Models\Firm;
use App\Models\Certificates;
use App\Events\SaveLog;

class CertificatesController extends Controller
{
    /**
     * List all requested certificates
     */
    public function index(Request $request)
    {
        try{
            $user = $request->user();
            if ($user->role == 'Admin') {
                $certs = Certificates::with([
                    'user:id,name,email,nema',
                ])
                ->skip($request->Genesis)
                ->orderByDesc('created_at')
                ->take($request->limit)
                ->whereAny(['number'],'LIKE' , '%'.$request->search.'%')
                ->get();
                if($request->count){
                    $count = Certificates::count();
                    return response()->json([
                        'count' => $count,
                        'certs'=> $certs
                    ]);
                }
                return response()->json($certs);
            } else {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        } catch (\Exception $e) {
            return response()->json([ 'error' => $e->getMessage() ],401);
        }
    }

    /**
     * Create a new certificate
     */
    public function store(Request $request){
        try{
            $user = $request->user();
            if ($user) {
                //EIK/<category>/<id>
                if($request->id && $user->role == 'Admin') {
                    $user = User::find($request->id);
                }
                if ($user->role == 'Individual') {
                    $category = Individual::where('user_id', $user->id)->first()->category;
                } else {
                    $category = Firm::where('user_id', $user->id)->first()->category;
                }
                $cert = Certificates::create($category, $user->id);
                SaveLog::dispatch([
                    'name' => $user->name,
                    'email' => $user->email,
                    'action' => 'New certificate request'
                ]);
                return response()->json([
                    'message'=>'Certificate requested',
                    'cert'=> $cert
                ]);
            } else {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1];
            if($errorCode == 1062){
                return response()->json(['error' => 'Existing print request'], 409);
            }
        }
        catch (\Exception $e) {
            return response()->json([ 'error' => $e->getMessage() ],401);
        }
    }

    public function validate(Request $request)
    {
        try{
            $user = $request->user();
            if ($user->role == 'Admin') {
                $cert = Certificates::find($request->id);
                if ($request->validate=='true'){
                    $cert->verified = now();
                    $cert->expiry = now()->addYear();
                } else {
                    $cert->verified = null;
                    $cert->expiry = null;
                }
                $cert->save();
                return response()->json([
                    'message' => $request->validate == 'true' ? 'Certificate validated' : 'Certificate invalidated',
                ]);
            } else {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        } catch (\Exception $e) {
            return response()->json([ 'error' => $e->getMessage() ],401);
        }
    }

    public function download(Request $request)
    {
        try{
            $cert = Certificates::with([
                'user:id,name,email',
            ])->find($request->id);
            $user = User::find($cert->user_id);
            if ($user->role=='Individual') {
                $cert->category = Individual::where('user_id', $user->id)->first()->category;
            } else {
                $cert->category = Firm::where('user_id', $user->id)->first()->category;
            }
            SaveLog::dispatch([
                'name' => $cert->user->name,
                'email' => $cert->user->email,
                'action' => 'Downloaded certificate'
            ]);
            return response()->json($cert);
        } catch (\Exception $e) {
            return response()->json([ 'error' => $e->getMessage() ],401);
        }
    }

    public function verify(Request $request)
    {
        try{
            $cert = Certificates::with([
                'user:id,name',
            ])->where('number', $request->id)->first();
            return response()->json($cert);
        } catch (\Exception $e) {
            return response()->json([ 'error' => $e->getMessage() ],401);
        }
    }

    public function destroy($id)
    {
        return response()->noContent();
    }

    public function delete(Request $request)
    {
        try{
            $user = $request->user();
            if ($user->role == 'Admin') {
                $cert = Certificates::find($request->id);
                $cert->delete();
                return response()->json([
                    'message' => 'Certificate deleted',
                ]);
            } else {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        } catch (\Exception $e) {
            return response()->json([ 'error' => $e->getMessage() ],401);
        }
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Individual;
use App\Models\Firm;
use App\Models\Certificates;

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
                ])->get();
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
                if ($user->role == 'Individual') {
                    $category = Individual::where('user_id', $user->id)->first()->category;
                } else {
                    $category = Firm::where('user_id', $user->id)->first()->category;
                }
                switch ($category) {
                    case 'Student':
                        $number = 'EIK/1/' . $user->id;
                        break;
                    case 'Associate':
                        $number = 'EIK/2/' . $user->id;
                        break;
                    case 'Fellow':
                        $number = 'EIK/3/' . $user->id;
                        break;
                    case 'Honorary':
                        $number = 'EIK/4/' . $user->id;
                        break;
                    case 'Affiliate':
                        $number = 'EIK/5/' . $user->id;
                        break;
                    case 'Lead':
                        $number = 'EIK/6/' . $user->id;
                        break;
                    case 'Corporate':
                        $number = 'EIK/7/' . $user->id;
                        break;
                    case 'Firms':
                        $number = 'EIK/8/' . $user->id;
                        break;
                    default:
                        $number = 'EIK/0/' . $user->id;
                        break;
                }
                $cert = Certificates::create($number, $user->id);
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
                'user:id,name',
            ])->find($request->id);
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
}
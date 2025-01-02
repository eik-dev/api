<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Individual;
use App\Models\Firm;
use App\Models\Certificates;
use App\Events\SaveLog;
use Barryvdh\DomPDF\Facade\Pdf;

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
                    'user:id,name,email',
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
                $year = $request->year?$request->year:date('Y');
                $cert = Certificates::create($category, $user->id, $year);
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
                } else {
                    $cert->verified = null;
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
            $year = $request->year?$request->year:date('Y');
            $background = public_path('system/members.jpeg');
            $cert = Certificates::with([
                'user:id,name,practicing,role,email',
            ])
            ->where('number', $request->id)
            ->where('year', $year)
            ->first();
            $qrData = 'https://portal.eik.co.ke/verify?id=' . $cert->number;
            if ($cert->user->role == 'Individual') {
                $category = Individual::where('user_id', $cert->user->id)->first()->category;
            } else {
                $category = Firm::where('user_id', $cert->user->id)->first()->category;
            }
            $name = strtoupper($cert->user->name);
            $date = $cert->verified;
            $practicing = $cert->user->practicing ? 'practicing' : 'non-practicing';
            $info = "Is a {$practicing} {$category} member of Environment Institute of Kenya An Institute Founded in the year 2014 to extend and disseminate Environment knowledge and promote the practical application for public good.";
            $number = $cert->number;
            $pdf = Pdf::loadView('certificates.members', compact(['background', 'name', 'number', 'qrData', 'info', 'date']));
            $pdf->render();
            SaveLog::dispatch([
                'name' => $cert->user->name,
                'email' => $cert->user->email,
                'action' => 'Downloaded certificate'
            ]);
            return $pdf->stream($cert->user->name.'.pdf');
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
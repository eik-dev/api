<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Logs;
use App\Models\Mpesa;
use App\Models\Individual;
use App\Models\Firm;
use App\Models\User;

//populates dashboard with stats
class StatsController extends Controller
{
    public function summary(Request $request)
    {
        $logins = Logs::whereAny(['action'],'LIKE' , '%Successful login%')
        ->count();
        $certificates = Logs::whereAny(['action'],'LIKE' , '%New certificate request%')
        ->count();
        $revenue = Mpesa::where('ResultCode', 0)
                    ->select(DB::raw('SUM(amount) as total_revenue'))
                    ->get()
                    ->first();
        return response()->json([
            'revenue' => [
                'quantity' => $revenue->total_revenue,
                'trend' => (bool)rand(0, 1),
                'rate' => rand(1, 100)
            ],
            'logins' => [
                'quantity' => $logins,
                'trend' => (bool)rand(0, 1),
                'rate' => rand(1, 100)
            ],
            'print' => [
                'quantity' => $certificates,
                'trend' => (bool)rand(0, 1),
                'rate' => rand(1, 100)
            ]
        ]);
    }

    public function revenue(Request $request)
    {
    }

    public function traffic(Request $request)
    {
    }

    public function certificate(Request $request)
    {
    }
    /**
     * Given a category return number of users in that category
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function category(Request $request)
    {
        if($request->category==''){
            return response()->json(User::count());
        }
        if($request->category=='Firm'){
            return response()->json(Firm::count());
        }else{
            return response()->json(Individual::where('category',$request->category)->count());
        }
    }
}
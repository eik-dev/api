<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Logs;

//populates dashboard with stats
class StatsController extends Controller
{
    public function summary(Request $request)
    {
        $logins = Logs::whereAny(['action'],'LIKE' , '%Successful login%')
        ->count();
        $certificates = Logs::whereAny(['action'],'LIKE' , '%New certificate request%')
        ->count();
        return response()->json([
            'revenue' => [
                'quantity' => number_format(rand(1000, 100000), 0, '.', ','),
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
}
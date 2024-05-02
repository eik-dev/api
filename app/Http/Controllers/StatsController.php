<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Logs;

//populates dashboard with stats
class StatsController extends Controller
{
    public function summary()
    {
        return response()->json([
            'revenue' => [
                'quantity' => number_format(rand(1000, 100000), 0, '.', ','),
                'trend' => (bool)rand(0, 1),
                'rate' => rand(1, 100)
            ],
            'logins' => [
                'quantity' => rand(10, 100),
                'trend' => (bool)rand(0, 1),
                'rate' => rand(1, 100)
            ],
            'print' => [
                'quantity' => rand(10, 100),
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
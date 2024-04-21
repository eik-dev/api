<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/version', function () {
    return ['Laravel' => app()->version()];
});

Route::get('/summary', function () {
    return response()->json([
        'revenue' => [
            'quantity' => '78,358',
            'trend' => true,
            'rate' => 8.5
        ],
        'logins' => [
            'quantity' => 38,
            'trend' => false,
            'rate' => 12.5
        ],
        'print' => [
            'quantity' => 23,
            'trend' => true,
            'rate' => 38.5
        ]
    ]);
});

Route::get('/verify', function (Request $request) {
    $id = $request->query('id');
    return response()->json([
        'name' => 'frida mutui nyuvi',
        'member' => 'EIK/1/4247',
        'date' => '08/04/2024',
        'id' => $id
    ]);
});

Route::get('/download/certificate', function (Request $request) {
    $id = $request->query('id');
    return response()->json([
        'name' => 'frida mutui nyuvi',
        'member' => 'EIK/1/4247',
        'date' => '08/04/2024',
        'id' => $id
    ]);
});
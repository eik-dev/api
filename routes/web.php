<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});
Route::get('/certificates/member', function () {
    $background = asset('system/members.jpeg');
    return view('certificates.members', compact(['background']));
});
Route::get('/certificates/training', function () {
    $name = 'Jane Doe';
    $number = 'EIK/01/24/1234';
    $qrData = 'https://portal.eik.co.ke/verify?training='.'1'.'&id='.'EIK/1/7';
    $background = asset('system/training.jpg');
    return view('certificates.training', compact(['background', 'name', 'number','qrData']));
});

require __DIR__.'/auth.php';

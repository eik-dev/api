<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});
Route::get('/certificates/member', function () {
    $qrCodeURL = '#';
    $background = asset('system/members.jpeg');
    return view('certificates.members', compact(['background','qrCodeURL']));
  });

require __DIR__.'/auth.php';

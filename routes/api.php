<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\StatsController;

Route::get('/user', [UserController::class, 'show']);

Route::get('/version', function () {
    return ['Laravel' => app()->version()];
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

Route::get('/books', [BookController::class, 'index']);
Route::post('/books', [BookController::class, 'store']);
Route::get('/books/{id}', [BookController::class, 'show']);
Route::put('/books/{id}', [BookController::class, 'update']);
Route::delete('/books/{id}', [BookController::class, 'destroy']);

Route::post('/register', [UserController::class, 'onboard']);

Route::get('/files', [FileController::class, 'index']);
Route::post('/files/{destination}', [FileController::class, 'store']);

Route::get('/summary', [StatsController::class, 'summary']);

Route::post('/login', [UserController::class, 'store']);
Route::get('/logout', [UserController::class, 'destroy']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'show']);
});
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LoginController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\StatsController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});


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

Route::post('/login', [LoginController::class, 'store']);

Route::get('/files', [FileController::class, 'index']);
Route::post('/files/{destination}', [FileController::class, 'store']);

Route::get('/summary', [StatsController::class, 'summary']);
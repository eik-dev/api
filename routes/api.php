<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\FirmController;
use App\Http\Controllers\CertificatesController;
use App\Http\Controllers\ResetController;

Route::get('/user', [UserController::class, 'show']);

Route::get('/version', function () {
    return ['Laravel' => app()->version()];
});

Route::get('/books', [BookController::class, 'index']);
Route::post('/books', [BookController::class, 'store']);
Route::get('/books/{id}', [BookController::class, 'show']);
Route::put('/books/{id}', [BookController::class, 'update']);
Route::delete('/books/{id}', [BookController::class, 'destroy']);

Route::post('/register', [UserController::class, 'onboard']);
Route::post('/login', [UserController::class, 'store']);
Route::get('/recover', [ResetController::class, 'store']);
Route::get('/recover/{token}', [ResetController::class, 'show']);
Route::post('/recover', [ResetController::class, 'update']);
Route::get('/certificate/download', [CertificatesController::class, 'download']);
Route::get('/certificate/verify', [CertificatesController::class, 'verify']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'show']);
    Route::get('/logout', [UserController::class, 'destroy']);
    //profile related routes
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::get('/profile/{id}', [ProfileController::class, 'show']);
    Route::post('/profile/edit/{section}', [ProfileController::class, 'update']);
    //files related routes
    Route::post('/files/{folder}', [FileController::class, 'store']);
    Route::get('/files/{folder}', [FileController::class, 'show']);
    Route::get('/file/delete/{folder}', [FileController::class, 'destroy']);
    //admin related routes
    Route::get('/summary', [StatsController::class, 'summary']);
    Route::get('/admins', [AdminController::class, 'index']);
    Route::post('/admin/add', [AdminController::class, 'store']);
    Route::post('/admin/modify', [AdminController::class, 'update']);
    Route::get('/admin/read', [AdminController::class, 'show']);
    Route::get('/admin/delete', [AdminController::class, 'destroy']);
    Route::get('/admin/members', [AdminController::class, 'members']);
    Route::post('/admin/members', [AdminController::class, 'updateMember']);
    Route::get('/admin/member/delete', [AdminController::class, 'deleteMember']);
    Route::get('/admin/firms', [AdminController::class, 'firms']);
    Route::get('/user/verify', [AdminController::class, 'verify']);
    //firm related routes
    Route::post('/firm/members', [FirmController::class, 'members']);
    //certificate related routes
    Route::get('/certificates', [CertificatesController::class, 'index']);
    Route::get('/request', [CertificatesController::class, 'store']);
    Route::get('/certificate/validate', [CertificatesController::class, 'validate']);
});
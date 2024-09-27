<?php

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
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PDFcontroller;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\PeerConnectionController;
use App\Http\Controllers\TWGsController;

Route::post('/connect', [PeerConnectionController::class, 'connect']);

Route::get('/user', [UserController::class, 'show']);
Route::post('/pay/mpesa', [PaymentController::class, 'mpesaSTK']);
Route::get('/mpesa/callback', [PaymentController::class, 'logCallback']);
Route::post('/mpesa/mpesaCallback', [PaymentController::class, 'mpesaCallback']);

Route::get('/version', function () {
    return ['Laravel' => app()->version()];
});

// test
Route::get('/generate', [PDFcontroller::class, 'generate']);
Route::get('/test', [TestController::class, 'index']);

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
    Route::get('/profile/get/{section}', [ProfileController::class, 'get']);
    Route::post(uri: '/profile/edit/{section}', action: [ProfileController::class, 'update']);
    //files related routes
    Route::post('/csv', [FileController::class, 'csv']);
    Route::post('/files/{folder}', [FileController::class, 'store']);
    Route::get('/files/{folder}', [FileController::class, 'show']);
    Route::get('/file/delete/{folder}', [FileController::class, 'destroy']);
    //admin related routes
    Route::get('/summary', [StatsController::class, 'summary']);
    Route::get('/stats/members', [StatsController::class, 'category']);
    Route::get('/admins', [AdminController::class, 'index']);
    Route::post('/admin/add', [AdminController::class, 'store']);
    Route::post('/admin/modify', [AdminController::class, 'update']);
    Route::get('/admin/read', [AdminController::class, 'show']);
    Route::get('/admin/delete', [AdminController::class, 'destroy']);
    Route::get('/admin/members', [AdminController::class, 'members']);
    Route::get('/admin/member', [AdminController::class, 'member']);
    Route::get('/admin/firm', [AdminController::class, 'firm']);
    Route::post('/admin/members', [AdminController::class, 'updateMember']);
    Route::get('/admin/member/delete', [AdminController::class, 'deleteMember']);
    Route::get('/admin/firm/delete', [AdminController::class, 'deleteFirm']);
    Route::get('/admin/firms', [AdminController::class, 'firms']);
    Route::get('/user/verify', [AdminController::class, 'verify']);
    Route::get('/logs', [AdminController::class, 'logs']);
    Route::get('/payments', [AdminController::class, 'payments']);
    //firm related routes
    Route::post('/firm/members', [FirmController::class, 'members']);
    //certificate related routes
    Route::get('/certificates', [CertificatesController::class, 'index']);
    Route::get('/request', [CertificatesController::class, 'store']);
    Route::get('/certificate/validate', [CertificatesController::class, 'validate']);
    Route::get('/certificate/delete', [CertificatesController::class, 'delete']);
    //TWGs related routes
    Route::get('/twg/index', [TWGsController::class, 'index']);
    Route::get('/twg/join', [TWGsController::class, 'join']);
    Route::get('/twg/exit', [TWGsController::class, 'exit']);
    //training related routes
    Route::get('/training/all', [TrainingController::class, 'index']);
    Route::get('/training/attended', [TrainingController::class, 'attended']);
    Route::get('/training/attendee', [TrainingController::class, 'attendee']);
    Route::post('/training/create', [TrainingController::class, 'store']);
    Route::post('/training/members', [TrainingController::class, 'register']);
    Route::post('/training/member/add', [TrainingController::class, 'registerUser']);
    Route::post('/training/member/edit', [TrainingController::class, 'editUser']);
    Route::get('/training/download', [TrainingController::class, 'download']);
    Route::get('/training/email', [TrainingController::class, 'send']);
    Route::get('/training/cart', [TrainingController::class, 'cart']);
});
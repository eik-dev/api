<?php

use Illuminate\Support\Facades\Route;
use App\Models\AllTrainings;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});
Route::get('/view/certificates/member', function () {
    $background = asset('system/members.jpeg');
    return view('certificates.members', compact(['background']));
});
Route::get('/view/certificates/training/{id}', function ($id) {
    $training = AllTrainings::findOrFail($id);
    $name = 'Jane Doe';
    $number = 'EIK/01/24/1234';
    $qrData = 'https://portal.eik.co.ke/verify?training='.'1'.'&id='.'EIK/1/7';
    $info = $training->Info;
    $StartDate = new DateTime($training->StartDate);
    $date = 'Date '. $StartDate->format('jS F Y');
    $background = asset($training->Background);
    return view($training->View, compact(['background', 'name', 'number','qrData', 'info', 'date']));
    // return response()->json([
    //     'data'=>compact(['background', 'name', 'number','qrData', 'info', 'date'])
    // ]);
});

require __DIR__.'/auth.php';

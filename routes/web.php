<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\AllTrainings;
use App\Models\Certificates;
use App\Models\Individual;
use App\Models\Firm;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});
Route::get('/view/certificates/member', function (Request $request) {
    $background = asset('system/members.jpeg');
    $cert = Certificates::with([
        'user:id,name,practicing,role',
    ])->where('number', $request->id)->first();
    $qrData = 'https://portal.eik.co.ke/verify?id=' . $cert->number;
    if ($cert->user->role == 'Individual') {
        $category = Individual::where('user_id', $cert->user->id)->first()->category;
    } else {
        $category = Firm::where('user_id', $cert->user->id)->first()->category;
    }
    $name = strtoupper($cert->user->name);
    $date = $cert->verified;
    $practicing = $cert->user->practicing ? 'practicing' : 'non-practicing';
    $info = "Is a {$practicing} {$category} member of Environment Institute of Kenya An Institute Founded in the year 2014 to extend and disseminate Environment knowledge and promote the practical application for public good.";
    $number = $cert->number;
    return view('certificates.members', compact(['background', 'name', 'number', 'qrData', 'info', 'date']));
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
});

require __DIR__.'/auth.php';

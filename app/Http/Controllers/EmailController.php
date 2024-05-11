<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\recover;

class EmailController extends Controller
{
    public function sendRecoveryEmail($email, $token)
    {
        Mail::to($email)->send(new recover($token));
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use App\Mail\recover;
use App\Mail\NewAdmin;
use App\Mail\DeleteUser;

class EmailController extends Controller
{
    public static function sendRecoveryEmail($email, $token)
    {
        Mail::to($email)->send(new recover($token));
    }

    public static function sendNewAdminEmail($email)
    {
        Mail::to($email)->send(new NewAdmin());
    }

    public static function sendDeleteUserEmail($email)
    {
        Mail::to($email)->send(new DeleteUser());
    }
}

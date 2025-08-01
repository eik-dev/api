<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use App\Mail\recover;
use App\Mail\NewAdmin;
use App\Mail\DeleteUser;
use App\Mail\VerifyUser;
use App\Mail\Receipt;
use App\Mail\SendCertificate;
use App\Mail\SendConferenceCertificate;
use App\Mail\AdminNotification;

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
    public static function sendVerifyUserEmail($email)
    {
        Mail::to($email)->send(new VerifyUser());
    }
    public static function sendReceiptEmail($email, $payload)
    {
        Mail::to($email)->send(new Receipt($payload));
    }
    public static function sendCertificate($email, $pdf, $payload)
    {
        Mail::to($email)->send(new SendCertificate($pdf, $payload));
    }
    public static function sendConferenceCertificate($email, $pdf, $payload)
    {
        Mail::to($email)->send(new SendConferenceCertificate($pdf, $payload));
    }

    public static function sendAdminNotification($number)
    {
        Mail::to('info@eik.co.ke')->send(new AdminNotification($number));
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\EmailController;

class ActionsController extends Controller
{
    //
    public function triggerNotification(Request $request)
    {
        $user = auth()->user();
        logger("Notification request from :: " . $user->number);
        EmailController::sendAdminNotification($user->number);
        return response()->json([
            'success' => true,
            'message'=> 'Notification sent'
        ]);
    }
}

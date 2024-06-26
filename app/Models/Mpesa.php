<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mpesa extends Model
{
    use HasFactory;
    protected $fillable = [
        'phone',
        'amount',
        'email',
        'AccountReference',
        'ResultCode',
        'ResultDesc',
        'CheckoutRequestID',
        'MpesaReceiptNumber'
    ];
    public static function create($input)
    {
        $mpesa = new Mpesa();
        $mpesa->phone = $input['phone'];
        $mpesa->amount = $input['amount'];
        $mpesa->email = $input['email'];
        $mpesa->AccountReference = $input['AccountReference'];
        $mpesa->CheckoutRequestID = $input['CheckoutRequestID'];
        $mpesa->save();
    }
}

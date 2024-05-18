<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class PaymentController extends Controller
{
    public function mpesaSTK(Request $request)
    {
        $timestamp = date('YmdHis');
        $shortcode = 284864;
        $passkey = '61cf92bff3cdbe482b9f7d736268c93a765917631b1b5729e6942ed07f8eb468';
        $password = base64_encode($shortcode . $passkey . $timestamp);
        $contact = '254' . ltrim($request->phone, '+2540');

        $credentials = [
            "ConsumerKey" => '87uccYKQ2s4V8D1jWXSFPdMtn9lvIWyv22iR7k2cHFVObOXc',
            "ConsumerSecret" => 'q7DQlTo40t1ZLjPbMoRINOZLPUX896QvGf7rxXvLhwepTp0vhrlpBLIkAbi4VrRr',
        ];


        $client = new Client();

        try {
            $response = $client->request('GET', 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials', [
                'auth' => [$credentials['ConsumerKey'], $credentials['ConsumerSecret']]
            ]);

            $token = json_decode($response->getBody())->access_token;

            $response = $client->request('POST', 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token
                ],
                'json' => [
                    "BusinessShortCode" => $shortcode,
                    "Password" => $password,
                    "Timestamp" => $timestamp,
                    "TransactionType" => "CustomerPayBillOnline",
                    "Amount" => $request->amount,
                    "PartyA" => $contact,
                    "PartyB" => $shortcode,
                    "PhoneNumber" => $contact,
                    "CallBackURL" => "https://2420-197-248-74-74.ngrok-free.app/callback",
                    "AccountReference" => "Registration Fee",
                    "TransactionDesc" => "Payment"
                ]
            ]);
            return json_decode($response->getBody());
        } catch (\Exception $e) {
            return response()->json(([
                'error'=>$e->getMessage(),
            ]));
        }
    }
    public function logCallback(Request $request)
    {
        Log::info($request->all());
    }
}

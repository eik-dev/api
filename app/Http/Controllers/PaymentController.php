<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

use App\Models\Mpesa;

class PaymentController extends Controller
{
    public $shortcode = 284864;
    public $passkey = '61cf92bff3cdbe482b9f7d736268c93a765917631b1b5729e6942ed07f8eb468';
    public function getToken(){
        $credentials = [
            "ConsumerKey" => '87uccYKQ2s4V8D1jWXSFPdMtn9lvIWyv22iR7k2cHFVObOXc',
            "ConsumerSecret" => 'q7DQlTo40t1ZLjPbMoRINOZLPUX896QvGf7rxXvLhwepTp0vhrlpBLIkAbi4VrRr',
        ];

        $client = new Client();

        try {
            $response = $client->request('GET', 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials', [
                'auth' => [$credentials['ConsumerKey'], $credentials['ConsumerSecret']]
            ]);

            return json_decode($response->getBody())->access_token;
        } catch (\Exception $e) {
            return response()->json(([
                'error'=>$e->getMessage(),
            ]));
        }
    }
    public function mpesaSTK(Request $request)
    {
        $timestamp = date('YmdHis');
        $password = base64_encode($this->shortcode . $this->passkey . $timestamp);
        $contact = '254' . ltrim($request->phone, '+2540');

        try {
            $client = new Client();
            $token = $this->getToken();

            $response = $client->request('POST', 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token
                ],
                'json' => [
                    "BusinessShortCode" => $this->shortcode,
                    "Password" => $password,
                    "Timestamp" => $timestamp,
                    "TransactionType" => "CustomerPayBillOnline",
                    "Amount" => 5,
                    "PartyA" => $contact,
                    "PartyB" => $this->shortcode,
                    "PhoneNumber" => $contact,
                    "CallBackURL" => "https://2420-197-248-74-74.ngrok-free.app/callback",
                    "AccountReference" => "Registration Fee",
                    "TransactionDesc" => "Payment"
                ]
            ]);
            $response = json_decode($response->getBody());
            if ($response->ResponseCode=="0"){
                Mpesa::create([
                    'phone' => $contact,
                    'amount' => 5,
                    'AccountReference' => 'Registration Fee',
                    'CheckoutRequestID' => $response->CheckoutRequestID
                ]);
            }
            return $response;
        } catch (\Exception $e) {
            return response()->json(([
                'error'=>$e->getMessage(),
            ]));
        }
    }
    public function logCallback(Request $request)
    {
        $timestamp = date('YmdHis');
        $password = base64_encode($this->shortcode . $this->passkey . $timestamp);
        $token = $this->getToken();

        $ch = curl_init('https:/api.safaricom.co.ke/mpesa/stkpushquery/v1/query');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array(
            "BusinessShortCode"=> $this->shortcode,
            "Password"=> $password,
            "Timestamp"=> $timestamp,
            "CheckoutRequestID"=> $request->CheckoutRequestID
        )));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = json_decode(curl_exec($ch));
        curl_close($ch);
        if (property_exists($response, 'ResponseCode') && $response->ResponseCode == "0"){
            Mpesa::where('CheckoutRequestID', $request->CheckoutRequestID)->update([
                'ResultCode' => $response->ResultCode,
                'ResultDesc' => $response->ResultDesc
            ]);
            if ($response->ResultCode == "0") {
                return response()->json([
                    'message' => 'Successfuly recorded payment',
                    'ResponseCode' => '0',
                ]);
            } else if ($response->ResultCode != "0") {
                return response()->json([
                    'error' => $response->ResultDesc,
                    'ResponseCode' => '0',
                ]);
            }
        }
        else{
            return response()->json([
                'requestId' => $response->requestId
            ]);
        }
    }
}

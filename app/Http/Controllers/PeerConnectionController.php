<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PeerConnectionController extends Controller
{
    public function connect(Request $request)
    {
        // Logic for signaling between peers
        return response()->json([
            'peerId' => $request->input('peerId'), // Example, you can handle this based on your logic
        ]);
    }
}

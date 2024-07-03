<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Logs;
use Barryvdh\DomPDF\Facade\Pdf;
use chillerlan\QRCode\{QRCode, QROptions};

class PDFcontroller extends Controller 
{
    public function index(Request $request)
    {
    }

    public function generate(Request $request)
    {
        $background = public_path('/system/training.jpg');
        $pdf = Pdf::loadView('certificates.training', compact(['background']));
        return $pdf->download('EIK.pdf');
    }
}
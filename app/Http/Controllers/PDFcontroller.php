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
    public function QRgenerator(){
        // Define data for the QR code
        $data = 'https://www.example.com';

        // Create QR code options
        $options = new QROptions;
        $options->scale = 20;

        // Generate QR code object
        $qrcode = new QRCode($options);

        // Get QR code image data as a string
        $qrCodeImage = $qrcode->render('png');

        // Output the image data (e.g., save to file)
        file_put_contents('qr_code.png', $qrCodeImage);

    }

    public function generate(Request $request)
    {
        $qrCodeURL = 'https://www.example.com'; 
        $background = public_path('/system/members.jpeg');
        $pdf = Pdf::loadView('certificates.members', compact(['qrCodeURL','background']));
        return $pdf->download('EIK.pdf');
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Training;
use App\Models\AllTrainings;

class TrainingController extends Controller
{
    /**
     * Get all trainings
     */
    public function index(Request $request)
    {
        return response()->json(['message' => 'Test controller']);
    }

    /**
     * Submit list of attendance
     */
    public function register(Request $request)
    {
        $training = $request->training;
        $startDate = AllTrainings::where('id', $training)->first()->Date;
        $month = date('m', strtotime($startDate));
        $year = substr(date('Y', strtotime($startDate)), -2);

        foreach ($request->data as $value) {
            $id = Training::latest('id')->first() ? Training::latest('id')->first()->id + 1 : 1;
            if (Training::where('Training', $training)->where('Email', $value['email'])->first()) continue;

            // Create a new Training record
            Training::create([
                'Training' => $training,
                'Email' => $value['email'],
                'Name' => $value['name'],
                'Number' => 'EIK/' . $year . '/' . $month . '/' . $id,
            ]);
        }
        $response = Training::where('Training',$training)->get(['Name','Email','Number']);
        return response()->json([
            'message'=>'success',
            'data'=>$response,
            'test'=>Training::latest('id')->first()
        ]);
    }

    /**
     * Genarete certificate numbers
     */
    public function generate(Request $request)
    {
        return response()->json(['message' => 'Test controller']);
    }

    /**
     * Download certificates
     */
    public function download(Request $request)
    {
        try{
            $certificate = Training::where('number',$request->number)->first();
            $training = AllTrainings::where('id',$certificate->Training)->first();
            if (!$certificate || !$training) throw new \Exception('Certificate not found');
            $name = $certificate->Name;
            $number = $certificate->Number;
            $qrData = 'https://portal.eik.co.ke/verify?training='.$certificate->Training.'&id='.$number;
            $background = public_path('/system/training.jpg');
            $pdf = Pdf::loadView('certificates.training', compact(['background','name','number','qrData']));
            $pdf->render();
            return $pdf->stream($name.'.pdf');
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'request'=>$request->all()
            ], 500);
        }
    }
}
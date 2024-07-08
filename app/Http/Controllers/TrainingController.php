<?php

namespace App\Http\Controllers;
use DateTime;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Training;
use App\Models\AllTrainings;
use App\Http\Controllers\EmailController;

class TrainingController extends Controller
{
    /**
     * Get all trainings
     */
    public function index(Request $request)
    {
        $trainings = AllTrainings::get();
        return response()->json([
            'message' => 'Success',
            'data' => $trainings
        ]);
    }
    /**
     * Create a new training
     */
    public function store(Request $request)
    {
        try{
            AllTrainings::create(
                $request->name,
                $request->start,
                $request->end,
                $request->info,
            );
            return response()->json(['message' => 'Succesfully created training']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'request'=>$request->all()
            ], 500);
        }
    }

    /**
     * Submit list of attendance
     */
    public function register(Request $request)
    {
        $training = $request->training;
        $startDate = AllTrainings::where('id', $training)->first()->StartDate;
        $month = date('m', strtotime($startDate));
        $year = substr(date('Y', strtotime($startDate)), -2);

        foreach ($request->data as $value) {
            $id = Training::latest('id')->first() ? Training::latest('id')->first()->id + 1 : 1;
            if (Training::where('Training', $training)->where('Email', $value['Email'])->first()) continue;

            // Create a new Training record
            Training::create([
                'Training' => $training,
                'Email' => $value['Email'],
                'Name' => $value['Name'],
                'Number' => 'EIK/' . $month . '/' . $year . '/' . $id,
            ]);
        }
        $response = Training::where('Training',$training)->get(['Name','Email','Number','Sent']);
        return response()->json([
            'message'=>'success',
            'data'=>$response,
            'test'=>$training
        ]);
    }

    /**
     * Download certificates
     */
    public function download(Request $request)
    {
        try{
            $certificate = Training::where('number',$request->number)->first();
            $training = AllTrainings::where('id',$request->training)->first();
            if (!$certificate || !$training) throw new \Exception('Certificate not found');
            $name = $certificate->Name;
            $number = $certificate->Number;
            $qrData = 'https://portal.eik.co.ke/verify?training='.$certificate->Training.'&id='.$number;
            $background = public_path($training->Background);
            $info = $training->Info;
            $StartDate = new DateTime($training->StartDate);
            $date = 'Date '. $StartDate->format('jS F Y');
            $pdf = Pdf::loadView('certificates.training', compact(['background', 'name', 'number','qrData', 'info', 'date']));
            $pdf->render();
            return $pdf->stream($name.'.pdf');
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'request'=>$request->all()
            ], 500);
        }
    }
    /**
     * Send certificate
     */
    public function send(Request $request)
    {
        try{
            $certificate = Training::where('number',$request->number)->first();

            // if($certificate->Sent) return response()->json(['message'=>'Email already sent']);
            
            $training = AllTrainings::where('id',$certificate->Training)->first();
            if (!$certificate || !$training) throw new \Exception('Certificate not found');
            $name = $certificate->Name;
            $number = $certificate->Number;
            $qrData = 'https://portal.eik.co.ke/verify?training='.$certificate->Training.'&id='.$number;
            $background = public_path($training->Background);
            $info = $training->Info;
            $StartDate = new DateTime($training->StartDate);
            $date = 'Date '. $StartDate->format('jS F Y');
            $pdf = Pdf::loadView('certificates.training', compact(['background', 'name', 'number','qrData', 'info', 'date']));
            $pdf->render();
            $pdfContent = $pdf->output();
            EmailController::sendCertificate($certificate->Email, $pdfContent, compact(['name','number']));
            $certificate->Sent = true;
            $certificate->save();
            return response()->json(['message'=>'success']);
        } catch (\Exception $e) {
            $certificate = Training::where('number',$request->number)->first();
            $certificate->Sent = false;
            $certificate->save();
            return response()->json([
                'error' => $e->getMessage(),
                'request'=>$request->all()
            ], 500);
        }
    }
}
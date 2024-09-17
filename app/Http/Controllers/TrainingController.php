<?php

namespace App\Http\Controllers;
use DateTime;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Training;
use App\Models\AllTrainings;
use App\Models\User;
use App\Models\Cart;
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
     * Get all trainings
     */
    public function cart(Request $request)
    {
        $trainings = AllTrainings::get();
        return response()->json([
            'message' => 'Success',
            'data' => $trainings
        ]);
    }

    /**
     * Get attended trainings for a user
     */
    public function attended(Request $request)
    {
        try{
            $user = $request->user();
            $id = ($request->id && $user->role=='Admin')?$request->id:$user->id;
            $user = User::where('id',$id)->first();
            if ($user) {
                $attendace = Training::where('Email',$user->email)->get();
                $trainings = [];
                foreach ($attendace as $value) {
                    $training = AllTrainings::where('id',$value->Training)->first();
                    $trainings[] = [
                        'Name' => $training->Name,
                        'Start' => $training->StartDate,
                        'End' => $training->EndDate,
                        'Info' => $training->Info,
                        'Number' => $value->Number,
                        'id' => $value->Training
                    ];
                }
                return response()->json($trainings);
            } else {
                return response()->json([
                    'error' => 'User not found',
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([ 
                'error' => $e->getMessage(),
            ], 401);
        }
    }

    /**
     * Get users for a training
     */
    public function attendee(Request $request)
    {
        try{
            $user = $request->user();
            if ($user) {
                $users = Training::where('Training',$request->id)->get();
                return response()->json($users);
            } else {
                return response()->json([
                    'error' => 'Unauthorized',
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([ 
                'error' => $e->getMessage(),
            ], 401);
        }
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
     * Add Individual to training
     */
    public function registerUser(Request $request)
    {
        try{
            $training = $request->training;
            $startDate = AllTrainings::where('id', $training)->first()->StartDate;
            $month = date('m', strtotime($startDate));
            $year = substr(date('Y', strtotime($startDate)), -2);
            $id = Training::latest('id')->first() ? Training::latest('id')->first()->id + 1 : 1;
            throw_if(Training::where('Training', $training)->where('Email', $request->email)->first(), "Email exists");
            // Create a new Training record
            Training::create([
                'Training' => $training,
                'Email' => $request->email,
                'Name' => $request->fullName,
                'Number' => 'EIK/' . $month . '/' . $year . '/' . $id,
            ]);

            $response = Training::where('Training',$training)
                                ->where('Email',$request->email)
                                ->get(['Name','Email','Number','Sent']);
            return response()->json([
                'message'=>'member added',
                'data'=>$response,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'request' => $request->all()
            ], 401);
        }
    }

     /**
     * Edit Individual info
     */
    public function editUser(Request $request)
    {
        try{
            $training = Training::where('Number', $request->number);
            $training->update([
                'Email' => $request->email,
                'Name' => $request->fullName,
            ]);

            return response()->json([
                'message'=>'member updated',
                'data'=>$training,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'request' => $request->all()
            ], 401);
        }
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

            if($certificate->Sent) return response()->json(['message'=>'Email already sent']);
            
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
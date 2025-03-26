<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\EmailController;
use App\Models\Conference;
use App\Models\ConferenceRoles;
use App\Models\AllConferences; 

class ConferenceController extends Controller
{
    /**
     * Get all conferneces
     */
    public function index(Request $request)
    {
        $conferences = AllConferences::get();
        return response()->json([
            'message' => 'Successfully fetched all conferences',
            'success' => true,
            'data' => $conferences
        ]);
    }

    /**
     * Get a confernece
     */
    public function conference(Request $request, AllConferences $conference)
    {
        $conference = $conference->with(['roles'])->find($conference->id);
        return response()->json([
            'message' => 'Successfully fetched conference',
            'success' => true,
            'data' => $conference
        ]);
    }

    /**
     * Update a confernece
     */
    public function updateConference(Request $request, AllConferences $conference)
    {
        $conference->update([
            'Name'=>$request->name,
            'StartDate'=>$request->start,
            'EndDate'=>$request->end,
        ]);
        return response()->json([
            'message' => 'Successfully updated conference',
            'success' => true,
            'data' => $conference
        ]);
    }

    /**
     * Get users for a confernece 
     */
    public function attendee(Request $request, $id)
    {
        try{
            logger(message: '$request->id');
            $users = Conference::where('conference_id',$request->id)
            ->with(['role'])
            ->get();

            return response()->json([
                'message' => 'Successfully fetched conference users',
                'success' => true,
                'data' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([ 
                'error' => $e->getMessage(),
            ], 401);
        }
    }

    /**
     * Create a new conference
     */
    public function store(Request $request)
    {
        try{
            $conference = AllConferences::create([
                'Name'=>$request->name,
                'StartDate'=>$request->start,
                'EndDate'=>$request->end,
            ]);
            return response()->json([
                'message' => 'Succesfully created conference',
                'data' => $conference,
                'success' => true,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'request'=>$request->all()
            ], 500);
        }
    }
    /**
     * Set conference roles
     */
    public function roles(Request $request, AllConferences $conference)
    {
        try{
            foreach ($request->roles as $role) {
                ConferenceRoles::firstOrCreate([
                  'conference_id'=>$conference->id,
                  'Name'=>$role,  
                ]);
            }
            return response()->json([
                'message' => 'Succesfully update roles',
                'success' => true,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'request'=>$request->all()
            ], 500);
        }
    }
    /**
     * get conference roles
     */
    public function getRoles(Request $request, AllConferences $conference)
    {
        try{
            $roles = ConferenceRoles::where('conference_id',$conference->id)->get();
            return response()->json([
                'message' => 'Succesfully fetched roles',
                'success' => true,
                'data' => $roles
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'request'=>$request->all()
            ], 500);
        }
    }
    /**
     * update conference role
     */
    public function updateRole(Request $request, ConferenceRoles $conferenceRoles)
    {
        try{
            $file = $request->file('file');
            $destinationPath = public_path("system");
            $name = str_replace(' ', '_', $file->getClientOriginalName());
            $file->move($destinationPath, $name);

            $conferenceRoles->update([
                'Background'=>"/system/{$name}"
            ]);
            return response()->json([
                'message' => 'Succesfully updated role',
                'success' => true,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'request'=>$request->all()
            ], 500);
        }
    }
    /**
     * get all roles
     */
    public function getAllRoles(Request $request)
    {
        try{
            $roles = ConferenceRoles::get();
            return response()->json([
                'message' => 'Succesfully fetched roles',
                'success' => true,
                'data'=>$roles
            ]);
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
        foreach ($request->data as $value) {
            $role = ConferenceRoles::where('Name',$value['role']['Name'])->where('conference_id',$request->conference)->first();

            $conference = Conference::create([
                'conference_id'=>$request->conference,
                'role_id'=>$role->id,
                'Name'=>$value['Name'],
                'Email'=>$value['Email'],
            ]);
            
            $conference->update([
                'Number'=>"EIK/24/{$role->id}/{$conference->id}"
            ]);
        }

        $response = Conference::where('conference_id',$request->conference)
        ->with(['role'])
        ->get();

        return response()->json([
            'message'=>'successfully updated trainings',
            'success' => true,
            'data'=>$response,
        ]);
    }

    /**
     * Add Individual to training
     */
    public function registerUser(Request $request)
    {
        try{
            $conference = Conference::create([
              'conference_id'=>$request->conference,
              'role_id'=>$request->role,
              'Name'=>$request->fullName,
              'Email'=>$request->email,
            ]);
            
            $conference->update([
                'Number'=>"EIK/24/{$conference->role->id}/{$conference->id}"
            ]);

            return response()->json([
                'message'=>'member added',
                'success' => true,
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
    public function download(Request $request, $id)
    {
        try{
            $user = Conference::with(['role'])
            ->findOrFail($id);
            
            if (!$user) throw new \Exception('Certificate not found');

            $name = $user->Name;
            $number = $user->Number;
            $qrData = 'https://portal.eik.co.ke/verify?conference='.$user->conference_id.'&id='.$number;
            $background = public_path($user->role->Background);
            $info = '';
            $StartDate = '31st MM YYYY';
            $date = '';

            $pdf = Pdf::loadView('certificates.conference', compact(['background', 'name', 'number','qrData', 'info', 'date']));
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
    public function send(Request $request, $id)
    {
        try{
            $user = Conference::with(['role'])
            ->findOrFail($id);
            
            if (!$user) throw new \Exception('Certificate not found');

            if($user->Sent) return response()->json(['message'=>'Email already sent']);
            
            $name = $user->Name;
            $number = $user->Number;
            $qrData = 'https://portal.eik.co.ke/verify?conference='.$user->conference_id.'&id='.$number;
            $background = public_path($user->role->Background);
            $info = '';
            $StartDate = '31st MM YYYY';
            $date = '';

            $pdf = Pdf::loadView('certificates.conference', compact(['background', 'name', 'number','qrData', 'info', 'date']));

            $pdf->render();
            $pdfContent = $pdf->output();

            EmailController::sendConferenceCertificate($user->Email, $pdfContent, compact(['name','number']));
            
            $user->Sent = true;
            $user->save();

            return response()->json(['message'=>'success, email sent']);
        } catch (\Exception $e) {
            $user = Conference::findOrFail($id);
            $user->Sent = false;
            $user->save();

            return response()->json([
                'error' => $e->getMessage(),
                'request'=>$request->all()
            ], 500);
        }
    }
}

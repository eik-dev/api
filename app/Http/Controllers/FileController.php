<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\User;
use App\Models\Files;

class FileController extends Controller
{
    public function index()
    {
        return response()->noContent();
    }

    public function store(Request $request, $folder)
    {
        try{
            $user = $request->user();
            if ($user) {
                $id = $user->id;
                $file = $request->file('file');
                $destinationPath = public_path("uploads/$id/$folder");
                $name = $file->getClientOriginalName();
                $file->move($destinationPath, str_replace(' ', '_', $name));
                $url = url("uploads/$id/$folder/" . str_replace(' ', '_', $name));
                $record = Files::create([
                    'user_id' => $id,
                    'title' => $request->title,
                    'folder' => $folder,
                    'name' => $name,
                    'url' => $url,
                ]);
                return response()->json([
                    'url' => $record->url,
                ]);
            } else {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        } catch (\Exception $e) {
            return response()->json([ 'error' => $e->getMessage() ],401);
        }
    }

    public function csv(Request $request)
    {
        try{
            $user = $request->user();
            if($user->role=='Admin'){
                $file = $request->file('file');
                $spreadsheet = IOFactory::load($file);
                $worksheet = $spreadsheet->getActiveSheet();
                $data = $worksheet->toArray();
                //get index of name and email columns
                $nameIndex = array_search('Name', $data[0]);
                $emailIndex = array_search('Email', $data[0]);
                //return an array of only name and email
                $response = [];
                foreach ($data as $key => $value) {
                    if($value[$nameIndex]==null) break;
                    if ($key > 0) {
                        $member = User::where('email',$value[$emailIndex])->first();
                        $response[] = [
                            'name' => $value[$nameIndex],
                            'email' => $value[$emailIndex],
                            'number' => $member?$member->number:''
                        ];
                    }
                }
                return response()->json([
                    'data' => $response,
                    'message' => 'CSV file uploaded successfully',
                    'nameIndex' => $nameIndex,
                    'emailIndex' => $emailIndex,
                ]);
            } else {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        } catch (\Exception $e) {
            return response()->json([ 'error' => $e->getMessage() ],401);
        }
    }
    public function show(Request $request, $folder)
    {
        try{
            $user = $request->user();
            $id = ($request->id && $user->role=='Admin')?$request->id:$user->id;
            if($user){
                $files = Files::where('user_id', $id)->where('folder', $folder)->select('name', 'url', 'title')->get();
                return response()->json($files);
            } else {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        } catch (\Exception $e) {
            return response()->json([ 'error' => $e->getMessage() ],401);
        }
    }

    public function destroy(Request $request, $folder)
    {
        try{
            $user = $request->user();
            if($user){
                $id = $user->id;
                $record = Files::where('user_id', $id)->where('folder', $folder)->where('url', $request->url)->first();
                if ($record) {
                    $path = public_path("uploads/$id/$folder/" . str_replace(' ', '_', $record->name));
                    if (file_exists($path)) {
                        unlink($path);
                        $record->delete();
                        return response()->json(['message' => 'File deleted successfully']);
                    } else{
                        return response()->json(['error' => 'File not found'], 404);
                    }
                } else {
                    return response()->json(['error' => 'File not found'], 404);
                }
            } else {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        } catch (\Exception $e) {
            return response()->json([ 'error' => $e->getMessage() ],401);
        }
    }
}
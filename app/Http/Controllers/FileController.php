<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    public function show(Request $request, $folder)
    {
        try{
            $user = $request->user();
            if($user){
                $id = $user->id;
                $files = Files::where('user_id', $id)->where('folder', $folder)->select('name', 'url')->get();
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
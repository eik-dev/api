<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Files;

class FileController extends Controller
{
    public function index()
    {
        return response()->noContent();
    }

    public function store(Request $request, $destination)
    {
        return response()->noContent();
    }

    public function destroy($id)
    {
        return response()->noContent();
    }
}
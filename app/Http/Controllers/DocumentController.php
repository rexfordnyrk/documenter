<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    //
    public function show($filename)
    {
        //extract filename from URL and construct path
        $path = 'documents/' . $filename;

        //check if th file exists in storage and serve it
        if (Storage::exists($path)) {
            return response()->file(storage_path('app/' . $path));
        }

        // Handle file not found error
        abort(404);
    }
}

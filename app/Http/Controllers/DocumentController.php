<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    //
    public function show($filename)
    {
        $path = 'documents/' . $filename;

        if (Storage::exists($path)) {
            return response()->file(storage_path('app/' . $path));
        }

        // Handle file not found error
        abort(404);
    }
}

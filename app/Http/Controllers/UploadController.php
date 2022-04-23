<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return AuthController::customResponse(false, 'Validation Error', $validator->errors());
        }

        $file = $request->file('file');
        $name = time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('images'), $name);

        $fullPathUri = url('/images/' . $name);
        return AuthController::customResponse(true, 'Success Upload', $fullPathUri);
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|string',
        ]);

        if ($validator->fails()) {
            return AuthController::customResponse(false, 'Validation Error', $validator->errors());
        }

        $file = $request->file;
        $file = public_path('images/' . $file);
        if (file_exists($file)) {
            unlink($file);
        } else {
            return AuthController::customResponse(false, 'File not found', null);
        }

        return AuthController::customResponse(true, 'Success Delete', null);
    }
}

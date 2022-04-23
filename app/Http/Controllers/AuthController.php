<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public static function customResponse($success, $message, $data)
    {
        return response()->json([
            'success' => $success,
            'message' => $message,
            'data' => $data,
        ]);
    }

    public function register(Request $request)
    {
        $req = json_decode($request->getContent(), true);
        $validator = Validator::make($req, [
            'email' => 'required|string|email|max:255|unique:users',
            'name' => 'required|string',
            'photo' => 'required|string',
        ]);

        if ($validator->fails()) {
            return self::customResponse(false, 'Validation Error', $validator->errors());
        }

        $user = User::create([
            'email' => $req['email'],
            'name' => $req['name'],
            'photo' => $req['photo'],
            'status' => 'Halo, Saya Menggunakan Chatify',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
        $user['token'] = $token;

        return self::customResponse(true, 'Success Register', $user);
    }

    public function login(Request $request)
    {
        $req = json_decode($request->getContent(), false);
        $user = User::where('email', $req->email)->first();

        if (!$user) {
            return self::customResponse(false, 'User not found', null);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        $user['token'] = $token;

        return self::customResponse(true, 'Success Login', $user);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return self::customResponse(true, 'Success Logout', null);
    }
}

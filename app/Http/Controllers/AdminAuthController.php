<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class AdminAuthController extends Controller
{
    //

    public function login(Request $request)
    {
        $fields = $request->validate([
            "email" => 'required|string',
            "password" => 'required|string',
        ]);

        // Check Email
        $where = ["email" => $fields['email']];
        $user = Admin::where($where)->first();

        // Check Password
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => "Bad credentials"
            ], 401);
        }

        $token = $user->createToken('myapptoken', ['role:admin'])->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function user()
    {
        $user = auth()->user();
        return response($user, 201);
    }
}

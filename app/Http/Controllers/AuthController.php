<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

use App\Models\Korisnik;



class AuthController extends Controller
{
    //

    public function register(Request $request)
    {
        // $fields = $request->validate([
        //     "korisnickoIme" => 'required|string',
        //     "email" => 'required|string|unique:korisnik,email',
        //     "password" => 'required|string|confirmed'
        // ]);

        $user = Korisnik::create([
            'korisnicko_ime' => $request['korisnickoIme'],
            'email' => $request['email'],
            'password' => bcrypt($request['password']),
        ]);

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token,
        ];

        return response($response, 201);
    }

    public function login(Request $request)
    {
        $fields = $request->validate([
            "email" => 'required|string',
            "password" => 'required|string',
        ]);

        // Check Email
        $where = ["email" => $fields['email']];
        $user = Korisnik::where($where)->first();

        // Check Password
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => "Bad credentials"
            ], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

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

    // public function logout(Request $request)
    // {
    //     auth()->user()->tokens()->delete();

    //     $response = [
    //         'message' => 'logged out'
    //     ];

    //     return response($response, 201);
    // }
}

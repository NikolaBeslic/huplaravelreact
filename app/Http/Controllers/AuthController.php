<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Korisnik;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    //

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "korisnickoIme" => 'required|string|unique:korisnik,korisnicko_ime',
            "email" => 'required|string|email|unique:korisnik,email',
            "password" => 'required|string|confirmed'
        ], [
            'korisnickoIme.unique' => 'Ovo korisnicko ime je zauzeto',
            'email.unique' => 'Vec postoji nalog sa ovom email adresom',
            'email.email' => 'Email adresa nije ispravna',
            'password.confirmed' => 'Lozinke se ne poklapaju'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "message" => 'validation_error',
                "errors" => $validator->errors(),
            ], 422);
        }



        if (Korisnik::create([
            'korisnicko_ime' => $request['korisnickoIme'],
            'email' => $request['email'],
            'password' => bcrypt($request['password']),
        ])) {
            return response()->json();
        }
        return response()->json("Nepoznata greska", 500);

        // $token = $user->createToken('myapptoken')->plainTextToken;

        // $response = [
        //     'user' => $user,
        //     'token' => $token,
        // ];


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

    public function logout(Request $request)
    {
        $userId = $request->id;
        $user = Korisnik::find($userId);

        // $request->user()->currentAccessToken()->delete();

        $tokenId = $user->tokens()->where('tokenable_id', $user->id)->value('id');

        if ($user->tokens()->where('id', $tokenId)->delete()) {
            $response = [
                'message' => 'logged out'
            ];

            return response($response, 201);
        }
        //Auth::user()->currentAccessToken()->delete();
        //$user->tokens()->where('id', $tokenId)->delete();
        return response('Error', 500);
    }
}

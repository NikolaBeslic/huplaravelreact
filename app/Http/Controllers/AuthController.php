<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Korisnik;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

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

        try {
            $korisnik = Korisnik::create([
                'korisnicko_ime' => $request['korisnickoIme'],
                'email' => $request['email'],
                'password' => bcrypt($request['password']),
            ]);
            $korisnik->sendEmailVerificationNotification();
            return response()->json();
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function login(Request $request)
    {
        $fields = $request->validate([
            "login_field" => 'required|string',
            "password" => 'required|string',
        ]);


        $login_field = $request->input('login_field');
        $password = $request->input('password');
        $user = Korisnik::where('korisnicko_ime', $login_field)->orWhere('email', $login_field)->first();

        // Avoid leaking whether user exists
        if (!$user) {
            return response()->json("Pogrešni podaci za logovanje.", 401);
        }

        if ((int)$user->statuskorisnikaid !== 1) {
            return response()->json("Account is disabled. Contact support.", 401);
        }

        $fieldName = filter_var($login_field, FILTER_VALIDATE_EMAIL) ? 'email' : 'korisnicko_ime';


        if (!Auth::attempt([$fieldName => $login_field, 'password' => $password], true)) {
            return response()->json("Pogrešni podaci za logovanje.", 401);
        }

        $request->session()->regenerate();

        return response()->json($user, 201);
    }

    public function user()
    {

        $user = auth()->user();
        return response($user, 201);
    }

    public function logout(Request $request)
    {

        try {
            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json([
                'message' => 'Logged out successfully'
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Logout failed'
            ], 500);
        }
    }
}

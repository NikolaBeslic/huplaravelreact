<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{
    //

    public function login(Request $request)
    {
        try {
            $request->validate([

                "login_field" => 'required|string',
                "password" => 'required|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json($e, 422);
        }

        $login_field = $request->input('login_field');
        $password = $request->input('password');

        $fieldName = filter_var($login_field, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $admin = Admin::where('username', $login_field)->orWhere('email', $login_field)->first();


        if (!Auth::guard('admin')->attempt([$fieldName => $login_field, 'password' => $password], true)) {
            return response()->json("Pogrešni podaci za logovanje.", 401);
        }

        $request->session()->regenerate();

        return response()->json($admin, 201);
    }

    public function user()
    {
        $user = Auth::guard('admin')->user();
        return response($user, 201);
    }

    public function logout(Request $request)
    {
        try {
            Auth::guard('admin')->logout();

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

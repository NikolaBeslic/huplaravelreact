<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class AdminAuthController extends Controller
{
    //

    public function login(Request $request)
    {
        $fields = $request->validate([
            "login_field" => 'required|string',
            "password" => 'required|string',
        ]);

        $login_field = $request->input('login_field');
        $password = $request->input('password');

        $fieldName = filter_var($login_field, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $admin = Admin::where('username', $login_field)->orWhere('email', $login_field)->first();


        if (!Auth::guard('admin')->attempt([$fieldName => $login_field, 'password' => $password], true)) {
            return response()->json(['message' => 'Invalid credentials'], 422);
        }

        $request->session()->regenerate();

        return response()->json($admin, 201);
    }

    public function user()
    {
        $user = Auth::guard('admin')->user();
        return response($user, 201);
    }
}

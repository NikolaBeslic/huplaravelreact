<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Korisnik;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect(Request $request)
    {
        $driver = Socialite::driver('google');
        $redirectUrl = $request->query('redirect_url');
        return $driver->stateless()->with(['state' => base64_encode(json_encode(['redirect_url' => $redirectUrl]))])->redirect();
    }

    public function callback(Request $request)
    {
        $driver = Socialite::driver('google');

        $googleUser = $driver->stateless()->user();

        $user = Korisnik::updateOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'korisnicko_ime' => $googleUser->getName()
            ]
        );

        Auth::login($user);
        $token = $user->createToken('myapptoken')->plainTextToken;

        $state = json_decode(base64_decode($request->query('state', '')), true);
        $redirectUrl = $state['redirect_url'] ?? session('redirect_url', env("FRONT_END_BASEURL"));

        return redirect(env('FRONT_END_BASEURL') . '/auth/google' . '?token=' . $token . '&user=' . urlencode(json_encode($user)) . '&redirect_url=' . $redirectUrl);
    }
}

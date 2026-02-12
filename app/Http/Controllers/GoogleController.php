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

        return $driver->redirect();
    }

    public function callback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user(); // no stateless()
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            return redirect('http://localhost:3000/login')->withErrors('Invalid Google login state');
        }

        $user = Korisnik::updateOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'korisnicko_ime' => $googleUser->getName()
            ]
        );

        $authRes = Auth::login($user, true);
        $currentUser = Auth::user();

        // $state = json_decode(base64_decode($request->query('state', '')), true);
        $redirectUrl = $state['redirect_url'] ?? session('redirect_url', env("FRONT_END_BASEURL"));
        return redirect()->away($redirectUrl);
    }
}

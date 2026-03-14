<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Korisnik;
use Exception;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect(Request $request)
    {
        session(['google_redirect_url' => $request->input('redirect_url', '/')]);
        $driver = Socialite::driver('google');

        return $driver->redirect();
    }

    public function callback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user(); // no stateless()

            $user = Korisnik::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                $user = new Korisnik();
                $user->fill([
                    'email' => $googleUser->getEmail(),
                    'korisnicko_ime' => $googleUser->getName(),
                    'provider_id' => $googleUser->getId(),
                    'provider' => 'Google',
                    'statuskorisnikaid' => 1,
                    'email_verified_at' => now()
                ]);
            } else {
                $user->fill([

                    'provider_id' => $googleUser->getId(),
                    'provider' => 'Google',
                    'statuskorisnikaid' => 1
                ]);
            }

            $user->save();

            $authRes = Auth::login($user, true);
            $currentUser = Auth::user();

            // $state = json_decode(base64_decode($request->query('state', '')), true);
            $redirectUrl = session('google_redirect_url', env("FRONT_END_BASEURL"));
            return redirect()->away($redirectUrl);
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {

            return redirect(
                env('FRONT_END_BASEURL')
            )->withErrors('Invalid Google login state');
        } catch (Exception $e) {
            return redirect(
                env('FRONT_END_BASEURL')
            )->withErrors("Greška prilikom logovanja. Pokušajte opet ili kontaktirajte našu podršku.");
        }
    }
}

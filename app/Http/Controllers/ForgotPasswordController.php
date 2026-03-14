<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\ForgotPasswordMail;
use App\Models\Korisnik;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = Korisnik::where('email', $request->email)->first();

        // Always return same response for security
        if (!$user) {
            return response()->json([
                'message' => 'Ako nalog postoji za uneti email, poslali smo link za reset lozinke.'
            ]);
        }

        // Delete existing reset requests for this email
        DB::table('password_resets')->where('email', $user->email)->delete();

        $plainToken = Str::random(64);

        DB::table('password_resets')->insert([
            'email' => $user->email,
            'token' => Hash::make($plainToken),
            'created_at' => Carbon::now()->addHours(2),
        ]);

        $resetUrl = config('app.frontend_url') .
            '/resetuj-lozinku?email=' . urlencode($user->email) .
            '&token=' . urlencode($plainToken);

        $appName = config('app.name');
        $logoUrl =  'https://hocupozoriste.rs/slike/logo.png';

        Mail::to($user->email)->send(new ForgotPasswordMail($resetUrl, $user, $appName, $logoUrl));

        return response()->json([
            'message' => 'Ako nalog sa tom email adresom postoji, poslali smo vam link za reset lozinke.'
        ], 200);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'token' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'password.confirmed' => 'Lozinke se ne poklapaju.',
        ]);

        $user = Korisnik::where('email', $request->email)->first();
        $passwordReset = DB::table('password_resets')->where('email', $request->email)->first();

        if (!$passwordReset) {
            return response()->json("Link za reset lozinke nije validan ili je istekao.", 400);
        }

        $isExpired = Carbon::parse($passwordReset->created_at)
            ->isPast();

        if ($isExpired) {
            DB::table('password_resets')
                ->where('email', $request->email)
                ->delete();
            return response()->json("Link za reset lozinke je istekao.", 400);
        }

        if (!Hash::check($request->token, $passwordReset->token)) {
            return response()->json("Link za reset lozinke nije validan ili je istekao.", 400);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_resets')
            ->where('email', $request->email)
            ->delete();

        return response()->json([
            'message' => 'Lozinka je uspešno promenjena.'
        ], 200);
    }
}

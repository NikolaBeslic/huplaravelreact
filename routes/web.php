<?php

use App\Http\Controllers\GoogleAnalyticsController;
use App\Http\Controllers\GoogleController;
use App\Models\Korisnik;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/auth/google', [GoogleController::class, 'redirect']);
Route::get('/auth/google/callback', [GoogleController::class, 'callback']);
Route::get('/ga', [GoogleAnalyticsController::class, 'visitorsAndPageViews']);


Route::get(
    '/email/verify/{id}/{hash}',
    function (Request $request, $id, $hash) {

        $user = Korisnik::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403);
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        return redirect(env('FRONT_END_BASEURL') . '/uspesna-verifikacija?verified=1');
    }
)->middleware(['signed'])->name('verification.verify');

Route::get('/email/verify', function () {
    return response()->json(['message' => 'Email verification required.'], 403);
})->middleware('auth:sanctum')->name('verification.notice');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return response()->json(['message' => 'Verification link sent.']);
})->middleware(['auth:sanctum', 'throttle:6,1'])->name('verification.send');


Route::get('/test-mail', function () {

    Mail::raw('Hello from Laravel', function ($message) {
        $message->to('test@test.com')
            ->subject('Mailhog Test');
    });

    return 'Email sent';
});

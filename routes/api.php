<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\PredstaveController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TekstoviController;
use App\Http\Controllers\PozoristaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AutoriController;
use App\Http\Controllers\RepertoariController;
use App\Http\Controllers\FestivaliController;
use App\Http\Controllers\KategorijeController;
use App\Http\Controllers\TagoviController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/search', [TekstoviController::class, 'search']);
Route::get('/get-trending-posts', [TekstoviController::class, 'getTrendingPosts']);
Route::get('/get-slider-posts', [TekstoviController::class, 'getSliderPosts']);
Route::get('/get-posts', [TekstoviController::class, 'getPosts']);
Route::get('/get-category-posts/{katergorija_slug}', [TekstoviController::class, 'getCategoryPosts']);
Route::get('/intervju/{slug}', [TekstoviController::class, 'getIntervju']);
Route::get('/get-single-text/{slug}', [TekstoviController::class, 'getSinglePost']);
Route::get('/get-related-posts/{tekstid}', [TekstoviController::class, 'getRelatedPosts']);
// Predstave
Route::get('/get-predstave', [PredstaveController::class, 'getPredstave']);
Route::get('/get-premijere', [PredstaveController::class, 'getPremijere']);
Route::get('/get-najnovije-predstave', [PredstaveController::class, 'getNajpopularnijePredstave']);
Route::get('/get-predstave-naslovna', [PredstaveController::class, 'getPredstaveZaNaslovnu']);
Route::get('/get-all-predstave', [PredstaveController::class, 'getAllPredstave']);
Route::get('/predstava-single/{predstava_slug}', [PredstaveController::class, 'getSinglePredstava']);
Route::get('/get-predstave-with-texts', [PredstaveController::class, 'getPredstaveWithTekst']);
Route::get('/get-pozorista', [PozoristaController::class, 'getPozorista']);
Route::get('/get-all-pozorista', [PozoristaController::class, 'getAllPozorista']);
Route::get('/pozoriste-single/{pozoriste_slug}', [PozoristaController::class, 'getSinglePozoriste']);
Route::get('/get-autori', [AutoriController::class, 'getAutori']);
Route::get('/get-single-autor/{autor_slug}', [AutoriController::class, 'getSingleAutor']);
Route::get('/get-repertoari', [RepertoariController::class, 'getJsonRepertoari']);
Route::get('/get-zanrovi', [PredstaveController::class, 'getZanrovi']);
Route::get('/get-gradovi', [PredstaveController::class, 'getGradovi']);
Route::get('/get-some-posts', [TekstoviController::class, 'getSomePosts']);
Route::get('/get-festivali', [FestivaliController::class, 'getFestivali']);
Route::get('/festival-single/{festival_slug}', [FestivaliController::class, 'getSingleFestival']);
Route::get('/get-all-festivali', [FestivaliController::class, 'getAllFestivali']);
Route::get('/get-hupkast', [TekstoviController::class, 'getAllHuPkast']);
Route::get('/hupkast-single/{hupkast_slug}', [TekstoviController::class, 'getSingleHuPkast']);
Route::get('/get-hupikon', [TekstoviController::class, 'getAllHupikon']);
Route::get('get-all-tagovi', [TagoviController::class, 'getAllTagovi']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/adminlogin', [AdminAuthController::class, 'login']);
Route::get('/admin/tekstovi', [TekstoviController::class, 'adminindex']);
Route::get('/admin/kategorije', [KategorijeController::class, 'adminindex']);
Route::get('/get-tekst/{tekstid}', [TekstoviController::class, 'getTekstById']);
Route::put('update-tekst', [TekstoviController::class, 'update2']);
Route::put('/admin/tekstovi/istakni', [TekstoviController::class, 'istakniTekst']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/flmngr', function () {

    \EdSDK\FlmngrServer\FlmngrServer::flmngrRequest(
        array(
            'dirFiles' => base_path() . '/public/slike'
        )
    );
});

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
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\HuPkastController;
use App\Http\Controllers\ImageUploadController;
use App\Http\Controllers\KategorijeController;
use App\Http\Controllers\KorisniciController;
use App\Http\Controllers\TagoviController;
use App\Http\Controllers\ZanroviController;
use App\Http\Controllers\GoogleAnalyticsController;

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
Route::get('/pretraga', [TekstoviController::class, 'searchAll']);
Route::get('/get-trending-posts', [TekstoviController::class, 'getSidebarPosts']);
Route::get('/get-slider-posts', [TekstoviController::class, 'getSliderPosts']);
Route::get('/get-posts', [TekstoviController::class, 'getPosts']);
Route::get('/get-category-posts/{katergorija_slug}', [TekstoviController::class, 'getCategoryPosts']);
Route::get('/intervju/{slug}', [TekstoviController::class, 'getIntervju']);
Route::get('/get-single-text/{kategorija_slug}/{slug}', [TekstoviController::class, 'getSinglePost']);
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
Route::get('/get-texts-by-tag/{tag_slug}', [TagoviController::class, 'getTekstsByTag']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->get('/get-korisnicki-profil', [KorisniciController::class, 'getKorisnickiProfil']);

Route::post('/adminlogin', [AdminAuthController::class, 'login']);
/* Admin naslovna */
Route::get('/admin/tekstovi-za-naslovnu', [TekstoviController::class, 'adminGetTekstoviZaNaslovnu']);
Route::get('/admin/predstave-za-naslovnu', [PredstaveController::class, 'adminGetPredstaveZaNaslovnu']);
//Route::get('/admin/komentari-za-naslovnu', [KomentariController::class, 'getKomentariZaNaslovnu']);
Route::get('/admin/festivali-za-naslovnu', [FestivaliController::class, 'adminGetFestivaliZaNaslovnu']);
Route::get('/admin/igranja-za-naslovnu', [RepertoariController::class, 'getIgranjaZaNaslovnu']);

Route::get('/admin/tekstovi', [TekstoviController::class, 'adminindex']);
Route::get('/get-tekst/{tekstid}', [TekstoviController::class, 'getTekstById']);
Route::put('/admin/tekstovi/istakni', [TekstoviController::class, 'istakniTekst']);
Route::put('update-tekst', [TekstoviController::class, 'update2']);
Route::post('/admin/create-tekst', [TekstoviController::class, 'store2']);
/* Admin kategorije */
Route::get('/admin/kategorije', [KategorijeController::class, 'adminindex']);
Route::get('/admin/get-single-kategorija/{kategorijaid}', [KategorijeController::class, 'getSingleKategorija']);
Route::post('/admin/create-kategorija', [KategorijeController::class, 'store']);
Route::put('/admin/update-kategorija', [KategorijeController::class, 'update']);
Route::get('/admin/get-sve-kategorije', [KategorijeController::class, 'getSveKategorije']);
/* Admin zanrovi */
Route::get('/admin/zanrovi', [ZanroviController::class, 'adminindex']);
Route::get('/admin/get-single-zanr/{zanrid}', [ZanroviController::class, 'getSingleZanr']);
Route::post('/admin/create-zanr', [ZanroviController::class, 'store']);
Route::put('/admin/update-zanr', [ZanroviController::class, 'update']);
/* Admin tagovi */
Route::get('/admin/get-svi-tagovi', [TagoviController::class, 'getAllTagovi']);
Route::post('/admin/tagovi/store', [TagoviController::class, 'storeTags']);
/* Admin autori */
Route::get('/admin/get-all-autori', [AutoriController::class, 'getAllAutori']);
Route::get('/admin/get-single-autor/{autorid}', [AutoriController::class, 'getSingleAutorAdmin']);
Route::get('/admin/get-gradovi', [PredstaveController::class, 'getGradovi']);
Route::post('/admin/create-autor', [AutoriController::class, 'store']);
Route::put('/admin/update-autor', [AutoriController::class, 'update']);
/* Admin pozorista */
Route::get('/admin/get-all-pozorista', [PozoristaController::class, 'getAllPozorista']);
Route::get('/admin/get-single-pozoriste/{pozoristeid}', [PozoristaController::class, 'getSinglePozoristeAdmin']);
Route::put('/admin/update-pozoriste', [PozoristaController::class, 'update']);
Route::post('/admin/create-pozoriste', [PozoristaController::class, 'store']);
/* Admin predstave */
Route::get('/admin/get-all-predstave', [PredstaveController::class, 'getAllPredstaveAdmin']);
Route::get('/admin/get-single-predstava/{predstavaid}', [PredstaveController::class, 'getSinglePredstavaById']);
Route::post('/admin/create-predstava', [PredstaveController::class, 'store']);
Route::put('/admin/update-predstava', [PredstaveController::class, 'update']);

/* Admin HuPkast */
Route::get('/admin/check-hupkast-rss', [HuPkastController::class, 'checkHuPkastRSS']);
Route::get('/admin/hupkast/insert-new-episodes', [HuPkastController::class, 'insertHuPkastFromRss']);
Route::get('/admin/get-all-hupkast', [HuPkastController::class, 'adminGetAllHupkast']);
Route::post('/admin/hupkast-store', [TekstoviController::class, 'hupkastStore']);
Route::get('/admin/get-hupkast-platforme', [HuPkastController::class, 'getHupkastPlatforme']);

/* Admin HuPikon */
Route::get('/admin/get-all-hupikon', [TekstoviController::class, 'adminGetAllHupikon']);
Route::post('/admin/hupikon-store', [TekstoviController::class, 'hupikonStore']);

/* Admin Reperotari */
Route::get('/admin/pozoriste-with-predstave/{pozoriste_slug}', [PozoristaController::class, 'getPozoristeWithPredstave']);
Route::get('/admin/get-igranja-pozorista/{pozoristeid}', [RepertoariController::class, 'getIgranjaPozorista']);
Route::post('/admin/igranje-store', [RepertoariController::class, 'igranjeStore']);
Route::put('/admin/igranje-update', [RepertoariController::class, 'igranjeUpdate']);
Route::delete('/admin/igranje-delete/{id}', [RepertoariController::class, 'igranjeDelete']);
Route::get('/admin/get-all-for-gostovanja', [RepertoariController::class, 'getAllForGostovanja']);
/* Admin Festivali */
Route::get('/admin/get-all-festivali', [FestivaliController::class, 'adminGetAllFestivali']);
Route::post('/admin/festival-store', [FestivaliController::class, 'store']);
Route::put('/admin/festival-update', [FestivaliController::class, 'update']);
Route::get('/admin/get-single-festival/{festivalid}', [FestivaliController::class, 'getSingleFestivalAdmin']);
/* Admin Grad */
Route::post("/admin/store-grad", [PozoristaController::class, 'gradStore']);

/* Google Analytics */
Route::get('/auth/google/redirect', [GoogleController::class, 'redirect']);
Route::get('/auth/google/callback', [GoogleController::class, 'callback']);
Route::get('/admin/get-fetches', [GoogleAnalyticsController::class, 'getFetches']);
Route::get('/admin/get-ga-monthly-data', [GoogleAnalyticsController::class, 'getMonthlyData']);
Route::get('/admin/get-fetch-details/{fetchId}', [GoogleAnalyticsController::class, 'getFetchDetails']);
Route::get('/admin/get-total-visits-period', [GoogleAnalyticsController::class, 'getTotalVisitsForPeriod']);

Route::middleware('auth:sanctum')->post('/predstava/oceni', [PredstaveController::class, 'oceni']);
Route::middleware('auth:sanctum')->post('/predstava/dodajNaListuZelja', [PredstaveController::class, 'dodajNaListuZelja']);
Route::middleware('auth:sanctum')->post('/predstava/dodajUOdgledane', [PredstaveController::class, 'dodajUOdgledane']);
Route::middleware('auth:sanctum')->post('/predstava/dodaj-komentar', [PredstaveController::class, 'dodajKomentar']);
Route::middleware('auth:sanctum')->post('/pozorista/dodajUOmiljena', [PozoristaController::class, 'dodajUOmiljena']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/admin/uploadImage', [ImageUploadController::class, 'uploadImage']);

Route::post('/flmngr', function () {

    \EdSDK\FlmngrServer\FlmngrServer::flmngrRequest(
        array(
            'dirFiles' => base_path() . '/react/public/slike'
        )
    );
});

<?php

namespace App\Http\Controllers;

use App\Models\Igranje;
use App\Models\Pozoriste;
use App\Models\Predstava;
use App\Models\Scena;
use DateTime;
use Illuminate\Support\Str;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DomCrawler\Crawler;


class ScraperController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $response = '';
        $browser = new HttpBrowser(HttpClient::create(['timeout' => 60]));

        $crawler = $browser->request('GET', 'https://www.jdp.rs/repertoar/');
        $response = $crawler->filter('#repertoar article')->each(
            function ($node) {
                $dan = $node->filter('.calendar__item-date .day > strong')->text();
                $mesec = $node->filter('.calendar__item-date .day > span')->text();
                $vremeIscena = $node->filter('.time')->text(); // TO DO: seperate by '|'
                $predstava = $node->filter('.calendar__item-info-title > a')->text();
                return $predstava;
            }
        );
        return dd($response);
    }

    public function scrapeRepertoarPozorista($pozoriste_slug)
    {
        $pozoriste = Pozoriste::select(['pozoristeid', 'naziv_pozorista'])->where('pozoriste_slug', $pozoriste_slug)
            ->first();

        if (!$pozoriste) {
            return response()->json(['message' => 'Pozoriste not found'], 404);
        }

        switch ($pozoriste_slug) {
            case 'narodno-pozoriste':
                return $this->getNarodnoBeograd();
            case 'bdp':
                return $this->getBdp();
            case 'atelje-212':
                return $this->getAtelje212();
            default:
                return response()->json(['message' => 'Scraper not implemented for this theater'], 400);
        }
    }


    public function getAtelje212()
    {

        $browser = new HttpBrowser(HttpClient::create(['timeout' => 60]));

        $allDays = [];

        $crawler = $browser->request('GET', 'https://bilet.atelje212.rs/repertoar.php#danas');

        //$repertoar = $crawler->filter('.card');
        //$repertoar->outerHtml();
        // return ddd($repertoar->outerHtml());

        $crawler->filter('.card')->each(function ($node) {
            $allDays = [];

            var_dump($allDays, 1);
        });

        return ddd($allDays);
    }

    public function getNarodnoBeograd()
    {
        $pozoriste = Pozoriste::select(['pozoristeid', 'naziv_pozorista'])->where('pozoriste_slug', 'narodno-pozoriste')
            ->first();
        $browser = new HttpBrowser(HttpClient::create(['timeout' => 60]));

        $arr = [];
        $crawler = $browser->request('GET', 'https://www.narodnopozoriste.rs/lat/repertoar/');

        $crawler->filter('#content .repertoarwide-entry')->each(function ($node) use (&$arr, $pozoriste) {
            $igranje = new Igranje();
            $igranje->pozoriste = $pozoriste;

            $dayString = $node->filter('.repertoarwide-entry-date')->text();
            $day = filter_var($dayString, FILTER_SANITIZE_NUMBER_INT);
            $month = ($node->filter('.mesec')->text());
            $date = new DateTime($month . ' ' . $day . ' ' . date('Y'));
            $igranje->datum = $date->format('Y-m-d');

            $vremeIScena = $node->filter('.repertoarwide-meta')->text();
            preg_match('/\b([01]?\d|2[0-3]):[0-5]\d\b/', $vremeIScena, $m);
            $igranje->vreme =  $m[0] ?? null;


            $scenaString = trim(preg_replace('/\b([01]?\d|2[0-3]):[0-5]\d\b/', '', $vremeIScena));
            $scenaString = Str::replace("·", "", $scenaString);
            $scena = Scena::where('naziv_scene', 'LIKE', '%' . trim($scenaString) . '%')->where('pozoristeid', $pozoriste->pozoristeid)->first();
            $igranje->scena = $scena;

            $predstavaString = $node->filter('.entry-title > h4 > a')->text();
            $predstava = Predstava::select(['predstavaid', 'naziv_predstave'])
                ->where('naziv_predstave', 'LIKE', '%' . trim($predstavaString) . '%')
                ->first();

            $igranje->predstava = $predstava;
            array_push($arr, $igranje);
        });
        return json_encode($arr);
    }

    public function getBdp()
    {
        $browser = new HttpBrowser(HttpClient::create(['timeout' => 60]));

        $crawler = $browser->request('GET', 'https://bdp.rs/repertoar/oktobar-2/');
        $response = $crawler->filter('.rep-content .single-date')->each(
            function ($node) {
                $datum = $node->filter('.date')->text();

                $predstavaVelikaScenaCount = $node->filter('.big-scena > div')->count();

                $predstava = null;
                $vreme = null;
                if ($predstavaVelikaScenaCount == 2) {
                    $predstava = $node->filter('.big-scena > div > span')->text();
                    $vremeString = $node->filter('.big-scena > div .dialog')->text();
                    $vreme = filter_var($vremeString, FILTER_SANITIZE_NUMBER_INT); // TO DO check if time can be inserted in this format
                }


                $predstavaVelikaScenaCount = $node->filter('.small-scena > div')->count();

                $predstava = null;
                if ($predstavaVelikaScenaCount == 2) {
                    $predstava = $node->filter('.small-scena > div > span')->text();
                }
            }
        );
        return dd($response);
    }
}

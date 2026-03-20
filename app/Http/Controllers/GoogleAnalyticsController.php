<?php

namespace App\Http\Controllers;

use AkkiIo\LaravelGoogleAnalytics\Facades\LaravelGoogleAnalytics;
use AkkiIo\LaravelGoogleAnalytics\Period;
use App\Models\GaFetch;
use App\Models\GaFetchDetails;
use App\Models\Kategorija;
use App\Models\Tekst;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;
use Google\Type\Month;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class GoogleAnalyticsController extends Controller
{
    /* Showing data alreadz existing in database*/
    public function getFetches()
    {
        $fetches = GaFetch::with('fetchType')->withCount('fetchDetails')->orderByDesc('fetch_id')->get();
        return json_encode($fetches);
    }

    public function getFetchDetails(Request $request)
    {
        $fetchDetails = GaFetchDetails::where('fetch_id', $request->fetchId)->get();
        return json_encode($fetchDetails);
    }

    public function insertIntoMostRead()
    {
        $fetches = GaFetch::where('type_id', 6)->with(
            [
                'fetchDetails' => function ($query) {
                    $query->orderBy('views', 'desc');
                }
            ]
        )->orderBy('created_at', 'desc')->first()->toArray();

        $fetchDetailsArr = $fetches['fetch_details'];
        $brojac = 0;
        $j = 0;
        while ($brojac <= 5) {

            $slug = $fetchDetailsArr[$j]['url'];
            if (substr_count($slug, "/") == 2) {
                $tekstSlug = ltrim(strrchr($slug, "/"), "/");
                $tekstid = Tekst::where('slug', $tekstSlug)->value('tekstid');
                if ($tekstid != null) {
                    DB::table('most_read')->insert([
                        'fetch_details_id' => $fetchDetailsArr[$j]['fetch_details_id'],
                        'tekstid' => $tekstid,
                        'views' => $fetchDetailsArr[$j]['views']
                    ]);
                    $brojac++;
                }
            }
            $j++;
        }
    }

    public function getMonthlyData(Request $request)
    {
        $startDate = Carbon::create($request->query('year'), $request->query('month'), 1)->startOfMonth();
        $endDate = Carbon::create($request->query('year'), $request->query('month'), 1)->endOfMonth();

        $period = Period::create($startDate, $endDate);

        $parameter = $startDate . ' - ' . $endDate;

        $analyticsData = LaravelGoogleAnalytics::getMostViewsByPage($period, $count = 1000);
        $this->saveGoogleAnalyticsData($analyticsData, 5, $parameter, false);
        $this->saveGoogleAnalyticsData($analyticsData, 6, $parameter, true);

        $this->insertIntoMostRead();

        $fetches = GaFetch::with('fetchType')->with('fetchDetails')->get();
        return json_encode($fetches);
    }

    public function getTotalVisitsForPeriod()
    {
        $startDate = Carbon::create(2024, 3, 1)->startOfMonth();
        $endDate = Carbon::create(2024, 3, 1)->endOfMonth();

        $period = Period::create($startDate, $endDate);

        $totalVisits = LaravelGoogleAnalytics::dateRange($period)
            ->metric('sessions')
            ->get();

        return json_encode($totalVisits);
    }

    public function visitorsAndPageViews()
    {
        //retrieve visitors and page view data for the current day and the last seven days
        $analyticsData = LaravelGoogleAnalytics::getTotalViewsByPage(Period::days(14));
        //$this->saveGoogleAnalyticsData($analyticsData);

        return json_encode($analyticsData);
    }

    public function saveGoogleAnalyticsData($analyticsData, $typeid, $parameter = null, $tekst = true)
    {
        if ($analyticsData != null) {
            $gaFetch = new GaFetch();
            $gaFetch->type_id = $typeid;
            $gaFetch->parameter = $parameter;
            $gaFetch->error_msg = null;
            $gaFetch->save();
            for ($i = 0; $i < count($analyticsData); $i++) {
                # code...
                $ad = $analyticsData[$i];
                $relativeUrl = $this->getRelativeUrl($ad['fullPageUrl']);
                if ($tekst) {
                    if (!$this->daLiJeTekst($relativeUrl))
                        continue;
                }
                $detail = new GaFetchDetails();
                $detail->title = $ad['pageTitle'];

                $detail->url = $relativeUrl;
                $detail->views = $ad['screenPageViews'];
                $detail->fetch_id = $gaFetch->fetch_id;
                $detail->save();
            }
        }
    }

    public function getRelativeUrl($absoluteUrl)
    {
        $relativeUrl = Str::replace("hocupozoriste.rs", "", $absoluteUrl); //substr_replace($absoluteUrl,"hocupozoriste.rs", "");
        return $relativeUrl;
    }

    public function daLiJeTekst($relativeUrl)
    {
        $kategorije = Kategorija::pluck('kategorija_slug')->toArray();
        $relativeUrl = Str::substr($relativeUrl, 1); // removes leading '/'        
        if (Str::startsWith($relativeUrl, $kategorije))
            return true;

        return false;
    }
}

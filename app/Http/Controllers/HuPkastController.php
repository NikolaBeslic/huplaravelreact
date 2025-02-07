<?php

namespace App\Http\Controllers;

use App\Models\Hupkast;
use App\Models\Kategorija;
use App\Models\Tekst;
use Illuminate\Http\Request;
use Dotenv\Exception\ValidationException;
use Illuminate\Support\Facades\Http;
use stdClass;
use willvincent\Feeds\Facades\FeedsFacade;
use Illuminate\Support\Str;


class HuPkastController extends Controller
{

    public function checkHuPkastRSS()
    {
        $feed = FeedsFacade::make('https://anchor.fm/s/ebcb0aac/podcast/rss');

        $itemsNo = count($feed->get_items());
        $hupkastNumber = Tekst::where('kategorijaid', 11)->where('is_published', 1)->count();
        $responseText = 'Neuspesna provera';
        if ($itemsNo == $hupkastNumber) {
            $responseText = 'Nema novih epizoda';
            $status = 304;
        } else if ($itemsNo > $hupkastNumber) {
            $responseText = 'Nove epizode u RSS';
            $status = 200;
        }
        return response()->json($responseText, $status);
    }

    public function getHuPkastRssItems()
    {
        $feed = FeedsFacade::make('https://anchor.fm/s/ebcb0aac/podcast/rss');

        $items = $feed->get_items();
        $episodes = array();

        foreach ($items as $item) {
            # code...
            $obj = new stdClass();
            $obj->title = $item->get_title();
            $obj->description = $item->get_description();
            $obj->published_at = $item->get_date();
            $obj->image = $item->get_item_tags('http://www.itunes.com/dtds/podcast-1.0.dtd', 'image')[0]["attribs"][""]["href"];
            $obj->sezona = $item->get_item_tags('http://www.itunes.com/dtds/podcast-1.0.dtd', 'season')[0]["data"];
            $obj->epizoda = $item->get_item_tags('http://www.itunes.com/dtds/podcast-1.0.dtd', 'episode')[0]["data"];
            $obj->mp3_url = $item->get_enclosure()->link;

            array_push($episodes, $obj);
        }
        return $episodes;
    }

    public function getLinks($episode)
    {
        // youtube feed
        $feed = FeedsFacade::make('https://rss.app/feeds/Vrqf9S0tihBWaR65.xml');
        $items = $feed->get_items();
        $ytLink = null;

        foreach ($items as $item) {
            # code...
            if ($episode->title == $item->get_title()) {
                $ytLink = $item->get_link();
            }
        }
        return json_encode($ytLink);
    }

    public function insertHuPkastFromRss()
    {
        $arr = $this->getHuPkastRssItems();
        foreach ($arr as $item) {
            $tekst = new Tekst();
            $tekst->naslov = $item->title;
            $tekst->slug = Str::slug($item->title);
            $tekst->uvod = substr($item->description, 0, 200);
            $tekst->sadrzaj = $item->description;
            $tekst->kategorijaid = 11;
            if ($item->image) {
                $httpResponse = Http::get($item->image);
                if ($httpResponse->successful()) {
                    $imageUrl = base_path() . '/react/public/slike/hupkast/';
                    $extension =  pathinfo(parse_url($item->image, PHP_URL_PATH), PATHINFO_EXTENSION);
                    $fileName = $imageUrl . Str::slug($item->title) . '.' . $extension;
                    if (file_put_contents($fileName, $httpResponse->body()))
                        $tekst->tekst_photo = '/slike/hupkast/' . Str::slug($item->title) . '.jpg';
                }
            }
            $tekst->is_published = 1;
            $tekst->published_at = $item->published_at;
            $tekst->save();

            $hupkast = new Hupkast();
            $hupkast->sezona = $item->sezona;
            $hupkast->epizoda = $item->epizoda;
            $hupkast->mp3_url = $item->mp3_url;
            $hupkast->tekstid = $tekst->tekstid;
            $hupkast->save();
        }
        return "ok";
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Kategorija;
use App\Models\Pozoriste;
use App\Models\Predstava;
use App\Models\Tekst;
use App\Models\Festival;
use DateTime;
use Illuminate\Support\Str;



class SitemapController extends Controller
{

    public function index()
    {
        $baseUrl = config('app.url');

        $staticPages = [
            [
                'loc' => $baseUrl,
                'lastmod' => now()->toDateString(),
                'changefreq' => 'daily',
                'priority' => '1.0',
            ],
            [
                'loc' => $baseUrl . '/predstave',
                'lastmod' => now()->toDateString(),
                'changefreq' => 'daily',
                'priority' => '0.9',
            ],
            [
                'loc' => $baseUrl . '/pozorista',
                'lastmod' => now()->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ],
            [
                'loc' => $baseUrl . '/festivali',
                'lastmod' => now()->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ],
            [
                'loc' => $baseUrl . '/politika-privatnosti',
                'lastmod' => now()->toDateString(),
                'changefreq' => 'yearly',
                'priority' => '0.3',
            ],
            [
                'loc' => $baseUrl . '/uslovi-koriscenja',
                'lastmod' => now()->toDateString(),
                'changefreq' => 'yearly',
                'priority' => '0.3',
            ],
            [
                'loc' => $baseUrl . '/politika-kolacica',
                'lastmod' => now()->toDateString(),
                'changefreq' => 'yearly',
                'priority' => '0.3',
            ],
        ];

        $kategorije = Kategorija::query()->select('kategorija_slug')
            ->get()
            ->map(function ($kategorija) use ($baseUrl) {
                return [
                    'loc' => $baseUrl . '/' . $kategorija->kategorija_slug,
                    'lastmod' => now()->toDateString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.9',
                ];
            })
            ->toArray();

        $tekstovi = Tekst::select('slug', 'tekst.updated_at', 'kategorijaid', 'tekstid')
            ->with('kategorija:kategorijaid,kategorija_slug')
            ->where('is_published', 1)
            ->get();
        $tekstovi = $tekstovi
            ->map(function ($tekst) use ($baseUrl) {
                return [
                    'loc' => $baseUrl . '/' . $tekst->kategorija->kategorija_slug . '/' . $tekst->slug,
                    'lastmod' => optional($tekst->updated_at)->toDateString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.8',
                ];
            })
            ->toArray();

        $predstave = Predstava::query()
            ->where('u_arhivi', 0)
            ->select('predstava_slug', 'updated_at')
            ->get()
            ->map(function ($predstava) use ($baseUrl) {
                return [
                    'loc' => $baseUrl . '/predstave/' . $predstava->predstava_slug,
                    'lastmod' => optional($predstava->updated_at)->toDateString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.8',
                ];
            })
            ->toArray();

        $pozorista = Pozoriste::query()
            ->where('is_deleted', 0)
            ->select('pozoriste_slug', 'updated_at')
            ->get()
            ->map(function ($pozoriste) use ($baseUrl) {
                return [
                    'loc' => $baseUrl . '/pozorista/' . $pozoriste->pozoriste_slug,
                    'lastmod' => optional($pozoriste->updated_at)->toDateString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.7',
                ];
            })
            ->toArray();

        $festivali = Festival::query()
            ->where('is_deleted', 0)
            ->select('festival_slug', 'updated_at')
            ->get()
            ->map(function ($festival) use ($baseUrl) {
                return [
                    'loc' => $baseUrl . '/festivali/' . $festival->festival_slug,
                    'lastmod' => optional($festival->updated_at)->toDateString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.7',
                ];
            })
            ->toArray();

        $urls = array_merge($staticPages, $kategorije, $tekstovi, $predstave, $pozorista, $festivali);

        $xml = view('sitemap', compact('urls'))->render();

        return response($xml, 200)
            ->header('Content-Type', 'application/xml');
    }
}

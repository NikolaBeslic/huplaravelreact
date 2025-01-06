<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Autor;
use App\Models\Festival;
use App\Http\Resources\KategorijaResource;
use App\Http\Resources\TekstResource;
use App\Models\Kategorija;
use App\Models\Pozoriste;
use App\Models\Tekst;
use App\Models\Hupikon;
use Dotenv\Exception\ValidationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException as ValidationValidationException;
use JsonSerializable;
use stdClass;

class TekstoviController extends Controller
{

    public function __construct()
    {

        $this->middleware('auth:admin')->only(['create', 'store', 'edit', 'adminshow', 'update', 'delete']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($kategorija_slug)
    {        //

        $kategorija = Kategorija::where('kategorija_slug', $kategorija_slug)->firstOrFail();
        $kategIds = $this->getAllKategIds($kategorija);
        //return dd($kategIds);        
        $tekstovi = Tekst::whereIn('kategorijaid', $kategIds)->where('is_published', 1)->where('is_deleted', 0)->orderBy('published_at', 'desc')->get();
        return view('tekstovi.index', ['tekstovi' => $tekstovi, 'kategorija' => $kategorija]);
    }

    public function search(Request $request)
    {
        $inputSearch = $request['inputSearch'];
        $result = Tekst::whereRaw("MATCH(naslov, sadrzaj) AGAINST(? IN BOOLEAN MODE)", [$inputSearch])->with('kategorija')->get();
        return json_encode($result);
    }

    public function getTrendingPosts()
    {
        $tekstovi = Tekst::with(['kategorija'])->inRandomOrder()->take(6)->get();
        return json_encode($tekstovi);
    }

    public function getRelatedPosts($tekstid)
    {
        $tekst = Tekst::where('tekstid', $tekstid)->firstOrFail();
        $autoriTeksta = $tekst->autori->pluck('autorid');
        $predstaveTeksta = $tekst->predstave->pluck('predstavaid');
        $pozoristaTeksta = $tekst->pozorista->pluck('pozoristeid');
        ////$istaKategorija = Tekst::where('tekstid', '<>', $tekst->tekstid)->where('kategorijaid', $tekst->kategorijaid)->take(5)->get();
        if ($tekst->festival)
            $istiFestival = Tekst::where('tekstid', '<>', $tekst->tekstid)->where('festivalid', $tekst->festivalid)->with('kategorija')->take(3)->get(); //0
        if ($tekst->autori)
            $istiAutori = Tekst::where('tekstid', '<>', $tekst->tekstid)->whereHas('autori', function ($query) use ($autoriTeksta) {
                $query->whereIn('autor_tekst.autorid', $autoriTeksta);
            })->with('kategorija')->orderBy('published_at', 'desc')->take(5)->get();  // 5
        if ($tekst->predstave)
            $istePredstave = Tekst::where('tekstid', '<>', $tekst->tekstid)->whereHas('predstave', function ($query) use ($predstaveTeksta) {
                $query->whereIn('tekst_predstava.predstavaid', $predstaveTeksta);
            })->with('kategorija')->orderBy('published_at', 'desc')->take(5)->get(); // 3 - propadaliste
        if ($tekst->pozorista)
            $istaPozorista = Tekst::where('tekstid', '<>', $tekst->tekstid)->whereHas('pozorista', function ($query) use ($pozoristaTeksta) {
                $query->whereIn('tekst_pozoriste.pozoristeid', $pozoristaTeksta);
            })->with('kategorija')->orderBy('published_at', 'desc')->take(5)->get();  //2

        $relatedPosts = new Collection();
        if (isset($istiFestival))
            $relatedPosts = $relatedPosts->merge($istiFestival);
        if (isset($istiAutori))
            $relatedPosts = $relatedPosts->merge($istiAutori);
        if (isset($istePredstave))
            $relatedPosts = $relatedPosts->merge($istePredstave);
        if (isset($istaPozorista))
            $relatedPosts = $relatedPosts->merge($istaPozorista);

        $relatedPosts = $relatedPosts->shuffle();

        // $relatedPosts = (object) [
        //     'istiFestival' => $istiFestival,
        //     'istePredstave' => $istePredstave,
        //     'istaPozorista' => $istaPozorista
        // ];
        // $relatedPosts = $relatedPosts->merge($istiAutori);  

        return response()->json($relatedPosts->values());
    }

    public function getSliderPosts()
    {
        $tekstovi = Tekst::where('na_slajderu', 1)->get();
        return response()->json($tekstovi);
    }

    public function getAllKategIds($kategorija)
    {
        $ids = Kategorija::select('kategorijaid')->where('kategorijaid', $kategorija->kategorijaid)->orWhere('parent_kategorija', $kategorija->kategorijaid)->get();
        return $ids->toArray();
    }

    public function getPosts()
    {
        $intervjui = Tekst::select('tekstid', 'naslov', 'slug', 'uvod', 'tekst_photo', 'kategorijaid', 'na_slajderu', 'created_at')
            ->with('kategorija')
            ->with([
                'predstave' => function ($query) {
                    $query->select('naziv_predstave', 'predstava_slug');
                }
            ])
            ->with([
                'autori' => function ($query) {
                    $query->select('ime_autora', 'autor_slug', 'url_slike');
                }
            ])
            ->where('kategorijaid', 2)
            ->orderBy('created_at', 'desc')
            ->take(10);
        $recenzije = Tekst::select('tekstid', 'naslov', 'slug', 'uvod', 'tekst_photo', 'kategorijaid', 'na_slajderu', 'created_at')
            ->where('kategorijaid', 4)
            ->with('kategorija')
            ->with([
                'predstave' => function ($query) {
                    $query->select('naziv_predstave', 'predstava_slug');
                }
            ])
            ->with([
                'autori' => function ($query) {
                    $query->select('ime_autora', 'autor_slug', 'url_slike');
                }
            ])
            ->orderBy('created_at', 'desc')
            ->take(10);
        $vesti = Tekst::select('tekstid', 'naslov', 'slug', 'uvod', 'tekst_photo', 'kategorijaid', 'na_slajderu', 'created_at')
            ->where('kategorijaid', 1)
            ->with('kategorija')
            ->with([
                'predstave' => function ($query) {
                    $query->select('naziv_predstave', 'predstava_slug');
                }
            ])
            ->with([
                'autori' => function ($query) {
                    $query->select('ime_autora', 'autor_slug', 'url_slike');
                }
            ])
            ->orderBy('created_at', 'desc')
            ->take(10);
        $naSlajderu  = Tekst::select('tekstid', 'naslov', 'slug', 'uvod', 'tekst_photo', 'kategorijaid', 'na_slajderu', 'created_at')
            ->where('na_slajderu', 1)
            ->with('kategorija')
            ->with([
                'predstave' => function ($query) {
                    $query->select('naziv_predstave', 'predstava_slug');
                }
            ])
            ->with([
                'autori' => function ($query) {
                    $query->select('ime_autora', 'autor_slug', 'url_slike');
                }
            ])
            ->orderBy('created_at', 'desc')
            ->take(5);
        $hupkast = Tekst::select('tekstid', 'naslov', 'slug', 'uvod', 'tekst_photo', 'kategorijaid', 'na_slajderu', 'created_at')
            ->where('kategorijaid', 11)
            ->with('kategorija')
            ->with([
                'autori' => function ($query) {
                    $query->select('ime_autora', 'autor_slug', 'url_slike');
                }
            ])
            ->orderBy('created_at', 'desc');


        $tekstovi = $intervjui->union($recenzije)->union($vesti)->union($naSlajderu)->orderBy('created_at', 'desc')->union($hupkast)->get();
        // $result = TekstResource::collection($tekstovi);
        $result = json_encode($tekstovi);
        return $result;
    }



    public function getCategoryPosts($kategorija_slug)
    {
        $kategorija = Kategorija::where('kategorija_slug', $kategorija_slug)->with('tekstovi')->firstOrFail();
        $katIds = $this->getAllKategIds($kategorija);
        $tekstovi = Tekst::whereIn('kategorijaid', $katIds)->where('is_published', 1)->with('kategorija')->orderBy('published_at', 'desc')->paginate(8);
        $kategorija->setRelation('tekstovi', $tekstovi);
        return json_encode($kategorija);
        // $result = KategorijaResource::make($kategorija);
        // return $result;
    }

    public function getSinglePost($tekst_slug)
    {
        $tekst = Tekst::where('slug', $tekst_slug)
            ->with('kategorija')
            ->with([
                'autori' => function ($query) {
                    $query->select('ime_autora', 'autor_slug', 'biografija', 'url_slike');
                }
            ])
            ->with([
                'predstave' => function ($query) {
                    $query->select('naziv_predstave', 'predstava_slug', 'plakat');
                }
            ])
            ->with(['pozorista' => function ($query) {
                $query->select('naziv_pozorista', 'pozoriste_slug', 'url_logo');
            }])
            ->with('tagovi')
            ->with('festival')
            ->with('hupikon')
            ->with('hupkast.linkovi')
            ->firstOrFail();
        //return TekstResource::make($tekst);
        return json_encode($tekst);
    }

    public function getIntervju($slug)
    {
        return json_encode(Tekst::where('slug', $slug)->get());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($kategorija)
    {
        $kategorija = Kategorija::where('kategorija_slug', $kategorija)->firstOrFail();

        $pozorista = Pozoriste::select('pozoristeid', 'naziv_pozorista')->orderBy('naziv_pozorista')->get();
        $autori = Autor::select('autorid', 'ime_autora')->where('status_autora', 1)->orderBy('ime_autora')->get();
        $festivali = Festival::select('festivalid', 'naziv_festivala')->orderBy('festivalid', 'desc')->get();
        $tagovi = Tag::select('tagid', 'tag_naziv')->orderBy('tag_naziv')->get();

        $path = storage_path() . "/json/predstave-basic.json";
        $predstave  = json_decode(file_get_contents($path), true);

        if ($kategorija->kategorijaid == 5) {
            return view('hupikoni.create', [
                'kategorija' => $kategorija,
                'autori' => $autori,
                'predstave' => $predstave,
                'tagovi' => $tagovi
            ]);
        }

        return view('tekstovi.create', [
            'kategorija' => $kategorija,
            'pozorista' => $pozorista,
            'autori' => $autori,
            'festivali' => $festivali,
            'predstave' => $predstave,
            'tagovi' => $tagovi,
            'action' => 'create'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'kategorijaid',
            'naslov' => 'required',
            'slug' => 'required|unique:tekst',
            'uvod' => 'required|max:280 ',
            'sadrzaj' => 'required',
            //'thumbnail' => 'required'
        ]);

        $tekst = new Tekst();

        if ($request->has('objavi')) {
            $tekst->is_published = 1;
        }

        if ($request->has('sacuvaj')) {
            $tekst->is_published = 0;
        }

        if ($request->pozoriste > 0) $tekst->pozoristeid = $request->pozoriste;
        if ($request->festival > 0)  $tekst->festivalid = $request->festival;
        if ($request->istakni) $tekst->na_slajderu = 1;

        $tekst->kategorijaid = $request->kategorijaid;
        $tekst->naslov = $request->naslov;
        $tekst->slug = $request->slug;
        $tekst->uvod = $request->uvod;
        $tekst->tekst_photo = '/slike/vesti/' . $request->thumbnail;
        $tekst->sadrzaj = $request->sadrzaj;


        // autor i tagovi su posle Save-a da bi dobili Id teksta
        if ($tekst->save()) {
            if ($request->kategorijaid == 5) {
                $this->dodajHupikon($tekst->tekstid, $request);
            }
            if ($request->autori > 0) {
                $tekst->autori()->attach($request->autori);
            }
            if ($request->predstave > 0) {
                $tekst->predstave()->attach($request->predstave);
            }
            if ($request->tagovi > 0) {
                $tekst->tagovi()->attach($request->tagovi);
            }
            return redirect()->route('admin.dashboard')->with('success', 'Uspesno ubacen tekst');
        } else {
            return back()->with('error', 'Neko sranje se desilo. Proveri sta si uradila. Ako se ponavlja, cimaj Nikolu');
        }

        //return dd($tekst);
    }
    public function dodajHupikon($tekstid, $request)
    {
        $request->validate([
            'sagovornik' => 'required',
        ]);

        $hupikon = new HuPikon();
        $hupikon->tekstid = $tekstid;
        $hupikon->fill($request->all());
        $hupikon->hupikon_slug = $request->slug;
        $hupikon->autorid = 1;
        $hupikon->save();
    }

    public function adminindex()
    {
        $tekstovi = Tekst::orderBy('na_slajderu', 'desc')->orderBy('created_at', 'desc')->get();
        // return view('tekstovi.adminindex', ['tekstovi' => $tekstovi]);
        $result = TekstResource::collection($tekstovi);
        return $result;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($kategorija, $slug)
    {
        //
        $tekst = Tekst::where(['slug' => $slug])->firstOrFail();
        $sidebar = vratiSidebar($tekst);

        return view(
            'tekstovi.show2',
            [
                'tekst' => $tekst,
                'sidebar' => $sidebar,
            ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($slug)
    {
        //
        $pozorista = Pozoriste::select('pozoristeid', 'naziv_pozorista')->orderBy('naziv_pozorista')->get();
        $autori = Autor::select('autorid', 'ime_autora')->orderBy('ime_autora')->get();
        $festivali = Festival::select('festivalid', 'naziv_festivala')->orderBy('festivalid', 'desc')->get();

        $path = storage_path() . "/json/predstave-basic.json";
        $predstave  = json_decode(file_get_contents($path), true);

        $tekst = Tekst::where(['slug' => $slug])->get()[0];
        return view(
            'tekstovi.adminshow',
            [
                'tekst' => $tekst,
                'pozorista' => $pozorista,
                'autori' => $autori,
                'festivali' => $festivali,
                'predstave' => $predstave,
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $slug)
    {
        // 
        $request->validate([
            'naslov' => 'required',
            'slug' => 'required',
            'uvod' => 'required|max:280 ',
        ]);

        $tekst =  Tekst::where('slug', $slug)->firstOrFail();

        if ($request->pozoriste > 0) $tekst->pozoristeid = $request->pozoriste;
        if ($request->festival > 0)  $tekst->festivalid = $request->festival;
        if ($request->istakni) $tekst->na_slajderu = 1;

        $tekst->fill($request->all());

        if ($tekst->kategorijaid == 5) {
            $request->validate([
                'sagovornik' => 'required',
            ]);
            $hupikon = HuPikon::where('tekstid', $tekst->tekstid)->firstOrFail();
            $hupikon->fill($request->all());
            if (!$hupikon->save()) {
                return back()->with('error', 'Neko sranje se desilo. Proveri sta si uradila. Ako se ponavlja, cimaj Nikolu');
            }
        }

        if ($tekst->save()) {
            return back()->with('success', 'Uspešna izmena!');
        } else {
            return back()->with('error', 'Neko sranje se desilo. Proveri sta si uradila. Ako se ponavlja, cimaj Nikolu');
        };
    }

    public function update2(Request $request)
    {

        // 
        try {
            $request->validate([
                'naslov' => 'required',
                'slug' => 'required',
                'uvod' => 'required|max:280 ',
            ]);
        } catch (ValidationException $e) {
            return response()->json($e, 422);
        }

        $tekst =  Tekst::where('tekstid', $request->tekstid)->firstOrFail();

        $tekst->fill($request->all());
        if ($tekst->save()) {
            $tekst->predstave()->sync($request->predstave);
            $tekst->tagovi()->sync($request->tagovi);
            $tekst->pozorista()->sync($request->pozorista);

            return response()->json([], 200);
        }
    }

    public function istakniTekst(Request $request)
    {
        $tekst = Tekst::where('tekstid', $request->tekstid)->with('kategorija')->firstOrFail();
        $tekst->na_slajderu = 1;
        if ($tekst->save()) {
            return response()->json(['na_slajderu' => $tekst->na_slajderu], 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        //        
        $tekst = Tekst::where('tekstid', $request->tekstid)->firstOrFail();
        $tekst->is_deleted = 1;
        if ($tekst->save()) {
            return redirect()->back()->with('success', 'Tekst uspešno obrisan');
        } else {
            return back()->with('error', 'Neko sranje se desilo. Proveri sta si uradila. Ako se ponavlja, cimaj Nikolu');
        }

        return var_dump($request->vestid);
    }

    public function uploadImage()
    {
        $imageFile = request()->file('file');
        $imagePath = '/slike/' . $imageFile->getClientOriginalName();
        $imageFile->move(public_path() . '/slike/', $imageFile->getClientOriginalName());
        return response()->json(['location' => $imagePath]);
    }

    public function store2(Request $request)
    {

        // 
        try {
            $request->validate([
                'naslov' => 'required',
                'slug' => 'required',
                'uvod' => 'required|max:280 ',
                'slika' => 'required'
            ]);
        } catch (ValidationException $e) {
            return response()->json($e, 422);
        }

        $tekst = new Tekst();
        $tekst->fill($request->all());
        /* handle photo upload */
        if ($request->file('slika')) {
            if ($request->kategorijaid) {
                $kategorija_slug = Kategorija::where('kategorijaid', $request->kategorijaid)->value('kategorija_slug');
            }
            $fileExtension = $request->file('slika')->extension();
            $fileName = $request->slug . '.' . $fileExtension;
            $path = $request->file('slika')->move(base_path() . '/react/public/slike/' . $kategorija_slug, $fileName);
            $tekst->tekst_photo = '/slike/' . $kategorija_slug . '/' . $fileName;
        }


        if ($tekst->save()) {
            $tekst->predstave()->sync($request->predstave);
            $tekst->tagovi()->sync($request->tagovi);
            $tekst->pozorista()->sync($request->pozorista);

            return response()->json([], 200);
        } else {
            return response()->json(["Error adding tekst"], 500);
        }
    }

    public function getTekstById(Request $request)
    {
        //$tekst = Tekst::with('autori')->with('predstave')->with('pozorista')->findOrFail($request->tekstid); 
        $tekst = Tekst::with(['autori', 'predstave', 'pozorista', 'tagovi', 'festival'])->findOrFail($request->tekstid);
        return json_encode($tekst);
    }

    public function getAllHuPkast()
    {
        $hupkast = Tekst::where('kategorijaid', 11)->where('is_published', 1)->orderBy('published_at', 'desc')->get();
        return json_encode($hupkast);
    }

    public function getSingleHuPkast($hupkast_slug)
    {
        $hupkast = Tekst::where('slug', $hupkast_slug)->where('is_published', 1)->with('hupkast.linkovi')->with('autori')->firstOrFail();
        return json_encode($hupkast);
    }

    public function getAllHupikon()
    {
        $hupikons = Tekst::where('kategorijaid', 5)->where('is_published', 1)->with('hupikon')->orderBy('published_at', 'desc')->get();
        return json_encode($hupikons);
    }

    public function adminGetTekstoviZaNaslovnu()
    {
        $tekstovi = Tekst::with('kategorija')->orderBy('created_at', 'desc')->take(10)->get();
        return json_encode($tekstovi);
    }
}

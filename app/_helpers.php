<?php

use App\Models\Predstava;
use App\Models\Tekst;

if (!function_exists('prikaziTekstove')) {
  function prikaziTekstove($kategorija)
  {
    $uslov = ['kategorijaid' => $kategorija->kategorijaid, 'is_published' => 1];
    $tekstovi = Tekst::where($uslov)->orderBy('published_at', 'desc')->paginate(21)->onEachSide(0);

    // $tekstovi = Tekst::select('naslov', 'slug', 'tekst_photo', 'uvod', 'published_at')->where($uslov)->orderBy('published_at', 'desc')->paginate(21)->onEachSide(0);

    return view('tekstovi.index', ['kategorija' => $kategorija, 'tekstovi' => $tekstovi]);
    // return view('intervjui.index', ['intervjui' => $intervjui]); ---- klasicno prosledjivanju svakom view-u za svaku kategoriju posebno

  }
}

if (!function_exists('vratiSidebar')) {
  function vratiSidebar($tekst)
  {
    $uslov = ['is_published' => 1, 'is_deleted' => 0];

    // najnoviji 
    // $novi = Tekst::select('naslov', 'slug', 'tekst_photo')->where($uslov)->where('tekstid', '<>', $tekst->tekstid)->orderBy('published_at', 'desc')->take(4)->get();
    $novi = Tekst::where($uslov)->where('tekstid', '<>', $tekst->tekstid)->orderBy('published_at', 'desc')->take(4)->get();

    // povezani preko kategorije
    $povezaniTekstoviKategorija = Tekst::where('kategorijaid', $tekst->kategorijaid)->where($uslov)->where('tekstid', '<>', $tekst->tekstid)->orderBy('published_at', 'desc')->take(4)->get();

    // povezani preko predstave
    $povezaniTekstoviPredstava = null;
    if ($tekst->predstave()) {
      $predstave = $tekst->predstave()->pluck('tekst_predstava.predstavaid');

      $povezaniTekstoviPredstava = Tekst::with('predstave')->where('is_published', 1)->where('is_deleted', 0)->where('tekstid', '<>', $tekst->tekstid)
        ->whereHas('predstave', function ($query) use ($predstave) {
          $query->whereIn('tekst_predstava.predstavaid', $predstave);
        })->get();
    }

    // povezani preko autora
    $povezaniTekstoviAutor = null;
    if ($tekst->autori()) {
      $autori = $tekst->autori()->pluck('autor_tekst.autorid');

      $povezaniTekstoviAutor = Tekst::with('autori')->where('is_published', 1)->where('is_deleted', 0)->where('tekstid', '<>', $tekst->tekstid)
        ->whereHas('autori', function ($query) use ($autori) {
          $query->whereIn('autor_tekst.autorid', $autori);
        })->get();
    }

    // TO DO: kada se tekstovi i pozorista i festivali povezu kao i tekstovi i predstave uraditi kao iznad

    // povezani preko festivala
    $povezaniTekstoviFestival = null;
    if ($tekst->festivalid) {
      $povezaniTekstoviFestival = Tekst::where('festivalid', $tekst->festivalid)->where('is_published', 1)->where('is_deleted', 0)->where('tekstid', '<>', $tekst->tekstid)->get();
    }

    // povezani preko pozorista
    $povezaniTekstoviPozoriste = null;
    if ($tekst->pozoristeid) {
      $povezaniTekstoviPozoriste = Tekst::where('pozoristeid', $tekst->pozoristeid)->where('is_published', 1)->where('is_deleted', 0)->where('tekstid', '<>', $tekst->tekstid)->get();
    }

    $najcitaniji = Tekst::where($uslov)->where('tekstid', '<>', $tekst->tekstid)->orderBy('naslov', 'asc')->take(4)->get();

    //$premijere = Predstava::where('premijera', '>=', date('Y-m-d'))->orderBy('premijera')->take(4)->get();
    $premijere = Predstava::orderBy('premijera', 'desc')->take(4)->get();

    $sidebar = new stdClass();
    $sidebar->novi = $novi;
    $sidebar->najcitaniji = $najcitaniji;
    $sidebar->premijere = $premijere;
    //$sidebar = array('novi' => $novi, 'najcitaniji' => $najcitaniji, 'premijere' => $premijere);

    if ($povezaniTekstoviKategorija != null) {
      //$sidebar['tekstoviKategorija'] = $povezaniTekstoviKategorija;
      $sidebar->tekstoviKategorija = $povezaniTekstoviKategorija;
    }

    if ($povezaniTekstoviAutor != null) {
      $sidebar->tekstoviAutora = $povezaniTekstoviAutor;
    }

    if ($povezaniTekstoviPredstava != null) {
      $sidebar->tekstoviPredstava = $povezaniTekstoviPredstava;
    }

    if ($povezaniTekstoviPozoriste != null) {
      $sidebar->tekstoviPozoriste = $povezaniTekstoviPozoriste;
    }

    if ($povezaniTekstoviFestival != null) {
      $sidebar->tekstoviFestival = $povezaniTekstoviFestival;
    }

    // TO DO: prebaciti dodavanje u sidebar tamo gde se dohvataju tekstovi


    return $sidebar;
  }
}

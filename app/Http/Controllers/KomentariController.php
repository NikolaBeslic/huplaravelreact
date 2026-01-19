<?php

namespace App\Http\Controllers;

use App\Models\Komentar;
use Illuminate\Http\Request;
use Dotenv\Exception\ValidationException;

class KomentariController extends Controller
{

    public function getAllKomentari()
    {
        $komentari = Komentar::with(['korisnik', 'predstava'])->orderBy('created_at', 'desc')->get();
        return response()->json($komentari);
    }

    public function odobriKomentar($komentarid)
    {
        $komentar = Komentar::with(['korisnik', 'predstava'])->find($komentarid);
        if (!$komentar) {
            return response()->json(['message' => 'Komentar nije pronađen'], 404);
        }

        $komentar->statuskomentaraid = 2; // Postavljanje statusa na "Odobren"
        $komentar->save();

        return response()->json(['message' => 'Komentar je uspešno odobren', 'komentar' => $komentar]);
    }

    public function deleteKomentar($komentarid)
    {
        $komentar = Komentar::find($komentarid);
        if (!$komentar) {
            return response()->json(['message' => 'Komentar nije pronađen'], 404);
        }

        if ($komentar->delete()) {
            $unnapprovedCommentsCount = Komentar::where('statuskomentaraid', 1)->count();
        }

        return response()->json(['message' => 'Komentar je uspešno obrisan', 'unnapprovedCommentsCount' => $unnapprovedCommentsCount ?? null]);
    }

    public function getUnapprovedCommentsCount()
    {
        $count = Komentar::where('statuskomentaraid', 1)->count();
        return response()->json($count);
    }
}

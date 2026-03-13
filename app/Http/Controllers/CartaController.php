<?php

namespace App\Http\Controllers;

use App\Models\Artista;
use App\Models\Carta;
use App\Models\Collezione;
use App\Models\CollezioneRarita;
use App\Models\CollezioneTipologia;
use Illuminate\Http\Request;

class CartaController extends Controller
{
    public function index(Request $request)
    {
        $query = Carta::with(['collezione', 'artista', 'raritas', 'versioni', 'tipologie']);

        if ($request->filled('collezione')) {
            $query->where('col_id_collezione', $request->collezione);
        }

        if ($request->filled('artista')) {
            $query->where('art_id_artista', $request->artista);
        }

        if ($request->filled('cerca')) {
            $term = '%' . $request->cerca . '%';
            $query->where(fn($q) => $q->where('titolo', 'ilike', $term)
                ->orWhereRaw("CAST(numero AS TEXT) ILIKE ?", [$term]));
        }

        if ($request->filled('rarita')) {
            $query->whereHas('raritas', fn ($q) => $q->where('collezione_rarita.id_collezione_rarita', $request->rarita));
        }

        if ($request->filled('tipologia')) {
            $query->whereHas('tipologie', fn($q) => $q->where('id_collezione_tipologia', $request->tipologia));
        }

        $pagination = $query->orderBy('id_carta')->paginate(12);
        $carte      = $pagination->getCollection();

        $collezioneSelezionata = $request->filled('collezione')
            ? Collezione::find($request->collezione)
            : null;

        $collezioni = Collezione::orderBy('nome')->get();

        // Rarità, tipologie e artisti: se una collezione è selezionata, solo i suoi;
        // altrimenti tutti quelli presenti nel sistema.
        if ($collezioneSelezionata) {
            $rarita    = CollezioneRarita::where('col_id_collezione', $collezioneSelezionata->id_collezione)->orderBy('nome')->get();
            $tipologie = CollezioneTipologia::where('col_id_collezione', $collezioneSelezionata->id_collezione)->orderBy('nome')->get();
            $artisti   = Artista::whereHas('carte', fn($q) => $q->where('col_id_collezione', $collezioneSelezionata->id_collezione))->orderBy('nominativo')->get();
        } else {
            $rarita    = CollezioneRarita::orderBy('nome')->get();
            $tipologie = CollezioneTipologia::orderBy('nome')->get();
            $artisti   = Artista::orderBy('nominativo')->get();
        }

        $collezione = $collezioneSelezionata;

        if ($request->expectsJson()) {
            return response()->json([
                'html'     => view('carte._cards', ['carte' => $carte])->render(),
                'hasMore'  => $pagination->hasMorePages(),
                'nextPage' => $pagination->currentPage() + 1,
            ]);
        }

        return view('carte.index', compact('carte', 'pagination', 'rarita', 'tipologie', 'collezione', 'collezioni', 'artisti'));
    }

    public function show($id)
    {
        $carta = Carta::with(['collezione', 'artista', 'raritas', 'versioni', 'tipologie'])->findOrFail($id);
        return view('carte.show', compact('carta'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Artista;
use App\Models\Carta;
use App\Models\Collezione;
use App\Models\CollezioneRarita;
use App\Models\CollezioneTipologia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartaController extends Controller
{
    public function index(Request $request)
    {
        $invalidFilters = [];

        $collezioneId = $this->parseFilterId($request->query('collezione'));
        if ($request->filled('collezione') && $collezioneId === null) {
            $invalidFilters[] = 'collezione';
        }

        $artistaId = $this->parseFilterId($request->query('artista'));
        if ($request->filled('artista') && $artistaId === null) {
            $invalidFilters[] = 'artista';
        }

        $raritaId = $this->parseFilterId($request->query('rarita'));
        if ($request->filled('rarita') && $raritaId === null) {
            $invalidFilters[] = 'rarita';
        }

        $tipologiaId = $this->parseFilterId($request->query('tipologia'));
        if ($request->filled('tipologia') && $tipologiaId === null) {
            $invalidFilters[] = 'tipologia';
        }

        $searchTerm = trim((string) $request->query('cerca', ''));
        if (mb_strlen($searchTerm) > 100) {
            $searchTerm = mb_substr($searchTerm, 0, 100);
            $invalidFilters[] = 'cerca';
        }

        $collezioneSelezionata = $collezioneId ? Collezione::find($collezioneId) : null;
        if ($collezioneId && !$collezioneSelezionata) {
            $collezioneId = null;
            $invalidFilters[] = 'collezione';
        }

        if ($collezioneSelezionata) {
            if ($raritaId && !CollezioneRarita::where('id_collezione_rarita', $raritaId)
                ->where('col_id_collezione', $collezioneSelezionata->id_collezione)
                ->exists()) {
                $raritaId = null;
                $invalidFilters[] = 'rarita';
            }

            if ($tipologiaId && !CollezioneTipologia::where('id_collezione_tipologia', $tipologiaId)
                ->where('col_id_collezione', $collezioneSelezionata->id_collezione)
                ->exists()) {
                $tipologiaId = null;
                $invalidFilters[] = 'tipologia';
            }

            if ($artistaId && !Artista::where('id_artista', $artistaId)
                ->whereHas('carte', fn($q) => $q->where('col_id_collezione', $collezioneSelezionata->id_collezione))
                ->exists()) {
                $artistaId = null;
                $invalidFilters[] = 'artista';
            }
        }

        $invalidFilters = array_values(array_unique($invalidFilters));
        if (!empty($invalidFilters) && !$request->expectsJson()) {
            $params = [];
            if ($collezioneId) {
                $params['collezione'] = $collezioneId;
            }
            if ($artistaId) {
                $params['artista'] = $artistaId;
            }
            if ($raritaId) {
                $params['rarita'] = $raritaId;
            }
            if ($tipologiaId) {
                $params['tipologia'] = $tipologiaId;
            }
            if ($searchTerm !== '') {
                $params['cerca'] = $searchTerm;
            }

            return redirect()->route('home', $params)
                ->with('warning', 'Alcuni filtri non validi sono stati rimossi automaticamente.');
        }

        $query = Carta::with(['collezione', 'artista', 'raritas', 'versioni', 'tipologie']);

        if ($collezioneId) {
            $query->where('col_id_collezione', $collezioneId);
        }

        if ($artistaId) {
            $query->where('art_id_artista', $artistaId);
        }

        if ($searchTerm !== '') {
            $term = '%' . mb_strtolower($searchTerm, 'UTF-8') . '%';
            $numeroCast = DB::connection()->getDriverName() === 'pgsql'
                ? 'CAST(numero AS TEXT)'
                : 'CAST(numero AS CHAR)';

            $query->where(fn($q) => $q->whereRaw('LOWER(titolo) LIKE ?', [$term])
                ->orWhereRaw("{$numeroCast} LIKE ?", [$term]));
        }

        if ($raritaId) {
            $query->whereHas('raritas', fn($q) => $q->where('collezione_rarita.id_collezione_rarita', $raritaId));
        }

        if ($tipologiaId) {
            $query->whereHas('tipologie', fn($q) => $q->where('collezione_tipologia.id_collezione_tipologia', $tipologiaId));
        }

        $pagination = $query->orderBy('id_carta')->paginate(12);
        $carte      = $pagination->getCollection();

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

    private function parseFilterId(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!is_scalar($value)) {
            return null;
        }

        $stringValue = trim((string) $value);
        if ($stringValue === '' || !ctype_digit($stringValue)) {
            return null;
        }

        $intValue = (int) $stringValue;
        return $intValue > 0 ? $intValue : null;
    }

    public function show($id)
    {
        $carta = Carta::with(['collezione', 'artista', 'raritas', 'versioni', 'tipologie'])->findOrFail($id);
        return view('carte.show', compact('carta'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Carta;
use App\Models\Collezione;
use App\Models\Artista;
use App\Models\CollezioneRarita;
use App\Models\CollezioneTipologia;
use App\Models\VersioneCollezione;
use App\Services\CartaImageService;
use Illuminate\Http\Request;

class AdminCollezioneController extends Controller
{
    public function index()
    {
        $collezioni = Collezione::withCount('carte')->orderBy('data_uscita', 'desc')->paginate(20);
        return view('admin.collezioni.index', compact('collezioni'));
    }

    public function show($id)
    {
        $collezione = Collezione::with('carte.artista', 'carte.raritas', 'carte.tipologie')->findOrFail($id);
        $carte      = $collezione->carte()->with(['artista', 'raritas', 'tipologie'])->paginate(20);
        $rarita     = $collezione->rarita()->orderBy('nome')->get();
        $tipologie  = $collezione->tipologie()->orderBy('nome')->get();
        $versioni   = $collezione->versioni()->orderBy('nome')->get();
        return view('admin.collezioni.show', compact('collezione', 'carte', 'rarita', 'tipologie', 'versioni'));
    }

    public function create()
    {
        return view('admin.collezioni.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome'         => 'required|string|max:255',
            'descrizione'  => 'nullable|string',
            'numero_carte' => 'required|integer|min:1',
            'data_uscita'  => 'nullable|date',
        ]);

        $collezione = Collezione::create($request->only('nome', 'descrizione', 'numero_carte', 'data_uscita'));

        return redirect()->route('admin.collezioni.show', $collezione->id_collezione)
            ->with('success', 'Collezione creata con successo.');
    }

    public function edit($id)
    {
        $collezione = Collezione::findOrFail($id);
        return view('admin.collezioni.edit', compact('collezione'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nome'         => 'required|string|max:255',
            'descrizione'  => 'nullable|string',
            'numero_carte' => 'required|integer|min:1',
            'data_uscita'  => 'nullable|date',
        ]);

        $collezione = Collezione::findOrFail($id);
        $collezione->update($request->only('nome', 'descrizione', 'numero_carte', 'data_uscita'));

        return redirect()->route('admin.collezioni.show', $id)
            ->with('success', 'Collezione aggiornata con successo.');
    }

    public function destroy($id)
    {
        $collezione = Collezione::findOrFail($id);
        $collezione->delete();

        return redirect()->route('admin.collezioni.index')
            ->with('success', 'Collezione eliminata con successo.');
    }

    public function createCarta($collezioneId)
    {
        $collezione = Collezione::findOrFail($collezioneId);
        $artisti    = Artista::orderBy('cognome')->get();
        $rarita     = CollezioneRarita::where('col_id_collezione', $collezioneId)->orderBy('nome')->get();
        $tipologie  = CollezioneTipologia::where('col_id_collezione', $collezioneId)->orderBy('nome')->get();
        $versioni   = VersioneCollezione::where('col_id_collezione', $collezioneId)->orderBy('nome')->get();

        return view('admin.carte.create', compact('collezione', 'artisti', 'rarita', 'tipologie', 'versioni'));
    }

    // Salva la nuova carta
    public function storeCarta(Request $request, $collezioneId, CartaImageService $imageService)
    {
        $request->validate([
            'titolo'                   => 'required|string|max:255',
            'descrizione'              => 'nullable|string',
            'art_id_artista'           => 'nullable|exists:artista,id_artista',
            'numero'                   => 'nullable|integer|min:1',
            'prefisso'                 => 'nullable|string|max:20',
            'rar_id_collezione_rarita' => 'nullable|exists:collezione_rarita,id_collezione_rarita',
            'tipologia_ids'            => 'nullable|array',
            'tipologia_ids.*'          => 'exists:collezione_tipologia,id_collezione_tipologia',
            'versione_ids'             => 'nullable|array',
            'versione_ids.*'           => 'exists:versione_collezione,id_versione',
            'immagine'                 => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        $collezione  = Collezione::findOrFail($collezioneId);
        $immagineUrl = $request->hasFile('immagine')
            ? $imageService->store($request->file('immagine'), $collezione)
            : null;

        $carta = Carta::create([
            'col_id_collezione' => $collezioneId,
            'titolo'            => $request->titolo,
            'descrizione'       => $request->descrizione,
            'art_id_artista'    => $request->art_id_artista,
            'numero'            => $request->numero,
            'prefisso'          => $request->prefisso ?: null,
            'immagine_url'      => $immagineUrl,
        ]);

        // Sync rarity pivot (single optional rarity)
        $raritaId = $request->filled('rar_id_collezione_rarita') ? [$request->rar_id_collezione_rarita] : [];
        $carta->raritas()->sync($raritaId);

        // Sync tipologie pivot
        $carta->tipologie()->sync($request->input('tipologia_ids', []));

        // Sync alternative versions pivot
        $carta->versioni()->sync($request->input('versione_ids', []));

        return redirect()->route('admin.collezioni.show', $collezioneId)
            ->with('success', 'Carta aggiunta con successo.');
    }
}

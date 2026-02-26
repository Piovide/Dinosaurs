<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Carta;
use App\Models\Collezione;
use App\Models\Artista;
use App\Models\Dizionario;
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
        $collezione = Collezione::with('carte.artista', 'carte.rarita', 'carte.tipo')->findOrFail($id);
        $carte = $collezione->carte()->with(['artista', 'rarita', 'tipo'])->paginate(20);
        return view('admin.collezioni.show', compact('collezione', 'carte'));
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

    // Form per aggiungere una carta a questa collezione
    public function createCarta($collezioneId)
    {
        $collezione = Collezione::findOrFail($collezioneId);
        $artisti    = Artista::orderBy('cognome')->get();
        $rarita     = Dizionario::where('categoria', 'rarita')->where('stato', 1)->get();
        $tipi       = Dizionario::where('categoria', 'tipo')->where('stato', 1)->get();

        return view('admin.carte.create', compact('collezione', 'artisti', 'rarita', 'tipi'));
    }

    // Salva la nuova carta
    public function storeCarta(Request $request, $collezioneId, CartaImageService $imageService)
    {
        $request->validate([
            'titolo'         => 'required|string|max:255',
            'descrizione'    => 'nullable|string',
            'art_id_artista' => 'nullable|exists:artista,id_artista',
            'dnz_id_rarita'  => 'nullable|exists:dizionario,id_dizionario',
            'dnz_id_tipo'    => 'nullable|exists:dizionario,id_dizionario',
            'numero'         => 'nullable|integer|min:1',
            'immagine'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        $collezione  = Collezione::findOrFail($collezioneId);
        $immagineUrl = $request->hasFile('immagine')
            ? $imageService->store($request->file('immagine'), $collezione)
            : null;

        Carta::create([
            'col_id_collezione' => $collezioneId,
            'titolo'            => $request->titolo,
            'descrizione'       => $request->descrizione,
            'art_id_artista'    => $request->art_id_artista,
            'dnz_id_rarita'     => $request->dnz_id_rarita,
            'dnz_id_tipo'       => $request->dnz_id_tipo,
            'numero'            => $request->numero,
            'immagine_url'      => $immagineUrl,
        ]);

        return redirect()->route('admin.collezioni.show', $collezioneId)
            ->with('success', 'Carta aggiunta con successo.');
    }
}

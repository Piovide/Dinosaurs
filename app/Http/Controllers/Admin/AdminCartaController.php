<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Carta;
use App\Models\Collezione;
use App\Models\Artista;
use App\Models\Dizionario;
use App\Services\CartaImageService;
use Illuminate\Http\Request;

class AdminCartaController extends Controller
{
    public function edit($id)
    {
        $carta      = Carta::with(['artista', 'rarita', 'tipo', 'collezione'])->findOrFail($id);
        $artisti    = Artista::orderBy('cognome')->get();
        $rarita     = Dizionario::where('categoria', 'rarita')->where('stato', 1)->get();
        $tipi       = Dizionario::where('categoria', 'tipo')->where('stato', 1)->get();
        $collezioni = Collezione::orderBy('nome')->get();

        return view('admin.carte.edit', compact('carta', 'artisti', 'rarita', 'tipi', 'collezioni'));
    }

    public function update(Request $request, $id, CartaImageService $imageService)
    {
        $request->validate([
            'titolo'            => 'required|string|max:255',
            'descrizione'       => 'nullable|string',
            'col_id_collezione' => 'required|exists:collezione,id_collezione',
            'art_id_artista'    => 'nullable|exists:artista,id_artista',
            'dnz_id_rarita'     => 'nullable|exists:dizionario,id_dizionario',
            'dnz_id_tipo'       => 'nullable|exists:dizionario,id_dizionario',
            'numero'            => 'nullable|integer|min:1',
            'immagine'          => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        $carta = Carta::with('collezione')->findOrFail($id);
        $data  = $request->only('titolo', 'descrizione', 'col_id_collezione', 'art_id_artista', 'dnz_id_rarita', 'dnz_id_tipo', 'numero');

        if ($request->hasFile('immagine')) {
            $imageService->delete($carta->immagine_url);
            $collezione = Collezione::findOrFail($request->col_id_collezione);
            $data['immagine_url'] = $imageService->store($request->file('immagine'), $collezione);
        }

        $carta->update($data);

        return redirect()->route('admin.collezioni.show', $carta->col_id_collezione)
            ->with('success', 'Carta aggiornata con successo.');
    }

    public function destroy($id, CartaImageService $imageService)
    {
        $carta = Carta::findOrFail($id);
        $collezioneId = $carta->col_id_collezione;
        $imageService->delete($carta->immagine_url);
        $carta->delete();

        return redirect()->route('admin.collezioni.show', $collezioneId)
            ->with('success', 'Carta eliminata con successo.');
    }
}

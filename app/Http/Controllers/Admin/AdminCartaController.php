<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Carta;
use App\Models\Collezione;
use App\Models\Artista;
use App\Models\CollezioneRarita;
use App\Models\CollezioneTipologia;
use App\Services\CartaImageService;
use Illuminate\Http\Request;

class AdminCartaController extends Controller
{
    public function edit($id)
    {
        $carta      = Carta::with(['artista', 'rarita', 'tipologia', 'collezione'])->findOrFail($id);
        $artisti    = Artista::orderBy('cognome')->get();
        $collezioneId = $carta->col_id_collezione;
        $rarita     = CollezioneRarita::where('col_id_collezione', $collezioneId)->orderBy('nome')->get();
        $tipologie  = CollezioneTipologia::where('col_id_collezione', $collezioneId)->orderBy('nome')->get();
        $collezioni = Collezione::orderBy('nome')->get();

        return view('admin.carte.edit', compact('carta', 'artisti', 'rarita', 'tipologie', 'collezioni'));
    }

    public function update(Request $request, $id, CartaImageService $imageService)
    {
        $request->validate([
            'titolo'            => 'required|string|max:255',
            'descrizione'       => 'nullable|string',
            'col_id_collezione' => 'required|exists:collezione,id_collezione',
            'art_id_artista'    => 'nullable|exists:artista,id_artista',
            'rar_id_rarita'     => 'nullable|exists:collezione_rarita,id_collezione_rarita',
            'tip_id_tipologia'  => 'nullable|exists:collezione_tipologia,id_collezione_tipologia',
            'numero'            => 'nullable|integer|min:1',
            'immagine'          => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        $carta = Carta::with('collezione')->findOrFail($id);
        $data  = $request->only('titolo', 'descrizione', 'col_id_collezione', 'art_id_artista', 'rar_id_rarita', 'tip_id_tipologia', 'numero');

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

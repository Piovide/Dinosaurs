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

class AdminCartaController extends Controller
{
    public function edit($id)
    {
        $carta        = Carta::with(['artista', 'raritas', 'versioni', 'tipologie', 'collezione'])->findOrFail($id);
        $artisti      = Artista::orderBy('nominativo')->get();
        $collezioneId = $carta->col_id_collezione;
        $rarita       = CollezioneRarita::where('col_id_collezione', $collezioneId)->orderBy('nome')->get();
        $tipologie    = CollezioneTipologia::where('col_id_collezione', $collezioneId)->orderBy('nome')->get();
        $versioni     = VersioneCollezione::where('col_id_collezione', $collezioneId)->orderBy('nome')->get();
        $collezioni   = Collezione::orderBy('nome')->get();

        return view('admin.carte.edit', compact('carta', 'artisti', 'rarita', 'tipologie', 'versioni', 'collezioni'));
    }

    public function update(Request $request, $id, CartaImageService $imageService)
    {
        $request->validate([
            'titolo'                   => 'required|string|max:255',
            'descrizione'              => 'nullable|string',
            'col_id_collezione'        => 'required|exists:collezione,id_collezione',
            'art_id_artista'           => 'nullable|exists:artista,id_artista',
            'numero'                   => 'nullable|integer|min:1',
            'prefisso'                 => 'nullable|string|max:20',
            'suffisso'                 => 'nullable|string|max:20',
            'rar_id_collezione_rarita' => 'nullable|exists:collezione_rarita,id_collezione_rarita',
            'tipologia_ids'            => 'nullable|array',
            'tipologia_ids.*'          => 'exists:collezione_tipologia,id_collezione_tipologia',
            'versione_ids'             => 'nullable|array',
            'versione_ids.*'           => 'exists:versione_collezione,id_versione',
            'immagine'                 => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10000',
        ], [
            'immagine.max' => 'Il file immagine non deve superare 10MB.',
            'immagine.image' => 'Il file deve essere un\'immagine valida.',
            'immagine.mimes' => 'Il file deve essere in formato: JPEG, PNG, JPG, GIF o WebP.',
        ]);

        $carta = Carta::with('collezione')->findOrFail($id);
        $data  = $request->only('titolo', 'descrizione', 'col_id_collezione', 'art_id_artista', 'numero');
        $data['prefisso'] = $request->prefisso ?: null;
        $data['suffisso'] = $request->suffisso ?: null;

        if ($request->hasFile('immagine')) {
            $imageService->delete($carta->immagine_url);
            $collezione = Collezione::findOrFail($request->col_id_collezione);
            $data['immagine_url'] = $imageService->store($request->file('immagine'), $collezione);
        }

        $carta->update($data);
        // Sync rarity pivot (single optional rarity)
        $raritaId = $request->filled('rar_id_collezione_rarita') ? [$request->rar_id_collezione_rarita] : [];
        $carta->raritas()->sync($raritaId);

        // Sync tipologie pivot
        $carta->tipologie()->sync($request->input('tipologia_ids', []));

        // Sync alternative versions pivot
        $carta->versioni()->sync($request->input('versione_ids', []));

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

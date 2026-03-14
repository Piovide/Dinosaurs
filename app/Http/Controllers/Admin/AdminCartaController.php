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
use Illuminate\Support\Facades\DB;

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
        $page = $request->input('page');

        $request->validate([
            'titolo'                   => 'required|string|max:255',
            'descrizione'              => 'nullable|string',
            'col_id_collezione'        => 'required|exists:collezione,id_collezione',
            'art_id_artista'           => 'nullable|exists:artista,id_artista',
            'art_id_artista_secondario' => 'nullable|exists:artista,id_artista',
            'art_id_artista_back'      => 'nullable|exists:artista,id_artista',
            'numero'                   => 'nullable|integer|min:1',
            'prefisso'                 => 'nullable|string|max:20',
            'suffisso'                 => 'nullable|string|max:20',
            'rar_id_collezione_rarita' => 'nullable|exists:collezione_rarita,id_collezione_rarita',
            'tipologia_ids'            => 'nullable|array',
            'tipologia_ids.*'          => 'exists:collezione_tipologia,id_collezione_tipologia',
            'versione_ids'             => 'nullable|array',
            'versione_ids.*'           => 'exists:versione_collezione,id_versione',
            'immagine'                 => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10000',
            'remove_immagine'          => 'nullable|boolean',
        ], [
            'immagine.max' => 'Il file immagine non deve superare 10MB.',
            'immagine.image' => 'Il file deve essere un\'immagine valida.',
            'immagine.mimes' => 'Il file deve essere in formato: JPEG, PNG, JPG, GIF o WebP.',
        ]);

        $carta = Carta::with('collezione')->findOrFail($id);
        $data  = $request->only(
            'titolo',
            'descrizione',
            'col_id_collezione',
            'art_id_artista',
            'art_id_artista_secondario',
            'art_id_artista_back',
            'numero'
        );
        $data['prefisso'] = $request->prefisso ?: null;
        $data['suffisso'] = $request->suffisso ?: null;

        if ($request->boolean('remove_immagine') && !$request->hasFile('immagine')) {
            $imageService->delete($carta->immagine_url);
            $data['immagine_url'] = null;
        }

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

        // Sync alternative versions pivot, with optional auto-rules by rarity.
        $versioneIds = $request->input('versione_ids');
        if (is_array($versioneIds)) {
            $carta->versioni()->sync($versioneIds);
        } else {
            $versioniDaRegola = [];
            if (!empty($raritaId)) {
                $versioniDaRegola = DB::table('collezione_regola_versione_rarita')
                    ->where('col_id_collezione', (int) $request->col_id_collezione)
                    ->where('rarita_id', (int) $raritaId[0])
                    ->pluck('versione_id')
                    ->map(fn($versioneId) => (int) $versioneId)
                    ->all();
            }
            $carta->versioni()->sync($versioniDaRegola);
        }

        return redirect()->route('admin.collezioni.show', [
                'id' => $carta->col_id_collezione,
                'page' => $page,
            ])
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

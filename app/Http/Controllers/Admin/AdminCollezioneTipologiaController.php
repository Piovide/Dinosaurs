<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CollezioneTipologia;
use App\Models\Collezione;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminCollezioneTipologiaController extends Controller
{
    /**
     * Store a new tipologia. Supports JSON (inline) and regular form responses.
     */
    public function store(Request $request, $collezioneId)
    {
        $request->validate([
            'nome'            => 'required|string|max:100',
            'tipo_icona'      => 'nullable|in:file,bootstrap',
            'icona'           => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:1024',
            'icona_bootstrap' => 'nullable|string|max:100',
        ]);

        $collezione = Collezione::findOrFail($collezioneId);

        [$iconaVal, $tipoIcona] = $this->resolveIcona($request, 'tipologia', $collezione->id_collezione);

        $tipologia = CollezioneTipologia::create([
            'col_id_collezione' => $collezioneId,
            'nome'              => $request->nome,
            'icona'             => $iconaVal,
            'tipo_icona'        => $tipoIcona,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'id'         => $tipologia->id_collezione_tipologia,
                'label'      => $tipologia->nome,
                'tipo_icona' => $tipologia->tipo_icona,
                'icona_val'  => $tipologia->icona,
                'icona_url'  => $tipologia->icona_url,
            ], 201);
        }

        return back()->with('success', 'Tipologia aggiunta con successo.');
    }

    public function update(Request $request, $collezioneId, $id)
    {
        $request->validate([
            'nome'            => 'required|string|max:100',
            'tipo_icona'      => 'nullable|in:file,bootstrap',
            'icona'           => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:1024',
            'icona_bootstrap' => 'nullable|string|max:100',
        ]);

        $tipologia = CollezioneTipologia::where('col_id_collezione', $collezioneId)->findOrFail($id);

        if ($tipologia->tipo_icona === 'file' && $tipologia->icona) {
            if ($request->tipo_icona === 'bootstrap' || $request->hasFile('icona')) {
                Storage::disk('public')->delete($tipologia->icona);
            }
        }

        [$iconaVal, $tipoIcona] = $this->resolveIcona($request, 'tipologia', $collezioneId, $tipologia);

        $tipologia->nome       = $request->nome;
        $tipologia->icona      = $iconaVal;
        $tipologia->tipo_icona = $tipoIcona;
        $tipologia->save();

        return back()->with('success', 'Tipologia aggiornata.');
    }

    public function destroy($collezioneId, $id)
    {
        $tipologia = CollezioneTipologia::where('col_id_collezione', $collezioneId)->findOrFail($id);
        if ($tipologia->tipo_icona === 'file' && $tipologia->icona) Storage::disk('public')->delete($tipologia->icona);
        $tipologia->delete();

        return back()->with('success', 'Tipologia eliminata.');
    }

    /**
     * Resolve icona value and tipo_icona from the request.
     * Returns [$iconaVal, $tipoIcona].
     */
    private function resolveIcona(Request $request, string $subfolder, int $colId, $existing = null): array
    {
        $tipo = $request->input('tipo_icona');

        if ($tipo === 'bootstrap') {
            $name = trim($request->input('icona_bootstrap', ''));
            return $name ? [$name, 'bootstrap'] : [null, null];
        }

        if ($tipo === 'file' && $request->hasFile('icona')) {
            $path = $request->file('icona')->store('icone/' . $subfolder . '/' . $colId, 'public');
            return [$path, 'file'];
        }

        // Keep existing if no new icon provided
        if ($existing) {
            return [$existing->icona, $existing->tipo_icona];
        }

        return [null, null];
    }
}

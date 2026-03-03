<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CollezioneRarita;
use App\Models\Collezione;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminCollezioneRaritaController extends Controller
{
    /**
     * Store a new rarità. Supports JSON (inline) and regular form responses.
     */
    public function store(Request $request, $collezioneId)
    {
        $request->validate([
            'nome'             => 'required|string|max:100',
            'tipo_icona'       => 'nullable|in:file,bootstrap',
            'icona'            => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:1024',
            'icona_bootstrap'  => 'nullable|string|max:100',
        ]);

        $collezione = Collezione::findOrFail($collezioneId);

        [$iconaVal, $tipoIcona] = $this->resolveIcona($request, 'rarita', $collezione->id_collezione);

        $rarita = CollezioneRarita::create([
            'col_id_collezione' => $collezioneId,
            'nome'              => $request->nome,
            'icona'             => $iconaVal,
            'tipo_icona'        => $tipoIcona,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'id'         => $rarita->id_collezione_rarita,
                'label'      => $rarita->nome,
                'tipo_icona' => $rarita->tipo_icona,
                'icona_val'  => $rarita->icona,
                'icona_url'  => $rarita->icona_url,
            ], 201);
        }

        return back()->with('success', 'Rarità aggiunta con successo.');
    }

    public function update(Request $request, $collezioneId, $id)
    {
        $request->validate([
            'nome'            => 'required|string|max:100',
            'tipo_icona'      => 'nullable|in:file,bootstrap',
            'icona'           => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:1024',
            'icona_bootstrap' => 'nullable|string|max:100',
        ]);

        $rarita = CollezioneRarita::where('col_id_collezione', $collezioneId)->findOrFail($id);

        // Remove old file if switching away from file or uploading a new one
        if ($rarita->tipo_icona === 'file' && $rarita->icona) {
            if ($request->tipo_icona === 'bootstrap' || $request->hasFile('icona')) {
                Storage::disk('public')->delete($rarita->icona);
            }
        }

        [$iconaVal, $tipoIcona] = $this->resolveIcona($request, 'rarita', $collezioneId, $rarita);

        $rarita->nome       = $request->nome;
        $rarita->icona      = $iconaVal;
        $rarita->tipo_icona = $tipoIcona;
        $rarita->save();

        return back()->with('success', 'Rarità aggiornata.');
    }

    public function destroy($collezioneId, $id)
    {
        $rarita = CollezioneRarita::where('col_id_collezione', $collezioneId)->findOrFail($id);
        if ($rarita->tipo_icona === 'file' && $rarita->icona) Storage::disk('public')->delete($rarita->icona);
        $rarita->delete();

        return back()->with('success', 'Rarità eliminata.');
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

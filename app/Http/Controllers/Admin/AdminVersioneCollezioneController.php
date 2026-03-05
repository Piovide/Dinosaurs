<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Collezione;
use App\Models\VersioneCollezione;
use Illuminate\Http\Request;

class AdminVersioneCollezioneController extends Controller
{
    public function store(Request $request, $collezioneId)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
        ]);

        Collezione::findOrFail($collezioneId);

        $versione = VersioneCollezione::create([
            'col_id_collezione' => $collezioneId,
            'nome'              => $request->nome,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'id'    => $versione->id_versione,
                'label' => $versione->nome,
            ], 201);
        }

        return back()->with('success', 'Versione aggiunta.');
    }

    public function update(Request $request, $collezioneId, $id)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
        ]);

        $versione = VersioneCollezione::where('col_id_collezione', $collezioneId)->findOrFail($id);
        $versione->update(['nome' => $request->nome]);

        return back()->with('success', 'Versione aggiornata.');
    }

    public function destroy($collezioneId, $id)
    {
        $versione = VersioneCollezione::where('col_id_collezione', $collezioneId)->findOrFail($id);
        $versione->delete();

        return back()->with('success', 'Versione eliminata.');
    }
}

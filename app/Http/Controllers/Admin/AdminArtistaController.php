<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artista;
use Illuminate\Http\Request;

class AdminArtistaController extends Controller
{
    public function index()
    {
        $artisti = Artista::withCount('carte')->orderBy('nominativo')->paginate(30);
        return view('admin.artisti.index', compact('artisti'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nominativo'    => 'required|string|max:255',
            'data_nascita'  => 'nullable|date',
            'link_sito'     => 'nullable|url|max:255',
            'link_social'   => 'nullable|url|max:255',
        ]);

        $artista = Artista::create($request->only('nominativo', 'data_nascita', 'link_sito', 'link_social'));

        if ($request->expectsJson()) {
            return response()->json([
                'id'     => $artista->id_artista,
                'label'  => $artista->nominativo,
            ], 201);
        }

        return back()->with('success', 'Artista creato con successo.');
    }

    public function edit($id)
    {
        $artista = Artista::findOrFail($id);
        return view('admin.artisti.edit', compact('artista'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nominativo'   => 'required|string|max:255',
            'data_nascita' => 'nullable|date',
            'link_sito'    => 'nullable|url|max:255',
            'link_social'  => 'nullable|url|max:255',
        ]);

        $artista = Artista::findOrFail($id);
        $artista->update($request->only('nominativo', 'data_nascita', 'link_sito', 'link_social'));

        return redirect()->route('admin.artisti.index')
            ->with('success', 'Artista aggiornato con successo.');
    }

    public function destroy($id)
    {
        Artista::findOrFail($id)->delete();
        return redirect()->route('admin.artisti.index')
            ->with('success', 'Artista eliminato.');
    }
}

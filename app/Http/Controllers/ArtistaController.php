<?php

namespace App\Http\Controllers;

use App\Models\Artista;

class ArtistaController extends Controller
{
    public function index()
    {
        $artisti = Artista::withCount('carte')->orderBy('nominativo')->get();
        return view('artisti.index', compact('artisti'));
    }

    public function show($id)
    {
        $artista = Artista::withCount('carte')->findOrFail($id);
        $carte   = $artista->carte()
            ->with(['collezione', 'raritas', 'tipologie', 'versioni'])
            ->orderBy('id_carta')
            ->paginate(24);

        return view('artisti.show', compact('artista', 'carte'));
    }
}

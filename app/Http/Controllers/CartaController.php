<?php

namespace App\Http\Controllers;

use App\Models\Carta;
use App\Models\Dizionario;
use Illuminate\Http\Request;

class CartaController extends Controller
{
    public function index(Request $request)
    {
        $query = Carta::with(['collezione', 'artista', 'rarita', 'tipo']);

        if ($request->filled('rarita')) {
            $query->where('dnz_id_rarita', $request->rarita);
        }

        if ($request->filled('tipo')) {
            $query->where('dnz_id_tipo', $request->tipo);
        }
        $pagination = $query->paginate(12);
        $carte = $pagination->getCollection();


        $rarita = Dizionario::where('categoria', 'rarita')->where('stato', 1)->get();
        $tipi = Dizionario::where('categoria', 'tipo')->where('stato', 1)->get();

        return view('carte.index', compact('carte', 'pagination', 'rarita', 'tipi'));
    }

    public function show($id)
    {
        $carta = Carta::with(['collezione', 'artista', 'rarita', 'tipo'])->findOrFail($id);
        return view('carte.show', compact('carta'));
    }
}

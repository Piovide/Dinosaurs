<?php

namespace App\Http\Controllers;

use App\Models\Carta;
use App\Models\CollezioneUtente;
use App\Models\Dizionario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CollezioneUtenteController extends Controller
{
    /**
     * Aggiorna o crea una carta nella collezione dell'utente
     */
    public function aggiorna(Request $request)
    {
        $request->validate([
            'car_id_carta' => 'required|exists:carta,id_carta',
            'quantita' => 'required|integer|min:0'
        ]);

        $utenteId = Auth::id();
        $cartaId = $request->car_id_carta;
        $quantita = $request->quantita;

        try {
            if ($quantita > 0) {
                CollezioneUtente::updateOrCreate(
                    [
                        'utn_id_utente' => $utenteId,
                        'car_id_carta' => $cartaId
                    ],
                    [
                        'quantita' => $quantita
                    ]
                );
            } else {
                CollezioneUtente::where('utn_id_utente', $utenteId)
                    ->where('car_id_carta', $cartaId)
                    ->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Carta aggiornata con successo',
                'quantita' => $quantita
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore nel salvataggio: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Recupera la quantità di una carta nella collezione dell'utente
     */
    public function getQuantita($cartaId)
    {
        $utenteId = Auth::id();

        $collezioneUtente = CollezioneUtente::where('utn_id_utente', $utenteId)
            ->where('car_id_carta', $cartaId)
            ->first();

        return response()->json([
            'quantita' => $collezioneUtente ? $collezioneUtente->quantita : 0
        ]);
    }

    public function collezione(Request $request, $username = null){
        if($username === null) {
            $username = Auth::user()->username;
        }

        $query = Carta::join('collezione_utente', 'carta.id_carta', '=', 'collezione_utente.car_id_carta')
            ->join('utente', 'collezione_utente.utn_id_utente', '=', 'utente.id_utente')
            ->where('utente.username', $username)
            ->select('carta.*', 'collezione_utente.quantita', 'collezione_utente.preferita', 'collezione_utente.note')
            ->orderBy('id_carta', 'asc');

        if ($request->filled('rarita')) {
            $query->whereHas('carta', function($q) use ($request) {
                $q->where('dnz_id_rarita', (int)$request->input('rarita'));
            });
        }

        if ($request->filled('tipo')) {
            $query->whereHas('carta', function($q) use ($request) {
                $q->where('dnz_id_tipo', (int)$request->input('tipo'));
            });
        }
        $pagination = $query->paginate(12);
        $carte = $pagination->getCollection();


        $rarita = Dizionario::where('categoria', 'rarita')->where('stato', 1)->get();
        $tipi = Dizionario::where('categoria', 'tipo')->where('stato', 1)->get();

        return view('carte.index', compact('carte', 'pagination', 'rarita', 'tipi', 'username'));
    }
}

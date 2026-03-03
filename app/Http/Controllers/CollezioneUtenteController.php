<?php

namespace App\Http\Controllers;

use App\Models\Carta;
use App\Models\CollezioneRarita;
use App\Models\CollezioneTipologia;
use App\Models\CollezioneUtente;
use App\Models\Utente;
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

    public function collezione(Request $request, $username = null)
    {
        if ($username === null) {
            $username = Auth::user()->username;
        }

        $utente   = Utente::where('username', $username)->firstOrFail();
        $cartaIds = CollezioneUtente::where('utn_id_utente', $utente->id_utente)->pluck('car_id_carta');

        $query = Carta::with(['collezione', 'artista', 'rarita', 'tipologia'])
            ->whereIn('id_carta', $cartaIds)
            ->orderBy('id_carta');

        if ($request->filled('rarita')) {
            $query->where('rar_id_rarita', $request->rarita);
        }

        if ($request->filled('tipologia')) {
            $query->where('tip_id_tipologia', $request->tipologia);
        }

        $pagination = $query->paginate(12);
        $carte      = $pagination->getCollection();

        $rarita    = CollezioneRarita::orderBy('nome')->get();
        $tipologie = CollezioneTipologia::orderBy('nome')->get();
        $collezione = null;

        if ($request->expectsJson()) {
            return response()->json([
                'html'     => view('carte._cards', ['carte' => $carte])->render(),
                'hasMore'  => $pagination->hasMorePages(),
                'nextPage' => $pagination->currentPage() + 1,
            ]);
        }

        return view('carte.index', compact('carte', 'pagination', 'rarita', 'tipologie', 'username', 'collezione'));
    }
}

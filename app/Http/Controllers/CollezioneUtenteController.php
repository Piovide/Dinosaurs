<?php

namespace App\Http\Controllers;

use App\Models\Artista;
use App\Models\Carta;
use App\Models\CollezioneRarita;
use App\Models\CollezioneTipologia;
use App\Models\CollezioneUtente;
use App\Models\Utente;
use App\Models\VersioneCollezione;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CollezioneUtenteController extends Controller
{
    /**
     * Aggiorna o crea una carta nella collezione dell'utente
     */
    public function aggiorna(Request $request)
    {
        $request->validate([
            'car_id_carta'             => 'required|exists:carta,id_carta',
            'quantita'                 => 'required|integer|min:0',
            'rar_id_collezione_rarita' => 'nullable|exists:collezione_rarita,id_collezione_rarita',
            'ver_id_versione'          => 'nullable|exists:versione_collezione,id_versione',
        ]);
        Log::info('Aggiornamento collezione utente', [
            'utente_id' => Auth::id(),
            'car_id_carta' => $request->car_id_carta,
            'quantita' => $request->quantita,
            'rar_id_collezione_rarita' => $request->rar_id_collezione_rarita,
            'ver_id_versione' => $request->ver_id_versione,
        ]);

        $utenteId  = Auth::id();
        $cartaId   = $request->car_id_carta;
        $quantita  = $request->quantita;
        $raritaId  = $request->rar_id_collezione_rarita ?? null;
        $versioneId = $request->ver_id_versione ?? null;

        try {
            if ($quantita > 0) {
                CollezioneUtente::updateOrCreate(
                    [
                        'utn_id_utente'            => $utenteId,
                        'car_id_carta'             => $cartaId,
                        'rar_id_collezione_rarita' => $raritaId,
                        'ver_id_versione'          => $versioneId,
                    ],
                    [
                        'quantita' => $quantita,
                    ]
                );
            } else {
                CollezioneUtente::where('utn_id_utente', $utenteId)
                    ->where('car_id_carta', $cartaId)
                    ->where('rar_id_collezione_rarita', $raritaId)
                    ->where('ver_id_versione', $versioneId)
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
            if (!Auth::check()) {
                return redirect()->route('auth.login')
                    ->with('error', 'Devi accedere per vedere la tua collezione.');
            }
            $username = Auth::user()->username;
        }

        $utente   = Utente::where('username', $username)->firstOrFail();
        $cartaIds = CollezioneUtente::where('utn_id_utente', $utente->id_utente)->pluck('car_id_carta');

        $query = Carta::with(['collezione', 'artista', 'raritas', 'versioni', 'tipologie'])
            ->whereIn('id_carta', $cartaIds)
            ->orderBy('id_carta');

        if ($request->filled('rarita')) {
            $query->whereHas('raritas', fn ($q) => $q->where('collezione_rarita.id_collezione_rarita', $request->rarita));
        }

        if ($request->filled('tipologia')) {
            $query->whereHas('tipologie', fn($q) => $q->where('id_collezione_tipologia', $request->tipologia));
        }

        $pagination = $query->paginate(12);
        $carte      = $pagination->getCollection();

        $rarita    = CollezioneRarita::orderBy('nome')->get();
        $tipologie = CollezioneTipologia::orderBy('nome')->get();
        $artisti   = Artista::whereHas('carte', fn($q) => $q->whereIn('id_carta', $cartaIds))
            ->orderBy('nominativo')
            ->get();
        $collezione = null;

        if ($request->expectsJson()) {
            return response()->json([
                'html'     => view('carte._cards', ['carte' => $carte])->render(),
                'hasMore'  => $pagination->hasMorePages(),
                'nextPage' => $pagination->currentPage() + 1,
            ]);
        }

        return view('carte.index', compact('carte', 'pagination', 'rarita', 'tipologie', 'artisti', 'username', 'collezione'));
    }
}

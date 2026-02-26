<?php

namespace App\Http\Controllers;

use App\Models\Carta;
use App\Models\CollezioneUtente;
use App\Models\Dizionario;
use App\Models\Collezione;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CollezioneModeratorController extends Controller
{
    public function edit($collezioneId)
    {
        $collezione = Collezione::findOrFail($collezioneId);

        if ($collezione->utn_id_utente !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Non hai il permesso di modificare questa collezione');
        }

        $carte = $collezione->carte()->with(['artista', 'rarita', 'tipo'])->paginate(20);
        $tutteLeCarte = Carta::with(['artista', 'rarita', 'tipo'])->get();

        return view('collezione.modifica', compact('collezione', 'carte', 'tutteLeCarte'));
    }

    public function addCard(Request $request, $collezioneId)
    {
        $request->validate([
            'car_id_carta' => 'required|exists:carta,id_carta',
            'quantita' => 'required|integer|min:1'
        ]);

        $collezione = Collezione::findOrFail($collezioneId);

        if ($collezione->utn_id_utente !== Auth::id() && !Auth::user()->isAdmin()) {
            return response()->json(['error' => 'Non autorizzato'], 403);
        }

        CollezioneUtente::updateOrCreate(
            [
                'utn_id_utente' => $collezione->utn_id_utente,
                'car_id_carta' => $request->car_id_carta
            ],
            [
                'quantita' => $request->quantita
            ]
        );

        return response()->json(['success' => 'Carta aggiunta con successo']);
    }

    public function removeCard(Request $request, $collezioneId, $cartaId)
    {
        $collezione = Collezione::findOrFail($collezioneId);

        if ($collezione->utn_id_utente !== Auth::id() && !Auth::user()->isAdmin()) {
            return response()->json(['error' => 'Non autorizzato'], 403);
        }

        CollezioneUtente::where('utn_id_utente', $collezione->utn_id_utente)
            ->where('car_id_carta', $cartaId)
            ->delete();

        return response()->json(['success' => 'Carta rimossa con successo']);
    }

    /**
     * Aggiorna la quantità di una carta
     */
    public function updateQuantity(Request $request, $collezioneId, $cartaId)
    {
        $request->validate([
            'quantita' => 'required|integer|min:0'
        ]);

        $collezione = Collezione::findOrFail($collezioneId);

        if ($collezione->utn_id_utente !== Auth::id() && !Auth::user()->isAdmin()) {
            return response()->json(['error' => 'Non autorizzato'], 403);
        }

        if ($request->quantita == 0) {
            CollezioneUtente::where('utn_id_utente', $collezione->utn_id_utente)
                ->where('car_id_carta', $cartaId)
                ->delete();
        } else {
            CollezioneUtente::where('utn_id_utente', $collezione->utn_id_utente)
                ->where('car_id_carta', $cartaId)
                ->update(['quantita' => $request->quantita]);
        }

        return response()->json(['success' => 'Quantità aggiornata']);
    }

    /**
     * Carica un'immagine per una carta
     */
    public function uploadImage(Request $request, $cartaId)
    {
        $request->validate([
            'immagine' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Verifica che sia moderatore o admin
        if (!Auth::user()->isAdmin() && !Auth::user()->isModerator()) {
            return response()->json(['error' => 'Non autorizzato'], 403);
        }

        $carta = Carta::findOrFail($cartaId);

        if ($request->hasFile('immagine')) {
            $file = $request->file('immagine');
            $filename = 'carta_' . $cartaId . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('img/carte'), $filename);

            // Aggiorna il campo immagine nel modello Carta
            $carta->update(['immagine' => 'img/carte/' . $filename]);

            return response()->json(['success' => 'Immagine caricata con successo', 'path' => 'img/carte/' . $filename]);
        }

        return response()->json(['error' => 'Errore nel caricamento'], 500);
    }

    /**
     * Mostra tutte le collezioni (solo per admin)
     */
    public function index()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Solo gli amministratori possono accedere');
        }

        $collezioni = Collezione::with('utente')->paginate(20);
        return view('collezione.index', compact('collezioni'));
    }
}

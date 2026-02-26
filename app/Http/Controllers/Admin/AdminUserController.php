<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Utente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminUserController extends Controller
{
    public function index()
    {
        $utenti = Utente::orderBy('ruolo')->orderBy('username')->paginate(20);
        return view('admin.utenti.index', compact('utenti'));
    }

    public function edit($id)
    {
        $utente = Utente::findOrFail($id);
        return view('admin.utenti.edit', compact('utente'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'ruolo' => 'required|in:utente,moderatore,admin',
            'email' => 'required|email|unique:utente,email,' . $id . ',id_utente',
        ]);

        $utente = Utente::findOrFail($id);
        $utente->update($request->only('ruolo', 'email'));

        return redirect()->route('admin.utenti.index')
            ->with('success', 'Utente aggiornato con successo.');
    }

    public function destroy($id)
    {
        $utente = Utente::findOrFail($id);

        // Prevent deleting yourself
        if ($utente->id_utente === Auth::id()) {
            return redirect()->route('admin.utenti.index')
                ->with('error', 'Non puoi eliminare il tuo account.');
        }

        $utente->delete();

        return redirect()->route('admin.utenti.index')
            ->with('success', 'Utente eliminato con successo.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Utente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput($request->only('username'))
                ->with('open_modal', 'login');
        }

        $validated = $validator->validated();

        $loggedIn = Auth::attempt(['username' => $validated['username'], 'password' => $validated['password']]);

        if (!$loggedIn && filter_var($validated['username'], FILTER_VALIDATE_EMAIL)) {
            $loggedIn = Auth::attempt(['email' => $validated['username'], 'password' => $validated['password']]);
        }

        if (!$loggedIn) {
            return back()
                ->withErrors(['username' => 'Le credenziali inserite non sono corrette.'])
                ->withInput($request->only('username'))
                ->with('open_modal', 'login');
        }

        /** @var \App\Models\Utente $user */
        $user = Auth::user();

        if (!$user->isAdmin() && !$user->hasVerifiedEmail()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return back()
                ->withErrors(['username' => 'Devi verificare la tua email prima di accedere. Controlla la casella di posta.'])
                ->withInput($request->only('username'))
                ->with('open_modal', 'login');
        }

        $request->session()->regenerate();
        return redirect()->route('home')->with('success', 'Login effettuato con successo!');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|unique:utente|max:255',
            'email'    => 'required|email|unique:utente',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput($request->only('username', 'email'))
                ->with('open_modal', 'register');
        }

        $validated = $validator->validated();

        $user = Utente::create([
            'username'    => $validated['username'],
            'email'       => $validated['email'],
            'password'    => Hash::make($validated['password']),
            'preferences' => json_encode([]),
        ]);

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->getEmailForVerification())
            ]
        );

        $user->sendEmailVerificationNotification($url);

        return redirect()->route('auth.login')
            ->with('success', 'Registrazione completata! Controlla la tua email per verificare l\'account prima di accedere.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Logout effettuato con successo!');
    }
}

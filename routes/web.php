<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\CartaController;
use App\Http\Controllers\CollezioneUtenteController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\ArtistaController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminCollezioneController;
use App\Http\Controllers\Admin\AdminCartaController;
use App\Http\Controllers\Admin\AdminArtistaController;
use App\Http\Controllers\Admin\AdminCollezioneRaritaController;
use App\Http\Controllers\Admin\AdminCollezioneTipologiaController;
use App\Http\Controllers\Admin\AdminVersioneCollezioneController;

Route::get('/', [CartaController::class, 'index'])->name('home');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/carte/{id}', [CartaController::class, 'show'])->name('carte.show');

Route::get('/artisti', [ArtistaController::class, 'index'])->name('artisti.index');
Route::get('/artisti/{id}', [ArtistaController::class, 'show'])->name('artisti.show');

Route::view('/crediti', 'crediti')->name('crediti');

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('auth.login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('auth.register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

// Email verification routes
Route::get('/email/verify', fn() => view('auth.verify-email'))->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Http\Request $request, $id, $hash) {
    $user = \App\Models\Utente::findOrFail($id);

    if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        abort(403, 'Link di verifica non valido.');
    }

    if (!$request->query('signature') && $request->query('amp;signature')) {
        $request->query->set('signature', $request->query('amp;signature'));
        $request->query->remove('amp;signature');
    }

    $queryDaIgnorare = array_values(array_filter(
        array_keys($request->query()),
        fn($query) => !in_array($query, ['expires', 'signature'], true)
    ));
    $queryDaIgnorare[] = 'amp;signature';
    $queryDaIgnorare = array_values(array_unique($queryDaIgnorare));

    $signatureValida = \Illuminate\Support\Facades\URL::hasValidSignature($request, true, $queryDaIgnorare)
        || \Illuminate\Support\Facades\URL::hasValidSignature($request, false, $queryDaIgnorare);

    if (!$signatureValida) {
        abort(403, 'Il link di verifica è scaduto o non valido.');
    }

    if (!$user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
    }

    \Illuminate\Support\Facades\Auth::login($user);

    return redirect()->route('home')->with('success', 'Email verificata con successo! Benvenuto su ' . config('app.name') . '!');
})->name('verification.verify');
Route::post('/email/verification-notification', function (\Illuminate\Http\Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('success', 'Link di verifica inviato di nuovo. Controlla la tua email.');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Password reset routes
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->middleware('guest')->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->middleware('guest')->name('password.email');
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->middleware('guest')->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->middleware('guest')->name('password.update');
Route::get('/collezione/{username?}', [CollezioneUtenteController::class, 'collezione'])->name('collezione');

// API Routes per la collezione dell'utente
Route::middleware('auth')->prefix('api')->group(function () {
    Route::post('/collezione-utente/aggiorna', [CollezioneUtenteController::class, 'aggiorna']);
    Route::get('/collezione-utente/quantita/{cartaId}', [CollezioneUtenteController::class, 'getQuantita']);
});

// ─── Admin Routes (solo per ruolo admin) ──────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/', fn() => redirect()->route('admin.dashboard'));
    Route::get('/dashboard', fn() => view('admin.dashboard'))->name('dashboard');

    // Gestione utenti
    Route::get('/utenti',              [AdminUserController::class, 'index'])->name('utenti.index');
    Route::get('/utenti/{id}/edit',    [AdminUserController::class, 'edit'])->name('utenti.edit');
    Route::put('/utenti/{id}',         [AdminUserController::class, 'update'])->name('utenti.update');
    Route::delete('/utenti/{id}',      [AdminUserController::class, 'destroy'])->name('utenti.destroy');

    // Gestione collezioni
    Route::get('/collezioni',                       [AdminCollezioneController::class, 'index'])->name('collezioni.index');
    Route::get('/collezioni/create',                [AdminCollezioneController::class, 'create'])->name('collezioni.create');
    Route::post('/collezioni',                      [AdminCollezioneController::class, 'store'])->name('collezioni.store');
    Route::get('/collezioni/{id}',                  [AdminCollezioneController::class, 'show'])->name('collezioni.show');
    Route::get('/collezioni/{id}/edit',             [AdminCollezioneController::class, 'edit'])->name('collezioni.edit');
    Route::put('/collezioni/{id}',                  [AdminCollezioneController::class, 'update'])->name('collezioni.update');
    Route::delete('/collezioni/{id}',               [AdminCollezioneController::class, 'destroy'])->name('collezioni.destroy');

    // Aggiunta carta a una collezione
    Route::get('/collezioni/{collezioneId}/carte/create',  [AdminCollezioneController::class, 'createCarta'])->name('collezioni.carta.create');
    Route::post('/collezioni/{collezioneId}/carte',        [AdminCollezioneController::class, 'storeCarta'])->name('collezioni.carta.store');

    // Rarità per collezione
    Route::post('/collezioni/{collezioneId}/rarita',            [AdminCollezioneRaritaController::class, 'store'])->name('collezioni.rarita.store');
    Route::put('/collezioni/{collezioneId}/rarita/{id}',        [AdminCollezioneRaritaController::class, 'update'])->name('collezioni.rarita.update');
    Route::delete('/collezioni/{collezioneId}/rarita/{id}',     [AdminCollezioneRaritaController::class, 'destroy'])->name('collezioni.rarita.destroy');

    // Tipologie per collezione
    Route::post('/collezioni/{collezioneId}/tipologie',         [AdminCollezioneTipologiaController::class, 'store'])->name('collezioni.tipologie.store');
    Route::put('/collezioni/{collezioneId}/tipologie/{id}',     [AdminCollezioneTipologiaController::class, 'update'])->name('collezioni.tipologie.update');
    Route::delete('/collezioni/{collezioneId}/tipologie/{id}',  [AdminCollezioneTipologiaController::class, 'destroy'])->name('collezioni.tipologie.destroy');

    // Versioni alternative per collezione
    Route::post('/collezioni/{collezioneId}/versioni',          [AdminVersioneCollezioneController::class, 'store'])->name('collezioni.versioni.store');
    Route::put('/collezioni/{collezioneId}/versioni/{id}',      [AdminVersioneCollezioneController::class, 'update'])->name('collezioni.versioni.update');
    Route::delete('/collezioni/{collezioneId}/versioni/{id}',   [AdminVersioneCollezioneController::class, 'destroy'])->name('collezioni.versioni.destroy');

    // Modifica / eliminazione carta
    Route::get('/carte/{id}/edit',     [AdminCartaController::class, 'edit'])->name('carte.edit');
    Route::put('/carte/{id}',          [AdminCartaController::class, 'update'])->name('carte.update');
    Route::delete('/carte/{id}',       [AdminCartaController::class, 'destroy'])->name('carte.destroy');

    // Gestione artisti
    Route::get('/artisti',             [AdminArtistaController::class, 'index'])->name('artisti.index');
    Route::post('/artisti',            [AdminArtistaController::class, 'store'])->name('artisti.store');
    Route::get('/artisti/{id}/edit',   [AdminArtistaController::class, 'edit'])->name('artisti.edit');
    Route::put('/artisti/{id}',        [AdminArtistaController::class, 'update'])->name('artisti.update');
    Route::delete('/artisti/{id}',     [AdminArtistaController::class, 'destroy'])->name('artisti.destroy');
});


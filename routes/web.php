<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartaController;
use App\Http\Controllers\CollezioneUtenteController;
use App\Http\Controllers\AuthController;

Route::get('/', [CartaController::class, 'index'])->name('home');
Route::get('/carte/{id}', [CartaController::class, 'show'])->name('carte.show');

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('auth.login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('auth.register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
Route::get('/collezione/{username?}', [CollezioneUtenteController::class, 'collezione'])->name('collezione');

// API Routes per la collezione dell'utente
Route::middleware('auth')->prefix('api')->group(function () {
    Route::post('/collezione-utente/aggiorna', [CollezioneUtenteController::class, 'aggiorna']);
    Route::get('/collezione-utente/quantita/{cartaId}', [CollezioneUtenteController::class, 'getQuantita']);
});

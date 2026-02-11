<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartaController;

Route::get('/', [CartaController::class, 'index'])->name('home');
Route::get('/carte/{id}', [CartaController::class, 'show'])->name('carte.show');

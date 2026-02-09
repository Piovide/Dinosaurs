<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollezioneUtente extends Model
{
    use HasFactory;

    protected $table = 'collezione_utente';
    protected $primaryKey = 'id_collezione_utente';
    protected $fillable = ['utn_id_utente', 'car_id_carta', 'quantita', 'preferita', 'note'];
}

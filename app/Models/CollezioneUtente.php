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

    public function utente(){
        return $this->belongsTo(Utente::class, 'utn_id_utente', 'id_utente');
    }
    public function carta(){
        return $this->belongsTo(Carta::class, 'car_id_carta', 'id_carta');
    }
}

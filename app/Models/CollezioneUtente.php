<?php

namespace App\Models;

use App\Models\CollezioneRarita;
use App\Models\VersioneCollezione;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollezioneUtente extends Model
{
    use HasFactory;

    protected $table = 'collezione_utente';
    protected $primaryKey = 'id_collezione_utente';
    protected $fillable = ['utn_id_utente', 'car_id_carta', 'rar_id_collezione_rarita', 'ver_id_versione', 'quantita', 'preferita', 'note'];

    public function utente(){
        return $this->belongsTo(Utente::class, 'utn_id_utente', 'id_utente');
    }
    public function carta(){
        return $this->belongsTo(Carta::class, 'car_id_carta', 'id_carta');
    }
    public function rarita(){
        return $this->belongsTo(CollezioneRarita::class, 'rar_id_collezione_rarita', 'id_collezione_rarita');
    }
    public function versione(){
        return $this->belongsTo(VersioneCollezione::class, 'ver_id_versione', 'id_versione');
    }
}

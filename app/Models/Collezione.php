<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collezione extends Model
{
    use HasFactory;

    protected $table = 'collezione';
    protected $primaryKey = 'id_collezione';
    protected $fillable = ['nome', 'descrizione', 'numero_carte', 'data_uscita'];

    public function carte(){
        return $this->hasMany(Carta::class, 'col_id_collezione', 'id_collezione');
    }

    public function rarita(){
        return $this->hasMany(CollezioneRarita::class, 'col_id_collezione', 'id_collezione');
    }

    public function tipologie(){
        return $this->hasMany(CollezioneTipologia::class, 'col_id_collezione', 'id_collezione');
    }
}

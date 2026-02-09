<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dizionario extends Model
{
    use HasFactory;

    protected $table = 'dizionario';
    protected $primaryKey = 'id_dizionario';
    protected $fillable = ['categoria', 'codice', 'descrizione', 'stato'];

    public function carteRarita()
    {
        return $this->hasMany(Carta::class, 'dnz_id_rarita', 'id_dizionario');
    }

    public function carteTipo()
    {
        return $this->hasMany(Carta::class, 'dnz_id_tipo', 'id_dizionario');
    }
}

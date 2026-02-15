<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carta extends Model
{
    use HasFactory;

    protected $table = 'carta';
    protected $primaryKey = 'id_carta';
    protected $fillable = [
        'col_id_collezione',
        'titolo',
        'descrizione',
        'art_id_artista',
        'dnz_id_rarita',
        'dnz_id_tipo',
        'numero',
        'immagine_url'
    ];

    public function collezione()
    {
        return $this->belongsTo(Collezione::class, 'col_id_collezione', 'id_collezione');
    }

    public function artista()
    {
        return $this->belongsTo(Artista::class, 'art_id_artista', 'id_artista');
    }

    public function rarita()
    {
        return $this->belongsTo(Dizionario::class, 'dnz_id_rarita', 'id_dizionario');
    }

    public function tipo()
    {
        return $this->belongsTo(Dizionario::class, 'dnz_id_tipo', 'id_dizionario');
    }
}

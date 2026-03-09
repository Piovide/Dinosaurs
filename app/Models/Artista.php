<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artista extends Model
{
    use HasFactory;

    protected $table = 'artista';
    protected $primaryKey = 'id_artista';
    protected $fillable = ['nominativo', 'data_nascita', 'link_sito', 'link_social'];

    public function carte()
    {
        return $this->hasMany(Carta::class, 'art_id_artista', 'id_artista');
    }
}

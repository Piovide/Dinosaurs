<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RaritaCarta extends Model
{
    protected $table = 'rarita_carta';
    protected $primaryKey = 'id_rarita_carta';
    protected $fillable = [
        'id_carta',
        'id_collezione_rarita',
    ];

    public function carta()
    {
        return $this->belongsTo(Carta::class, 'id_carta', 'id_carta');
    }

    public function rarita()
    {
        return $this->belongsTo(CollezioneRarita::class, 'id_collezione_rarita', 'id_collezione_rarita');
    }
}

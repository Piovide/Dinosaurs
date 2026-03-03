<?php

namespace App\Models;

use App\Services\CartaImageService;
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
        'rar_id_rarita',
        'tip_id_tipologia',
        'numero',
        'immagine_url',
    ];

    /**
     * Returns the full public asset URL for the card image, or null if not set.
     * DB stores a relative path like "{collection-slug}/{filename}".
     */
    public function getImmagineAssetAttribute(): ?string
    {
        return app(CartaImageService::class)->assetUrl($this->immagine_url);
    }

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
        return $this->belongsTo(CollezioneRarita::class, 'rar_id_rarita', 'id_collezione_rarita');
    }

    public function tipologia()
    {
        return $this->belongsTo(CollezioneTipologia::class, 'tip_id_tipologia', 'id_collezione_tipologia');
    }
}

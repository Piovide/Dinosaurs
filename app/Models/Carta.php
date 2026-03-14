<?php

namespace App\Models;

use App\Services\CartaImageService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\VersioneCollezione;
use App\Models\CollezioneTipologia;

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
        'art_id_artista_secondario',
        'art_id_artista_back',
        'numero',
        'prefisso',
        'suffisso',
        'immagine_url',
        'immagine_retro_url',
    ];

    /**
     * Returns the full public asset URL for the card front image, or null if not set.
     * DB stores a relative path like "{collection-slug}/{filename}".
     */
    public function getImmagineAssetAttribute(): ?string
    {
        return app(CartaImageService::class)->assetUrl($this->immagine_url);
    }

    /**
     * Returns the full public asset URL for the card back image, or null if not set.
     */
    public function getImmagineRetroAssetAttribute(): ?string
    {
        return app(CartaImageService::class)->assetUrl($this->immagine_retro_url);
    }

    public function collezione()
    {
        return $this->belongsTo(Collezione::class, 'col_id_collezione', 'id_collezione');
    }

    public function artista()
    {
        return $this->belongsTo(Artista::class, 'art_id_artista', 'id_artista');
    }

    public function artistaSecondario()
    {
        return $this->belongsTo(Artista::class, 'art_id_artista_secondario', 'id_artista');
    }

    public function artistaBack()
    {
        return $this->belongsTo(Artista::class, 'art_id_artista_back', 'id_artista');
    }

    /**
     * A card can have multiple rarity versions.
     */
    public function raritas()
    {
        return $this->belongsToMany(
            CollezioneRarita::class,
            'rarita_carta',
            'id_carta',
            'id_collezione_rarita'
        )->withTimestamps();
    }

    /**
     * A card can belong to multiple tipologie.
     */
    public function tipologie()
    {
        return $this->belongsToMany(
            CollezioneTipologia::class,
            'carta_tipologia',
            'id_carta',
            'id_collezione_tipologia'
        )->withTimestamps();
    }

    /**
     * Alternative versions of this card (from the collection's version list).
     */
    public function versioni()
    {
        return $this->belongsToMany(
            VersioneCollezione::class,
            'carta_versione',
            'id_carta',
            'id_versione'
        )->withTimestamps();
    }
}

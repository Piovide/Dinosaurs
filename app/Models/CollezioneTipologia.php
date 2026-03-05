<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollezioneTipologia extends Model
{
    protected $table = 'collezione_tipologia';
    protected $primaryKey = 'id_collezione_tipologia';
    protected $fillable = ['col_id_collezione', 'nome', 'icona', 'tipo_icona'];

    public function collezione()
    {
        return $this->belongsTo(Collezione::class, 'col_id_collezione', 'id_collezione');
    }

    public function carte()
    {
        return $this->belongsToMany(Carta::class, 'carta_tipologia', 'id_collezione_tipologia', 'id_carta');
    }

    /**
     * True if this record has any icon configured.
     */
    public function getHasIconaAttribute(): bool
    {
        return !empty($this->icona);
    }

    /**
     * Returns the full asset URL when tipo_icona is 'file' (or null for backward compat).
     * Returns null for Bootstrap icon types.
     */
    public function getIconaUrlAttribute(): ?string
    {
        if (!$this->icona) return null;
        if ($this->tipo_icona === 'bootstrap') return null;
        return asset('storage/' . $this->icona);
    }

    /**
     * Returns the BI icon name when tipo_icona is 'bootstrap', otherwise null.
     */
    public function getIconaBootstrapAttribute(): ?string
    {
        if ($this->tipo_icona === 'bootstrap' && $this->icona) return $this->icona;
        return null;
    }
}

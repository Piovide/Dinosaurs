<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VersioneCollezione extends Model
{
    use HasFactory;

    protected $table = 'versione_collezione';
    protected $primaryKey = 'id_versione';
    protected $fillable = ['col_id_collezione', 'nome'];

    public function collezione()
    {
        return $this->belongsTo(Collezione::class, 'col_id_collezione', 'id_collezione');
    }

    public function collezioniUtenti()
    {
        return $this->hasMany(CollezioneUtente::class, 'ver_id_versione', 'id_versione');
    }
}

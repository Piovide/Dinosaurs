<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collezione extends Model
{
    use HasFactory;

    protected $table = 'collezione';
    protected $primaryKey = 'id_collezione';
    protected $fillable = ['nome', 'descrizione', 'data_uscita'];

    public function carte(){
        return $this->hasMany(Carta::class, 'col_id_collezione', 'id_collezione');
    }
}

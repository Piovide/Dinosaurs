<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CollezioneSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('collezione')->insert([
            'nome' => 'Cuphead Out Of The Cards',
            'descrizione' => 'Collezione di carte ispirata al videogioco Cuphead',
            'numero_carte' => 160,
            'data_uscita' => '2024-01-01',
        ]);
    }
}

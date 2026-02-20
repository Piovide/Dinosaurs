<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CollezioneSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('collezione')->insertOrIgnore([
            'nome' => 'Dinosaurs',
            'descrizione' => 'Set di carte sui dinosauri',
            'numero_carte' => 200,
            'data_uscita' => now(),
        ]);
    }
}

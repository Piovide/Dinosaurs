<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CollezioneSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('collezione')->insert([
            'nome' => 'Dinosaurs',
            'descrizione' => 'Set di carte Dinosaurs Tomodachi Press',
            'data_uscita' => now(),
        ]);
    }
}

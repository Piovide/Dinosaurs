<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CartaSeeder extends Seeder
{
    public function run(): void
    {

        DB::table('carta')->insertOrIgnore([
            [
                'col_id_collezione' => 1,
                'titolo' => 'T-Rex',
                'descrizione' => 'Il re dei dinosauri.',
                'art_id_artista' => 1,
                'dnz_id_rarita' => 1,
                'dnz_id_tipo' => 4,
                'numero' => 1,
                'immagine_url' => 'TEST_1.png',
            ],
            [
                'col_id_collezione' => 1,
                'titolo' => 'Triceratops',
                'descrizione' => 'Dinosauro con tre corna.',
                'art_id_artista' => 2,
                'dnz_id_rarita' => 2,
                'dnz_id_tipo' => 4,
                'numero' => 2,
                'immagine_url' => 'TEST_2.png',
            ],
        ]);
    }
}

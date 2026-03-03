<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CartaSeeder extends Seeder
{
    public function run(): void
    {
        $colId = DB::table('collezione')->value('id_collezione');

        // Seed rarità for this collection
        $raritaData = [
            ['col_id_collezione' => $colId, 'nome' => 'Comune'],
            ['col_id_collezione' => $colId, 'nome' => 'Rara'],
            ['col_id_collezione' => $colId, 'nome' => 'Ultra Rara'],
        ];
        foreach ($raritaData as $r) {
            DB::table('collezione_rarita')->insertOrIgnore($r);
        }

        // Seed tipologie for this collection
        $tipologiaData = [
            ['col_id_collezione' => $colId, 'nome' => 'Saurischi'],
            ['col_id_collezione' => $colId, 'nome' => 'Pterosauri'],
            ['col_id_collezione' => $colId, 'nome' => 'Ornitischi'],
        ];
        foreach ($tipologiaData as $t) {
            DB::table('collezione_tipologia')->insertOrIgnore($t);
        }

        // Resolve IDs by name
        $rarComune   = DB::table('collezione_rarita')->where('col_id_collezione', $colId)->where('nome', 'Comune')->value('id_collezione_rarita');
        $rarRara     = DB::table('collezione_rarita')->where('col_id_collezione', $colId)->where('nome', 'Rara')->value('id_collezione_rarita');
        $tipSaur     = DB::table('collezione_tipologia')->where('col_id_collezione', $colId)->where('nome', 'Saurischi')->value('id_collezione_tipologia');

        DB::table('carta')->insertOrIgnore([
            [
                'col_id_collezione' => $colId,
                'titolo'            => 'T-Rex',
                'descrizione'       => 'Il re dei dinosauri.',
                'art_id_artista'    => 1,
                'rar_id_rarita'     => $rarComune,
                'tip_id_tipologia'  => $tipSaur,
                'numero'            => 1,
                'immagine_url'      => 'dinosaurs/TEST_1.png',
            ],
            [
                'col_id_collezione' => $colId,
                'titolo'            => 'Triceratops',
                'descrizione'       => 'Dinosauro con tre corna.',
                'art_id_artista'    => 2,
                'rar_id_rarita'     => $rarRara,
                'tip_id_tipologia'  => $tipSaur,
                'numero'            => 2,
                'immagine_url'      => 'dinosaurs/TEST_2.png',
            ],
        ]);
    }
}

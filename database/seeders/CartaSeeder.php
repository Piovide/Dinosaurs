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
        $rarRara     = DB::table('collezione_rarita')->where('col_id_collezione', $colId)->where('nome', 'Rara')->value('id_collezione_rarita');
        $tipSaur     = DB::table('collezione_tipologia')->where('col_id_collezione', $colId)->where('nome', 'Saurischi')->value('id_collezione_tipologia');

        DB::table('carta')->insertOrIgnore([
            [
                'col_id_collezione' => $colId,
                'titolo'            => 'T-Rex',
                'descrizione'       => 'Il re dei dinosauri.',
                'art_id_artista'    => 1,
                'numero'            => 1,
                'immagine_url'      => 'dinosaurs/TEST_1.png',
            ],
            [
                'col_id_collezione' => $colId,
                'titolo'            => 'Triceratops',
                'descrizione'       => 'Dinosauro con tre corna.',
                'art_id_artista'    => 2,
                'numero'            => 2,
                'immagine_url'      => 'dinosaurs/TEST_2.png',
            ],
        ]);

        // Seed tipologie + rarità per carta via pivots
        $trexId        = DB::table('carta')->where('titolo', 'T-Rex')->value('id_carta');
        $triceratopsId = DB::table('carta')->where('titolo', 'Triceratops')->value('id_carta');

        DB::table('carta_tipologia')->insertOrIgnore([
            ['id_carta' => $trexId,        'id_collezione_tipologia' => $tipSaur, 'created_at' => now(), 'updated_at' => now()],
            ['id_carta' => $triceratopsId, 'id_collezione_tipologia' => $tipSaur, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Seed rarità per carta via pivot rarita_carta
        $rarUltraRara = DB::table('collezione_rarita')
            ->where('col_id_collezione', $colId)
            ->where('nome', 'Ultra Rara')
            ->value('id_collezione_rarita');

        DB::table('rarita_carta')->insertOrIgnore([
            // T-Rex: Ultra Rara (una sola rarità per carta)
            ['id_carta' => $trexId, 'id_collezione_rarita' => $rarUltraRara, 'created_at' => now(), 'updated_at' => now()],
            // Triceratops: Rara
            ['id_carta' => $triceratopsId, 'id_collezione_rarita' => $rarRara, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}

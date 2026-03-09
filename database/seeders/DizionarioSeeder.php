<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DizionarioSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('dizionario')->truncate();
        $entries = [
            // Rarità
            [
                'categoria' => 'rarita',
                'descrizione' => 'Comune',
                'stato' => 1,
            ],
            [
                'categoria' => 'rarita',
                'descrizione' => 'Rara',
                'stato' => 1,
            ],
            [
                'categoria' => 'rarita',
                'descrizione' => 'Ultra rara',
                'stato' => 1,
            ],

            // Stato carta
            [
                'categoria' => 'stato',
                'descrizione' => 'valido',
                'stato' => 1,
            ],
            [
                'categoria' => 'stato',
                'descrizione' => 'invalido',
                'stato' => 1,
            ],
        ];

        DB::table('dizionario')->insert($entries);
    }
}

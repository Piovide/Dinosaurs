<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DizionarioSeeder extends Seeder
{
    public function run(): void
    {
        $entries = [
            // Rarità
            [
                'categoria' => 'rarita',
                'descrizione' => '',
                'stato' => 1,
            ],
            [
                'categoria' => 'rarita',
                'descrizione' => '',
                'stato' => 1,
            ],
            [
                'categoria' => 'rarita',
                'descrizione' => '',
                'stato' => 1,
            ],

            // Tipi
            [
                'categoria' => 'tipo',
                'descrizione' => '',
                'stato' => 1,
            ],
            [
                'categoria' => 'tipo',
                'descrizione' => '',
                'stato' => 1,
            ],
            [
                'categoria' => 'tipo',
                'descrizione' => '',
                'stato' => 1,
            ],

            // Stato carta
            [
                'categoria' => 'stato',
                'descrizione' => '',
                'stato' => 1,
            ],
            [
                'categoria' => 'stato',
                'descrizione' => '',
                'stato' => 1,
            ],
        ];

        DB::table('dizionario')->insert($entries);
    }
}

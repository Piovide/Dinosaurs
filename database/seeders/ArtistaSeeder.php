<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArtistaSeeder extends Seeder
{
    public function run(): void
    {
        $artists = [
            ['nome' => 'Mario', 'cognome' => 'Rossi', 'link_sito' => '', 'link_social' => ''],
            ['nome' => 'Luigi', 'cognome' => 'Bianchi', 'link_sito' => '', 'link_social' => ''],
        ];


        DB::table('artista')->insertOrIgnore($artists);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArtistaSeeder extends Seeder
{
    public function run(): void
    {
        $artists = [
            ['nominativo' => 'Anne & Francesco'],
            ['nominativo' => 'Art Of SOOL'],
            ['nominativo' => 'Federico Artuso'],
            ['nominativo' => 'AWK'],
            ['nominativo' => 'Walter Baiamonte'],
            ['nominativo' => 'Mauro Belfiore'],
            ['nominativo' => 'Ivan Bigarella'],
            ['nominativo' => 'Alessandro Bucca'],
            ['nominativo' => 'Micol Buti'],
            ['nominativo' => 'Federico Butticé'],
            ['nominativo' => 'Pablo Cammello'],
            ['nominativo' => 'Francesco Castelli'],
            ['nominativo' => 'Alarico Ciaramelli'],
            ['nominativo' => 'Lorenzo Colangeli'],
            ['nominativo' => 'Stefano Colferai'],
            ['nominativo' => 'Sofia Corsi'],
            ['nominativo' => 'Alessandro Costa'],
            ['nominativo' => 'Roberto D\'agnano'],
            ['nominativo' => 'Matteo De Longis'],
            ['nominativo' => 'Mattia Di Meo'],
            ['nominativo' => 'Lorenzo Di Santo'],
            ['nominativo' => 'Serena Ferrero'],
            ['nominativo' => 'Chiara Fiordeponti'],
            ['nominativo' => 'Lorenzo Fornaciari'],
            ['nominativo' => 'Mauro Pietro Gandini'],
            ['nominativo' => 'Gabriele Genova'],
            ['nominativo' => 'Giakus'],
            ['nominativo' => 'Francesco Guarnaccia'],
            ['nominativo' => 'Ilclod'],
            ['nominativo' => 'Agnese Innocente'],
            ['nominativo' => 'Kappazap'],
            ['nominativo' => 'Martoz'],
            ['nominativo' => 'Riccardo La Bella'],
            ['nominativo' => 'Chiara Lamieri'],
            ['nominativo' => 'Arturo Lauria'],
            ['nominativo' => 'Savi Lomuscio'],
            ['nominativo' => 'Enrico Macchiavello'],
            ['nominativo' => 'Gaia Magnini'],
            ['nominativo' => 'Fabio Mancini'],
            ['nominativo' => 'Paolo Marro'],
            ['nominativo' => 'Stefano Martinuz'],
            ['nominativo' => 'Gianluca Maruotti'],
            ['nominativo' => 'Francesco Mazziotta'],
            ['nominativo' => 'Andrea Milana'],
            ['nominativo' => 'Paolo Minopoli'],
            ['nominativo' => 'Michele Monte'],
            ['nominativo' => 'Andrea Morandini'],
            ['nominativo' => 'Luca Negri'],
            ['nominativo' => 'NICORI'],
            ['nominativo' => 'Carlotta Notaro'],
            ['nominativo' => 'Nicholas Olivieri'],
            ['nominativo' => 'Davide Ottani'],
            ['nominativo' => 'Palmen'],
            ['nominativo' => 'Lucio Passalacqua'],
            ['nominativo' => 'Gianluca Patti'],
            ['nominativo' => 'Simone Peano'],
            ['nominativo' => 'Maria Luisa Petrarca'],
            ['nominativo' => 'Cecilia Petrucci'],
            ['nominativo' => 'Marco Raffaelli'],
            ['nominativo' => 'Maira Ranieri'],
            ['nominativo' => 'Giulio Rincione'],
            ['nominativo' => 'Alessandro Ripane'],
            ['nominativo' => 'Riccardo Robaldo'],
            ['nominativo' => 'Sara Ruggeri'],
            ['nominativo' => 'Domenico Russo'],
            ['nominativo' => 'Jacopo Starace'],
            ['nominativo' => 'Thefrancio'],
            ['nominativo' => 'The Gipsy Marionettist'],
            ['nominativo' => 'Riccardo Vignoli'],
            ['nominativo' => 'Zufo'],
        ];

        DB::table('artista')->insert($artists);
    }
}

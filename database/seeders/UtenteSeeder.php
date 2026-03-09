<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Utente;
use Illuminate\Support\Facades\Hash;

class UtenteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Utente::createOrFirst(['username' => 'testuser'], [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'preferences' => json_encode([]),
            'ruolo' => 'utente',
        ]);

        Utente::createOrFirst(['username' => 'admin'], [
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('4Mf9hmkTMA7bAUW'),
            'preferences' => json_encode([]),
            'ruolo' => 'admin',
        ]);
    }
}

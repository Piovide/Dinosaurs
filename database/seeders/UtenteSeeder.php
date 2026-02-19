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
        Utente::create([
            'nome' => 'Test',
            'cognome' => 'User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'preferences' => json_encode([])
        ]);

        Utente::create([
            'nome' => 'Admin',
            'cognome' => 'User',
            'username' => 'adminuser',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'),
            'preferences' => json_encode([])
        ]);
    }
}

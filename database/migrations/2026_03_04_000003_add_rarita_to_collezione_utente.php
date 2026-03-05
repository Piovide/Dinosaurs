<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('collezione_utente', function (Blueprint $table) {
            // Tracks which specific rarity version of a card the user owns.
            // NULL means the card has no rarity variants.
            $table->foreignId('rar_id_collezione_rarita')
                  ->nullable()
                  ->after('car_id_carta')
                  ->constrained('collezione_rarita', 'id_collezione_rarita')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('collezione_utente', function (Blueprint $table) {
            $table->dropForeign(['rar_id_collezione_rarita']);
            $table->dropColumn('rar_id_collezione_rarita');
        });
    }
};

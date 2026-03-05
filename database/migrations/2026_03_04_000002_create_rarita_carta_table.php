<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create the pivot table carta <-> collezione_rarita
        Schema::create('rarita_carta', function (Blueprint $table) {
            $table->id('id_rarita_carta');
            $table->foreignId('id_carta')
                  ->constrained('carta', 'id_carta')
                  ->onDelete('cascade');
            $table->foreignId('id_collezione_rarita')
                  ->constrained('collezione_rarita', 'id_collezione_rarita')
                  ->onDelete('cascade');
            $table->unique(['id_carta', 'id_collezione_rarita']);
            $table->timestamps();
        });

        // Migrate existing rar_id_rarita data into the new pivot table
        $carteConRarita = DB::table('carta')
            ->whereNotNull('rar_id_rarita')
            ->get(['id_carta', 'rar_id_rarita']);

        foreach ($carteConRarita as $carta) {
            DB::table('rarita_carta')->insertOrIgnore([
                'id_carta'             => $carta->id_carta,
                'id_collezione_rarita' => $carta->rar_id_rarita,
                'created_at'           => now(),
                'updated_at'           => now(),
            ]);
        }

        // Drop the old single-rarity FK column from carta
        Schema::table('carta', function (Blueprint $table) {
            $table->dropForeign(['rar_id_rarita']);
            $table->dropColumn('rar_id_rarita');
        });
    }

    public function down(): void
    {
        // Re-add the old column
        Schema::table('carta', function (Blueprint $table) {
            $table->foreignId('rar_id_rarita')
                  ->nullable()
                  ->after('art_id_artista')
                  ->constrained('collezione_rarita', 'id_collezione_rarita')
                  ->nullOnDelete();
        });

        // Migrate first rarity back (best-effort)
        $pivot = DB::table('rarita_carta')->get(['id_carta', 'id_collezione_rarita']);
        foreach ($pivot as $row) {
            DB::table('carta')
                ->where('id_carta', $row->id_carta)
                ->whereNull('rar_id_rarita')
                ->update(['rar_id_rarita' => $row->id_collezione_rarita]);
        }

        Schema::dropIfExists('rarita_carta');
    }
};

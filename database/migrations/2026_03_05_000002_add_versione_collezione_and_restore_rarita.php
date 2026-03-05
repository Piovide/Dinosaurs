<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Drop old per-card versione_carta (wrong concept) — FK in collezione_utente must go first
        Schema::table('collezione_utente', function (Blueprint $table) {
            if (Schema::hasColumn('collezione_utente', 'ver_id_versione')) {
                $table->dropForeign(['ver_id_versione']);
                $table->dropColumn('ver_id_versione');
            }
        });

        if (Schema::hasTable('versione_carta')) {
            Schema::dropIfExists('versione_carta');
        }

        // 2) Create versione_collezione — versions are defined per collection, not per card
        Schema::create('versione_collezione', function (Blueprint $table) {
            $table->id('id_versione');
            $table->foreignId('col_id_collezione')
                  ->constrained('collezione', 'id_collezione')
                  ->onDelete('cascade');
            $table->string('nome');
            $table->timestamps();
        });

        // 3) Restore rarita_carta pivot (cards ↔ rarities, many-to-many)
        // Drop first in case the earlier migration already created it with a different schema
        Schema::dropIfExists('rarita_carta');
        Schema::create('rarita_carta', function (Blueprint $table) {
            $table->unsignedBigInteger('id_carta');
            $table->unsignedBigInteger('id_collezione_rarita');
            $table->foreign('id_carta')->references('id_carta')->on('carta')->onDelete('cascade');
            $table->foreign('id_collezione_rarita')->references('id_collezione_rarita')->on('collezione_rarita')->onDelete('cascade');
            $table->unique(['id_carta', 'id_collezione_rarita']);
            $table->timestamps();
        });

        // 4) Re-add rar_id_collezione_rarita and ver_id_versione to collezione_utente
        Schema::table('collezione_utente', function (Blueprint $table) {
            if (!Schema::hasColumn('collezione_utente', 'rar_id_collezione_rarita')) {
                $table->unsignedBigInteger('rar_id_collezione_rarita')->nullable()->after('car_id_carta');
                $table->foreign('rar_id_collezione_rarita')
                      ->references('id_collezione_rarita')
                      ->on('collezione_rarita')
                      ->onDelete('set null');
            }

            if (!Schema::hasColumn('collezione_utente', 'ver_id_versione')) {
                $table->unsignedBigInteger('ver_id_versione')->nullable()->after('rar_id_collezione_rarita');
                $table->foreign('ver_id_versione')
                      ->references('id_versione')
                      ->on('versione_collezione')
                      ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('collezione_utente', function (Blueprint $table) {
            // Only drop ver_id_versione — rar_id_collezione_rarita is managed by 2026_03_04_000003
            if (Schema::hasColumn('collezione_utente', 'ver_id_versione')) {
                $table->dropForeign(['ver_id_versione']);
                $table->dropColumn('ver_id_versione');
            }
        });

        Schema::dropIfExists('rarita_carta');
        Schema::dropIfExists('versione_collezione');
    }
};

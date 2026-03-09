<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // carta ↔ collezione_rarita  (one card, one rarity)
        Schema::create('rarita_carta', function (Blueprint $table) {
            $table->unsignedBigInteger('id_carta');
            $table->unsignedBigInteger('id_collezione_rarita');
            $table->foreign('id_carta')
                  ->references('id_carta')->on('carta')
                  ->onDelete('cascade');
            $table->foreign('id_collezione_rarita')
                  ->references('id_collezione_rarita')->on('collezione_rarita')
                  ->onDelete('cascade');
            $table->unique(['id_carta', 'id_collezione_rarita']);
            $table->timestamps();
        });

        // carta ↔ collezione_tipologia  (many tipologie per card)
        Schema::create('carta_tipologia', function (Blueprint $table) {
            $table->unsignedBigInteger('id_carta');
            $table->unsignedBigInteger('id_collezione_tipologia');
            $table->foreign('id_carta')
                  ->references('id_carta')->on('carta')
                  ->onDelete('cascade');
            $table->foreign('id_collezione_tipologia')
                  ->references('id_collezione_tipologia')->on('collezione_tipologia')
                  ->onDelete('cascade');
            $table->unique(['id_carta', 'id_collezione_tipologia']);
            $table->timestamps();
        });

        // carta ↔ versione_collezione  (card available in multiple versions)
        Schema::create('carta_versione', function (Blueprint $table) {
            $table->unsignedBigInteger('id_carta');
            $table->unsignedBigInteger('id_versione');
            $table->foreign('id_carta')
                  ->references('id_carta')->on('carta')
                  ->onDelete('cascade');
            $table->foreign('id_versione')
                  ->references('id_versione')->on('versione_collezione')
                  ->onDelete('cascade');
            $table->unique(['id_carta', 'id_versione']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carta_versione');
        Schema::dropIfExists('carta_tipologia');
        Schema::dropIfExists('rarita_carta');
    }
};

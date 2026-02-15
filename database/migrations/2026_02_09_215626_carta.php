<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('carta', function (Blueprint $table) {
            $table->id('id_carta');
            $table->foreignId('col_id_collezione')->constrained('collezione', 'id_collezione');
            $table->string('titolo');
            $table->text('descrizione')->nullable();
            $table->foreignId('art_id_artista')->nullable()->constrained('artista', 'id_artista');
            $table->foreignId('dnz_id_rarita')->nullable()->constrained('dizionario', 'id_dizionario');
            $table->foreignId('dnz_id_tipo')->nullable()->constrained('dizionario', 'id_dizionario');
            $table->integer('numero')->nullable();
            $table->string('immagine_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('carta');
    }
};

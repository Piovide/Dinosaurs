<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carta', function (Blueprint $table) {
            $table->id('id_carta');
            $table->foreignId('col_id_collezione')
                  ->constrained('collezione', 'id_collezione');
            $table->string('titolo');
            $table->text('descrizione')->nullable();
            $table->foreignId('art_id_artista')
                  ->nullable()
                  ->constrained('artista', 'id_artista')
                  ->nullOnDelete();
            $table->integer('numero')->nullable();
            $table->string('prefisso', 20)->nullable();
            $table->string('suffisso', 20)->nullable();
            $table->string('immagine_url')->nullable();
            $table->string('immagine_retro_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carta');
    }
};

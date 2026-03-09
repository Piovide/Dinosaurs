<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collezione_rarita', function (Blueprint $table) {
            $table->id('id_collezione_rarita');
            $table->foreignId('col_id_collezione')
                  ->constrained('collezione', 'id_collezione')
                  ->onDelete('cascade');
            $table->string('nome');
            $table->string('icona')->nullable();
            $table->enum('tipo_icona', ['file', 'bootstrap'])->nullable();
            $table->timestamps();
        });

        Schema::create('collezione_tipologia', function (Blueprint $table) {
            $table->id('id_collezione_tipologia');
            $table->foreignId('col_id_collezione')
                  ->constrained('collezione', 'id_collezione')
                  ->onDelete('cascade');
            $table->string('nome');
            $table->string('icona')->nullable();
            $table->enum('tipo_icona', ['file', 'bootstrap'])->nullable();
            $table->timestamps();
        });

        Schema::create('versione_collezione', function (Blueprint $table) {
            $table->id('id_versione');
            $table->foreignId('col_id_collezione')
                  ->constrained('collezione', 'id_collezione')
                  ->onDelete('cascade');
            $table->string('nome');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('versione_collezione');
        Schema::dropIfExists('collezione_tipologia');
        Schema::dropIfExists('collezione_rarita');
    }
};

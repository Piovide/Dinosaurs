<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collezione', function (Blueprint $table) {
            $table->id('id_collezione');
            $table->string('nome');
            $table->text('descrizione')->nullable();
            $table->string('banner_url')->nullable();
            $table->integer('numero_carte');
            $table->date('data_uscita')->nullable();
            $table->timestamps();
        });

        Schema::create('artista', function (Blueprint $table) {
            $table->id('id_artista');
            $table->string('nominativo');
            $table->date('data_nascita')->nullable();
            $table->string('link_sito')->nullable();
            $table->string('link_social')->nullable();
            $table->timestamps();
        });

        Schema::create('dizionario', function (Blueprint $table) {
            $table->id('id_dizionario');
            $table->string('categoria');
            $table->string('descrizione');
            $table->boolean('stato')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dizionario');
        Schema::dropIfExists('artista');
        Schema::dropIfExists('collezione');
    }
};

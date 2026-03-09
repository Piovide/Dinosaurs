<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collezione_utente', function (Blueprint $table) {
            $table->id('id_collezione_utente');
            $table->unsignedBigInteger('utn_id_utente');
            $table->foreign('utn_id_utente')
                  ->references('id_utente')->on('utente')
                  ->onDelete('cascade');
            $table->foreignId('car_id_carta')
                  ->constrained('carta', 'id_carta');
            $table->unsignedBigInteger('rar_id_collezione_rarita')->nullable();
            $table->foreign('rar_id_collezione_rarita')
                  ->references('id_collezione_rarita')->on('collezione_rarita')
                  ->onDelete('set null');
            $table->unsignedBigInteger('ver_id_versione')->nullable();
            $table->foreign('ver_id_versione')
                  ->references('id_versione')->on('versione_collezione')
                  ->onDelete('set null');
            $table->integer('quantita')->default(0);
            $table->boolean('preferita')->default(false);
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collezione_utente');
    }
};

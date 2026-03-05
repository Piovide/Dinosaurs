<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pivot: a card can be associated with multiple alternative versions of its collection
        Schema::create('carta_versione', function (Blueprint $table) {
            $table->unsignedBigInteger('id_carta');
            $table->unsignedBigInteger('id_versione');
            $table->foreign('id_carta')->references('id_carta')->on('carta')->onDelete('cascade');
            $table->foreign('id_versione')->references('id_versione')->on('versione_collezione')->onDelete('cascade');
            $table->unique(['id_carta', 'id_versione']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carta_versione');
    }
};

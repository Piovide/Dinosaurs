<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collezione_regola_versione_rarita', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('col_id_collezione');
            $table->unsignedBigInteger('rarita_id');
            $table->unsignedBigInteger('versione_id');
            $table->timestamps();

            $table->foreign('col_id_collezione')
                ->references('id_collezione')->on('collezione')
                ->onDelete('cascade');

            $table->foreign('rarita_id')
                ->references('id_collezione_rarita')->on('collezione_rarita')
                ->onDelete('cascade');

            $table->foreign('versione_id')
                ->references('id_versione')->on('versione_collezione')
                ->onDelete('cascade');

            $table->unique(['col_id_collezione', 'rarita_id', 'versione_id'], 'uq_regola_collezione_rarita_versione');
            $table->index(['col_id_collezione', 'rarita_id'], 'idx_regola_collezione_rarita');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collezione_regola_versione_rarita');
    }
};

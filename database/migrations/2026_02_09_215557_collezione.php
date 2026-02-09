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
        Schema::create('collezione', function (Blueprint $table) {
            $table->id('id_collezione');
            $table->string('nome');
            $table->text('descrizione')->nullable();
            $table->integer('numero_carte');
            $table->date('data_uscita')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collezione');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('collezione_utente', function (Blueprint $table) {
            $table->id('id_collezione_utente');
            $table->foreignId('utn_id_utente')->constrained('users');
            $table->foreignId('car_id_carta')->constrained('carta', 'id_carta');
            $table->integer('quantita')->default(1);
            $table->boolean('preferita')->default(false);
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('collezione_utente');
    }
};

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
        Schema::table('utente', function (Blueprint $table) {
            $table->enum('ruolo', ['utente', 'moderatore', 'admin'])->default('utente')->after('preferences');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('utente', function (Blueprint $table) {
            $table->dropColumn('ruolo');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carta', function (Blueprint $table) {
            $table->string('immagine_retro_url')->nullable()->after('immagine_url');
        });
    }

    public function down(): void
    {
        Schema::table('carta', function (Blueprint $table) {
            $table->dropColumn('immagine_retro_url');
        });
    }
};

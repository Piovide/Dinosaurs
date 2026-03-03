<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('collezione_rarita', function (Blueprint $table) {
            // 'file' = uploaded image/svg file stored in storage/public
            // 'bootstrap' = Bootstrap Icons sprite name (e.g. 'star-fill')
            $table->enum('tipo_icona', ['file', 'bootstrap'])->nullable()->after('icona');
        });

        Schema::table('collezione_tipologia', function (Blueprint $table) {
            $table->enum('tipo_icona', ['file', 'bootstrap'])->nullable()->after('icona');
        });
    }

    public function down(): void
    {
        Schema::table('collezione_rarita', function (Blueprint $table) {
            $table->dropColumn('tipo_icona');
        });

        Schema::table('collezione_tipologia', function (Blueprint $table) {
            $table->dropColumn('tipo_icona');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carta', function (Blueprint $table) {
            // Drop old dizionario-based FK columns
            $table->dropForeign(['dnz_id_rarita']);
            $table->dropForeign(['dnz_id_tipo']);
            $table->dropColumn(['dnz_id_rarita', 'dnz_id_tipo']);

            // Add new collection-scoped FK columns
            $table->foreignId('rar_id_rarita')
                  ->nullable()
                  ->after('art_id_artista')
                  ->constrained('collezione_rarita', 'id_collezione_rarita')
                  ->nullOnDelete();

            $table->foreignId('tip_id_tipologia')
                  ->nullable()
                  ->after('rar_id_rarita')
                  ->constrained('collezione_tipologia', 'id_collezione_tipologia')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('carta', function (Blueprint $table) {
            $table->dropForeign(['rar_id_rarita']);
            $table->dropForeign(['tip_id_tipologia']);
            $table->dropColumn(['rar_id_rarita', 'tip_id_tipologia']);

            $table->foreignId('dnz_id_rarita')->nullable()->constrained('dizionario', 'id_dizionario');
            $table->foreignId('dnz_id_tipo')->nullable()->constrained('dizionario', 'id_dizionario');
        });
    }
};

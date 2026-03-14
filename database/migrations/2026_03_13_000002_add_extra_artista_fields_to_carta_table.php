<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('carta')) {
            return;
        }

        Schema::table('carta', function (Blueprint $table) {
            if (!Schema::hasColumn('carta', 'art_id_artista_secondario')) {
                $table->foreignId('art_id_artista_secondario')
                    ->nullable()
                    ->after('art_id_artista')
                    ->constrained('artista', 'id_artista')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('carta', 'art_id_artista_back')) {
                $table->foreignId('art_id_artista_back')
                    ->nullable()
                    ->after('art_id_artista_secondario')
                    ->constrained('artista', 'id_artista')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('carta')) {
            return;
        }

        Schema::table('carta', function (Blueprint $table) {
            if (Schema::hasColumn('carta', 'art_id_artista_back')) {
                $table->dropConstrainedForeignId('art_id_artista_back');
            }

            if (Schema::hasColumn('carta', 'art_id_artista_secondario')) {
                $table->dropConstrainedForeignId('art_id_artista_secondario');
            }
        });
    }
};

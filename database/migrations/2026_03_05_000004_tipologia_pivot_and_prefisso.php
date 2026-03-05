<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Add optional prefix field to carta
        Schema::table('carta', function (Blueprint $table) {
            $table->string('prefisso', 20)->nullable()->after('numero');
        });

        // 2) Create pivot: a card can belong to multiple tipologie
        Schema::create('carta_tipologia', function (Blueprint $table) {
            $table->unsignedBigInteger('id_carta');
            $table->unsignedBigInteger('id_collezione_tipologia');
            $table->foreign('id_carta')->references('id_carta')->on('carta')->onDelete('cascade');
            $table->foreign('id_collezione_tipologia')->references('id_collezione_tipologia')->on('collezione_tipologia')->onDelete('cascade');
            $table->unique(['id_carta', 'id_collezione_tipologia']);
            $table->timestamps();
        });

        // 3) Migrate existing single tip_id_tipologia → pivot
        if (Schema::hasColumn('carta', 'tip_id_tipologia')) {
            DB::table('carta')
                ->whereNotNull('tip_id_tipologia')
                ->get(['id_carta', 'tip_id_tipologia'])
                ->each(function ($row) {
                    DB::table('carta_tipologia')->insertOrIgnore([
                        'id_carta'                => $row->id_carta,
                        'id_collezione_tipologia' => $row->tip_id_tipologia,
                        'created_at'              => now(),
                        'updated_at'              => now(),
                    ]);
                });

            // 4) Drop old FK column
            Schema::table('carta', function (Blueprint $table) {
                $table->dropForeign(['tip_id_tipologia']);
                $table->dropColumn('tip_id_tipologia');
            });
        }
    }

    public function down(): void
    {
        // Re-add single-tipologia column (best-effort: picks first from pivot)
        Schema::table('carta', function (Blueprint $table) {
            $table->foreignId('tip_id_tipologia')
                  ->nullable()
                  ->after('art_id_artista')
                  ->constrained('collezione_tipologia', 'id_collezione_tipologia')
                  ->nullOnDelete();
        });

        DB::table('carta_tipologia')
            ->get(['id_carta', 'id_collezione_tipologia'])
            ->each(function ($row) {
                DB::table('carta')
                    ->where('id_carta', $row->id_carta)
                    ->whereNull('tip_id_tipologia')
                    ->update(['tip_id_tipologia' => $row->id_collezione_tipologia]);
            });

        Schema::dropIfExists('carta_tipologia');

        Schema::table('carta', function (Blueprint $table) {
            $table->dropColumn('prefisso');
        });
    }
};

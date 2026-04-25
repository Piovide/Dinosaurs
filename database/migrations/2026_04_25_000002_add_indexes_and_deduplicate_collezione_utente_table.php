<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function (): void {
            $duplicateGroups = DB::table('collezione_utente')
                ->select([
                    'utn_id_utente',
                    'car_id_carta',
                    'rar_id_collezione_rarita',
                    'ver_id_versione',
                    DB::raw('MIN(id_collezione_utente) as keeper_id'),
                    DB::raw('SUM(quantita) as quantita_totale'),
                    DB::raw('COUNT(*) as rows_count'),
                ])
                ->groupBy('utn_id_utente', 'car_id_carta', 'rar_id_collezione_rarita', 'ver_id_versione')
                ->havingRaw('COUNT(*) > 1')
                ->get();

            foreach ($duplicateGroups as $group) {
                DB::table('collezione_utente')
                    ->where('id_collezione_utente', $group->keeper_id)
                    ->update([
                        'quantita' => (int) $group->quantita_totale,
                        'updated_at' => now(),
                    ]);

                $duplicateIds = DB::table('collezione_utente')
                    ->where('utn_id_utente', $group->utn_id_utente)
                    ->where('car_id_carta', $group->car_id_carta)
                    ->when(
                        $group->rar_id_collezione_rarita === null,
                        fn ($query) => $query->whereNull('rar_id_collezione_rarita'),
                        fn ($query) => $query->where('rar_id_collezione_rarita', $group->rar_id_collezione_rarita)
                    )
                    ->when(
                        $group->ver_id_versione === null,
                        fn ($query) => $query->whereNull('ver_id_versione'),
                        fn ($query) => $query->where('ver_id_versione', $group->ver_id_versione)
                    )
                    ->where('id_collezione_utente', '<>', $group->keeper_id)
                    ->pluck('id_collezione_utente');

                if ($duplicateIds->isNotEmpty()) {
                    DB::table('collezione_utente')
                        ->whereIn('id_collezione_utente', $duplicateIds->all())
                        ->delete();
                }
            }
        });

        Schema::table('collezione_utente', function (Blueprint $table) {
            $table->index(['utn_id_utente', 'car_id_carta'], 'idx_collezione_utente_user_card');
            $table->index(
                ['utn_id_utente', 'car_id_carta', 'rar_id_collezione_rarita', 'ver_id_versione'],
                'idx_collezione_utente_lookup_combo'
            );
        });
    }

    public function down(): void
    {
        Schema::table('collezione_utente', function (Blueprint $table) {
            $table->dropIndex('idx_collezione_utente_lookup_combo');
            $table->dropIndex('idx_collezione_utente_user_card');
        });
    }
};

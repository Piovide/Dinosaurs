<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('sitemap:robots', function () {
    $sitemapUrl = rtrim(config('app.url'), '/') . '/sitemap.xml';
    $content = "User-agent: *\n"
        . "Disallow: /admin/\n"
        . "Disallow: /api/\n"
        . "Disallow: /email/\n"
        . "Disallow: /login\n"
        . "Disallow: /register\n"
        . "Disallow: /forgot-password\n"
        . "Disallow: /reset-password\n\n"
        . "Sitemap: {$sitemapUrl}\n";

    file_put_contents(public_path('robots.txt'), $content);
    $this->info("robots.txt aggiornato: Sitemap -> {$sitemapUrl}");
})->purpose('Aggiorna robots.txt con il link alla sitemap corretto per l\'ambiente attuale');

Artisan::command('collezione:health-check', function () {
    $table = 'collezione_utente';

    $rows = DB::table($table)->count();

    $duplicateBase = DB::table($table)
        ->select([
            'utn_id_utente',
            'car_id_carta',
            'rar_id_collezione_rarita',
            'ver_id_versione',
            DB::raw('COUNT(*) as c'),
            DB::raw('SUM(quantita) as quantita_totale'),
        ])
        ->groupBy('utn_id_utente', 'car_id_carta', 'rar_id_collezione_rarita', 'ver_id_versione')
        ->havingRaw('COUNT(*) > 1');

    $duplicateGroups = DB::query()->fromSub($duplicateBase, 'd')->count();
    $rowsInDuplicateGroups = (int) DB::query()
        ->fromSub($duplicateBase, 'd')
        ->sum('c');

    $this->line('Table: ' . $table);
    $this->info('rows=' . $rows);
    $this->info('duplicate_groups=' . $duplicateGroups);
    $this->info('rows_in_duplicate_groups=' . $rowsInDuplicateGroups);

    if ($duplicateGroups > 0) {
        $this->warn('Top 10 gruppi duplicati:');

        $top = DB::query()
            ->fromSub($duplicateBase, 'd')
            ->orderByDesc('c')
            ->limit(10)
            ->get();

        $this->table(
            ['utn_id_utente', 'car_id_carta', 'rarita', 'versione', 'rows', 'quantita_totale'],
            $top->map(fn ($row) => [
                $row->utn_id_utente,
                $row->car_id_carta,
                $row->rar_id_collezione_rarita,
                $row->ver_id_versione,
                $row->c,
                $row->quantita_totale,
            ])->all()
        );
    }
})->purpose('Mostra metriche di salute e duplicati per collezione_utente');

<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

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

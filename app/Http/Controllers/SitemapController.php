<?php

namespace App\Http\Controllers;

use App\Models\Artista;
use App\Models\Carta;
use App\Models\Collezione;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $appUrl = rtrim(config('app.url'), '/');

        $staticUrls = collect([
            ['loc' => $appUrl . '/',          'priority' => '1.0', 'changefreq' => 'daily'],
            ['loc' => $appUrl . '/artisti',   'priority' => '0.7', 'changefreq' => 'weekly'],
            ['loc' => $appUrl . '/crediti',   'priority' => '0.3', 'changefreq' => 'yearly'],
        ]);

        $collezioni = Collezione::orderBy('data_uscita')->get()->map(fn($c) => [
            'loc'        => $appUrl . '/?' . http_build_query(['collezione' => $c->id_collezione]),
            'priority'   => '0.8',
            'changefreq' => 'weekly',
            'lastmod'    => $c->updated_at?->toAtomString(),
        ]);

        $carte = Carta::orderBy('id_carta')->get(['id_carta', 'updated_at'])->map(fn($c) => [
            'loc'        => $appUrl . '/carte/' . $c->id_carta,
            'priority'   => '0.6',
            'changefreq' => 'monthly',
            'lastmod'    => $c->updated_at?->toAtomString(),
        ]);

        $artisti = Artista::orderBy('id_artista')->get(['id_artista', 'updated_at'])->map(fn($a) => [
            'loc'        => $appUrl . '/artisti/' . $a->id_artista,
            'priority'   => '0.6',
            'changefreq' => 'monthly',
            'lastmod'    => $a->updated_at?->toAtomString(),
        ]);

        $urls = $staticUrls->concat($collezioni)->concat($carte)->concat($artisti);

        $xml = view('sitemap', compact('urls'))->render();

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}

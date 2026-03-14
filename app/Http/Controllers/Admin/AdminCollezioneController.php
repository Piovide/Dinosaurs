<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Carta;
use App\Models\Collezione;
use App\Models\Artista;
use App\Models\CollezioneRarita;
use App\Models\CollezioneTipologia;
use App\Models\VersioneCollezione;
use App\Services\CartaImageService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class AdminCollezioneController extends Controller
{
    public function index()
    {
        $collezioni = Collezione::withCount('carte')->orderBy('data_uscita', 'desc')->paginate(20);
        return view('admin.collezioni.index', compact('collezioni'));
    }

    public function show($id)
    {
        $collezione = Collezione::findOrFail($id);
        $carte      = $collezione->carte()->with(['artista', 'raritas', 'tipologie'])->orderBy('id_carta')->paginate(20);
        $rarita     = $collezione->rarita()->orderBy('nome')->get();
        $tipologie  = $collezione->tipologie()->orderBy('nome')->get();
        $versioni   = $collezione->versioni()->orderBy('nome')->get();

        $regoleVersioni = DB::table('collezione_regola_versione_rarita as regole')
            ->join('collezione_rarita as rarita', 'rarita.id_collezione_rarita', '=', 'regole.rarita_id')
            ->join('versione_collezione as versione', 'versione.id_versione', '=', 'regole.versione_id')
            ->where('regole.col_id_collezione', $id)
            ->orderBy('rarita.nome')
            ->orderBy('versione.nome')
            ->get([
                'regole.rarita_id',
                'rarita.nome as rarita_nome',
                'versione.id_versione',
                'versione.nome as versione_nome',
            ])
            ->groupBy('rarita_id');

        return view('admin.collezioni.show', compact('collezione', 'carte', 'rarita', 'tipologie', 'versioni', 'regoleVersioni'));
    }

    public function create()
    {
        return view('admin.collezioni.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome'         => 'required|string|max:255',
            'descrizione'  => 'nullable|string',
            'numero_carte' => 'required|integer|min:1',
            'data_uscita'  => 'nullable|date',
        ]);

        $collezione = Collezione::create($request->only('nome', 'descrizione', 'numero_carte', 'data_uscita'));

        return redirect()->route('admin.collezioni.show', $collezione->id_collezione)
            ->with('success', 'Collezione creata con successo.');
    }

    public function edit($id)
    {
        $collezione = Collezione::findOrFail($id);
        return view('admin.collezioni.edit', compact('collezione'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nome'         => 'required|string|max:255',
            'descrizione'  => 'nullable|string',
            'numero_carte' => 'required|integer|min:1',
            'data_uscita'  => 'nullable|date',
        ]);

        $collezione = Collezione::findOrFail($id);
        $collezione->update($request->only('nome', 'descrizione', 'numero_carte', 'data_uscita'));

        return redirect()->route('admin.collezioni.show', $id)
            ->with('success', 'Collezione aggiornata con successo.');
    }

    public function destroy($id)
    {
        $collezione = Collezione::findOrFail($id);
        $collezione->delete();

        return redirect()->route('admin.collezioni.index')
            ->with('success', 'Collezione eliminata con successo.');
    }

    public function createCarta($collezioneId)
    {
        $collezione = Collezione::findOrFail($collezioneId);
        $artisti    = Artista::orderBy('nominativo')->get();
        $rarita     = CollezioneRarita::where('col_id_collezione', $collezioneId)->orderBy('nome')->get();
        $tipologie  = CollezioneTipologia::where('col_id_collezione', $collezioneId)->orderBy('nome')->get();
        $versioni   = VersioneCollezione::where('col_id_collezione', $collezioneId)->orderBy('nome')->get();

        return view('admin.carte.create', compact('collezione', 'artisti', 'rarita', 'tipologie', 'versioni'));
    }

    // Salva la nuova carta
    public function storeCarta(Request $request, $collezioneId, CartaImageService $imageService)
    {
        $request->validate([
            'titolo'                   => 'required|string|max:255',
            'descrizione'              => 'nullable|string',
            'art_id_artista'           => 'nullable|exists:artista,id_artista',
            'art_id_artista_secondario' => 'nullable|exists:artista,id_artista',
            'art_id_artista_back'      => 'nullable|exists:artista,id_artista',
            'numero'                   => 'nullable|integer|min:1',
            'prefisso'                 => 'nullable|string|max:20',
            'suffisso'                 => 'nullable|string|max:20',
            'rar_id_collezione_rarita' => 'nullable|exists:collezione_rarita,id_collezione_rarita',
            'tipologia_ids'            => 'nullable|array',
            'tipologia_ids.*'          => 'exists:collezione_tipologia,id_collezione_tipologia',
            'versione_ids'             => 'nullable|array',
            'versione_ids.*'           => 'exists:versione_collezione,id_versione',
            'immagine'                 => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10000',
        ], [
            'immagine.max' => 'Il file immagine non deve superare 10MB.',
            'immagine.image' => 'Il file deve essere un\'immagine valida.',
            'immagine.mimes' => 'Il file deve essere in formato: JPEG, PNG, JPG, GIF o WebP.',
        ]);

        $collezione  = Collezione::findOrFail($collezioneId);
        $immagineUrl = $request->hasFile('immagine')
            ? $imageService->store($request->file('immagine'), $collezione)
            : null;

        $carta = Carta::create([
            'col_id_collezione' => $collezioneId,
            'titolo'            => $request->titolo,
            'descrizione'       => $request->descrizione,
            'art_id_artista'    => $request->art_id_artista,
            'art_id_artista_secondario' => $request->art_id_artista_secondario,
            'art_id_artista_back' => $request->art_id_artista_back,
            'numero'            => $request->numero,
            'prefisso'          => $request->prefisso ?: null,
            'suffisso'          => $request->suffisso ?: null,
            'immagine_url'      => $immagineUrl,
        ]);

        // Sync rarity pivot (single optional rarity)
        $raritaId = $request->filled('rar_id_collezione_rarita') ? [$request->rar_id_collezione_rarita] : [];
        $carta->raritas()->sync($raritaId);

        // Sync tipologie pivot
        $carta->tipologie()->sync($request->input('tipologia_ids', []));

        // Sync alternative versions pivot, with optional auto-rules by rarity.
        $versioneIds = $request->input('versione_ids');
        if (is_array($versioneIds)) {
            $carta->versioni()->sync($versioneIds);
        } else {
            $carta->versioni()->sync($this->versioniAutomaticheDaRarita($collezioneId, $raritaId[0] ?? null));
        }

        return redirect()->route('admin.collezioni.show', $collezioneId)
            ->with('success', 'Carta aggiunta con successo.');
    }

    public function importCarteJson(Request $request, $collezioneId)
    {
        $request->validate([
            'json_file' => 'required|file|mimetypes:application/json,text/plain|max:10240',
        ], [
            'json_file.required' => 'Seleziona un file JSON da importare.',
            'json_file.max' => 'Il file JSON non deve superare 10MB.',
        ]);

        $collezione = Collezione::findOrFail($collezioneId);
        $payload = json_decode(file_get_contents($request->file('json_file')->getRealPath()), true);

        if (!is_array($payload) || !isset($payload['cards']) || !is_array($payload['cards'])) {
            return back()->with('error', 'JSON non valido. Struttura attesa: {"cards": [...]}');
        }

        $cards = $payload['cards'];
        $artistiByNome = Artista::query()->get()->mapWithKeys(function ($artista) {
            return [$this->normalizzaNome($artista->nominativo) => $artista->id_artista];
        });
        $raritaByNome = CollezioneRarita::where('col_id_collezione', $collezioneId)->get()->mapWithKeys(function ($item) {
            return [$this->normalizzaNome($item->nome) => $item->id_collezione_rarita];
        });
        $tipologieByNome = CollezioneTipologia::where('col_id_collezione', $collezioneId)->get()->mapWithKeys(function ($item) {
            return [$this->normalizzaNome($item->nome) => $item->id_collezione_tipologia];
        });
        $versioniByNome = VersioneCollezione::where('col_id_collezione', $collezioneId)->get()->mapWithKeys(function ($item) {
            return [$this->normalizzaNome($item->nome) => $item->id_versione];
        });

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $warnings = [];

        foreach ($cards as $index => $cardData) {
            $linea = $index + 1;
            if (!is_array($cardData)) {
                $skipped++;
                $warnings[] = "Record {$linea}: formato non valido.";
                continue;
            }

            $numeroRaw = $cardData['numero'] ?? null;
            $titolo = trim((string) ($cardData['titolo'] ?? ''));

            if (!$titolo || !is_numeric($numeroRaw) || (int) $numeroRaw < 1) {
                $skipped++;
                $warnings[] = "Record {$linea}: servono almeno numero intero >= 1 e titolo.";
                continue;
            }

            $numero = (int) $numeroRaw;

            $artistaId = null;
            $artistaNome = trim((string) ($cardData['artista'] ?? ''));
            if ($artistaNome !== '') {
                $artistaId = $artistiByNome->get($this->normalizzaNome($artistaNome));
                if (!$artistaId) {
                    $warnings[] = "Record {$linea}: artista '{$artistaNome}' non trovato, valore ignorato.";
                }
            }

            $artistaSecondarioId = null;
            $artistaSecondarioNome = trim((string) ($cardData['artista_secondario'] ?? ''));
            if ($artistaSecondarioNome !== '') {
                $artistaSecondarioId = $artistiByNome->get($this->normalizzaNome($artistaSecondarioNome));
                if (!$artistaSecondarioId) {
                    $warnings[] = "Record {$linea}: artista secondario '{$artistaSecondarioNome}' non trovato, valore ignorato.";
                }
            }

            $artistaBackId = null;
            $artistaBackNome = trim((string) ($cardData['artista_back'] ?? ''));
            if ($artistaBackNome !== '') {
                $artistaBackId = $artistiByNome->get($this->normalizzaNome($artistaBackNome));
                if (!$artistaBackId) {
                    $warnings[] = "Record {$linea}: artista back '{$artistaBackNome}' non trovato, valore ignorato.";
                }
            }

            $raritaId = null;
            $raritaNome = trim((string) ($cardData['rarita'] ?? ''));
            if ($raritaNome !== '') {
                $raritaId = $raritaByNome->get($this->normalizzaNome($raritaNome));
                if (!$raritaId) {
                    $warnings[] = "Record {$linea}: rarita '{$raritaNome}' non trovata, valore ignorato.";
                }
            }

            $tipologiaIds = [];
            foreach ((array) ($cardData['tipologie'] ?? []) as $tipologiaNome) {
                $tipologiaNome = trim((string) $tipologiaNome);
                if ($tipologiaNome === '') {
                    continue;
                }
                $tipologiaId = $tipologieByNome->get($this->normalizzaNome($tipologiaNome));
                if ($tipologiaId) {
                    $tipologiaIds[] = $tipologiaId;
                } else {
                    $warnings[] = "Record {$linea}: tipologia '{$tipologiaNome}' non trovata.";
                }
            }

            $versioneIds = [];
            $versioniPayload = (array) ($cardData['versioni'] ?? []);
            foreach ($versioniPayload as $versioneNome) {
                $versioneNome = trim((string) $versioneNome);
                if ($versioneNome === '') {
                    continue;
                }
                $versioneId = $versioniByNome->get($this->normalizzaNome($versioneNome));
                if ($versioneId) {
                    $versioneIds[] = $versioneId;
                } else {
                    $warnings[] = "Record {$linea}: versione '{$versioneNome}' non trovata.";
                }
            }

            if (empty($versioneIds) && $raritaId) {
                $versioneIds = $this->versioniAutomaticheDaRarita($collezioneId, (int) $raritaId);
            }

            $carta = Carta::where('col_id_collezione', $collezioneId)
                ->where('numero', $numero)
                ->first();

            $payloadCarta = [
                'col_id_collezione' => $collezioneId,
                'titolo' => $titolo,
                'descrizione' => $cardData['descrizione'] ?? null,
                'art_id_artista' => $artistaId,
                'art_id_artista_secondario' => $artistaSecondarioId,
                'art_id_artista_back' => $artistaBackId,
                'numero' => $numero,
                'prefisso' => trim((string) ($cardData['prefisso'] ?? '')) ?: null,
                'suffisso' => trim((string) ($cardData['suffisso'] ?? '')) ?: null,
            ];

            if ($carta) {
                $carta->update($payloadCarta);
                $updated++;
            } else {
                $carta = Carta::create($payloadCarta);
                $created++;
            }

            $carta->raritas()->sync($raritaId ? [(int) $raritaId] : []);
            $carta->tipologie()->sync(array_values(array_unique($tipologiaIds)));
            $carta->versioni()->sync(array_values(array_unique($versioneIds)));
        }

        $summary = "Import JSON completato per {$collezione->nome}: create {$created}, aggiornate {$updated}, saltate {$skipped}.";
        if (!empty($warnings)) {
            $summary .= ' Avvisi: ' . implode(' | ', array_slice($warnings, 0, 6));
            if (count($warnings) > 6) {
                $summary .= ' | ...';
            }
        }

        return back()->with('success', $summary);
    }

    public function importImmaginiCarte(Request $request, $collezioneId, CartaImageService $imageService)
    {
        $request->validate([
            'immagini' => 'required|array|min:1',
            'immagini.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10000',
        ], [
            'immagini.required' => 'Seleziona almeno un file immagine.',
            'immagini.*.image' => 'Tutti i file devono essere immagini valide.',
            'immagini.*.mimes' => 'Sono ammessi solo JPEG, PNG, JPG, GIF e WebP.',
        ]);

        $collezione = Collezione::findOrFail($collezioneId);
        $collegate = 0;
        $nomiInvalidi = 0;
        $carteMancanti = 0;

        foreach ($request->file('immagini', []) as $file) {
            if (!$file instanceof UploadedFile) {
                continue;
            }

            $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            if (!ctype_digit($baseName)) {
                $nomiInvalidi++;
                continue;
            }

            $numero = (int) $baseName;
            $carta = Carta::where('col_id_collezione', $collezioneId)
                ->where('numero', $numero)
                ->first();

            if (!$carta) {
                $carteMancanti++;
                continue;
            }

            $imageService->delete($carta->immagine_url);
            $carta->update([
                'immagine_url' => $imageService->store($file, $collezione),
            ]);
            $collegate++;
        }

        return back()->with(
            'success',
            "Import immagini completato: associate {$collegate}, nomi non numerici {$nomiInvalidi}, carte non trovate {$carteMancanti}."
        );
    }

    public function storeRegolaVersioniPerRarita(Request $request, $collezioneId)
    {
        $request->validate([
            'rarita_id' => 'required|exists:collezione_rarita,id_collezione_rarita',
            'versione_ids' => 'nullable|array',
            'versione_ids.*' => 'exists:versione_collezione,id_versione',
        ]);

        $raritaId = (int) $request->input('rarita_id');
        $versioneIds = collect($request->input('versione_ids', []))
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values();

        $raritaValida = CollezioneRarita::where('col_id_collezione', $collezioneId)
            ->where('id_collezione_rarita', $raritaId)
            ->exists();

        if (!$raritaValida) {
            return back()->with('error', 'La rarita selezionata non appartiene a questa collezione.');
        }

        if ($versioneIds->isNotEmpty()) {
            $versioniValide = VersioneCollezione::where('col_id_collezione', $collezioneId)
                ->whereIn('id_versione', $versioneIds)
                ->count();

            if ($versioniValide !== $versioneIds->count()) {
                return back()->with('error', 'Una o piu versioni selezionate non appartengono a questa collezione.');
            }
        }

        DB::transaction(function () use ($collezioneId, $raritaId, $versioneIds) {
            DB::table('collezione_regola_versione_rarita')
                ->where('col_id_collezione', $collezioneId)
                ->where('rarita_id', $raritaId)
                ->delete();

            if ($versioneIds->isNotEmpty()) {
                $now = now();
                $rows = $versioneIds->map(function ($versioneId) use ($collezioneId, $raritaId, $now) {
                    return [
                        'col_id_collezione' => $collezioneId,
                        'rarita_id' => $raritaId,
                        'versione_id' => $versioneId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                })->all();

                DB::table('collezione_regola_versione_rarita')->insert($rows);
            }
        });

        if ($versioneIds->isEmpty()) {
            return back()->with('success', 'Regola svuotata: nessuna versione automatica per la rarita selezionata.');
        }

        return back()->with('success', 'Regola salvata con successo.');
    }

    public function destroyRegolaVersioniPerRarita($collezioneId, $raritaId)
    {
        DB::table('collezione_regola_versione_rarita')
            ->where('col_id_collezione', $collezioneId)
            ->where('rarita_id', $raritaId)
            ->delete();

        return back()->with('success', 'Regola eliminata con successo.');
    }

    private function versioniAutomaticheDaRarita(int $collezioneId, ?int $raritaId): array
    {
        if (!$raritaId) {
            return [];
        }

        return DB::table('collezione_regola_versione_rarita')
            ->where('col_id_collezione', $collezioneId)
            ->where('rarita_id', $raritaId)
            ->pluck('versione_id')
            ->map(fn($id) => (int) $id)
            ->all();
    }

    private function normalizzaNome(?string $value): string
    {
        return mb_strtolower(trim((string) $value));
    }
}

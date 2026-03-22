@props(['carta'])
@php
    $raritas = $carta->raritas;
    $firstRarita = $raritas->first();
    $versioniRaw = $carta->versioni ?? collect();
    $tipologie = $carta->tipologie ?? collect();

    // Load user quantities indexed by "raritaId__versioneId"
    $combos = [];
    if (auth()->check()) {
        Auth::user()
            ->collezione_utente()
            ->where('car_id_carta', $carta->id_carta)
            ->get(['rar_id_collezione_rarita', 'ver_id_versione', 'quantita'])
            ->each(function ($r) use (&$combos) {
                $k = ($r->rar_id_collezione_rarita ?? '') . '__' . ($r->ver_id_versione ?? '');
                $combos[$k] = (int) $r->quantita;
            });
    }
    $combosJson = json_encode($combos);

    $versionQtyById = [];
    foreach ($combos as $comboKey => $qty) {
        [$raritaKey, $versioneKey] = explode('__', $comboKey . '__');
        if ($versioneKey !== '') {
            $versionQtyById[$versioneKey] = ($versionQtyById[$versioneKey] ?? 0) + (int) $qty;
        }
    }

    // Support nested markdown syntax: __[NomeVersione]-Opzione
    // Nested versions are grouped by NomeVersione and rendered as a single select.
    $nestedVersionGroups = [];
    $regularVersions = [];

    foreach ($versioniRaw as $v) {
        $nome = trim((string) $v->nome);
        $qty = (int) ($versionQtyById[(string) $v->id_versione] ?? 0);

        if (preg_match('/^__\[([^\]]+)\]-(.+)$/u', $nome, $m)) {
            $groupName = trim((string) $m[1]);
            $optionLabel = trim((string) $m[2]);
            if ($groupName !== '' && $optionLabel !== '') {
                if (!isset($nestedVersionGroups[$groupName])) {
                    $nestedVersionGroups[$groupName] = [];
                }
                $nestedVersionGroups[$groupName][] = [
                    'id' => $v->id_versione,
                    'label' => $optionLabel,
                    'qty' => $qty,
                ];
                continue;
            }
        }

        $regularVersions[] = [
            'id' => $v->id_versione,
            'label' => $v->nome,
            'qty' => $qty,
        ];
    }

    usort($regularVersions, function ($a, $b) {
        return strnatcasecmp((string) ($a['label'] ?? ''), (string) ($b['label'] ?? ''));
    });

    foreach ($nestedVersionGroups as &$options) {
        usort($options, function ($a, $b) {
            return strnatcasecmp((string) ($a['label'] ?? ''), (string) ($b['label'] ?? ''));
        });
    }
    unset($options);

    if (!empty($nestedVersionGroups)) {
        uksort($nestedVersionGroups, function ($a, $b) {
            return strnatcasecmp((string) $a, (string) $b);
        });
    }

    $hasVersioni = !empty($regularVersions) || !empty($nestedVersionGroups);

    // Initial state: first rarity (or null) + no version
    $firstRaritaId = $firstRarita?->id_collezione_rarita ?? '';
    $initialKey = $firstRaritaId . '__';
    $initialQty = $combos[$initialKey] ?? 0;
@endphp
<div class="col-6 col-sm-4 col-md-3 col-xl-2 col-xxl-carte">
    <div class="card h-100 p-3 rounded" data-carta-id="{{ $carta->id_carta }}" data-combos="{{ $combosJson }}">
        <div class="img-container position-relative">
            @if ($carta->immagine_asset === null)
                <div
                    class="placeholder-image d-flex align-items-center justify-content-center bg-secondary text-white h-100">
                    Immagine non ancora disponibile
                </div>
            @else
                <img src="{{ $carta->immagine_asset }}" class="card-img-top carta-image" alt="{{ $carta->titolo }}"
                    data-carta-id="{{ $carta->id_carta }}" style="cursor: pointer;">
            @endif
            <div
                class="card-info position-absolute bottom-0 start-0 p-2 bg-dark bg-opacity-50 text-white rounded-end rounded-bottom-0">
                <small class="d-flex align-items-center gap-1 flex-wrap">
                    {{ $carta->prefisso ? $carta->prefisso : '' }}{{ $carta->numero }}{{ $carta->suffisso ? $carta->suffisso : '' }}
                    / {{ $carta->collezione->numero_carte }}
                    @if ($firstRarita)
                        <x-icona-badge :record="$firstRarita" size="14px" />
                    @endif
                </small>
            </div>
        </div>
        <div class="card-body d-flex flex-column">
            <h5 class="card-title text-center">{{ $carta->titolo }}</h5>

            {{-- Tipologia + Rarità badges side by side --}}
            @if ($tipologie->isNotEmpty() || $firstRarita)
                <div class="d-flex justify-content-center align-items-center gap-1 flex-wrap mb-1">
                    @foreach ($tipologie as $t)
                        <span class="badge bg-info text-dark d-inline-flex align-items-center gap-1">
                            <x-icona-badge :record="$t" size="12px" />
                            {{ $t->nome }}
                        </span>
                    @endforeach
                    @if ($firstRarita)
                        <span class="badge bg-warning text-dark d-inline-flex align-items-center gap-1">
                            <x-icona-badge :record="$firstRarita" size="12px" />
                            {{ $firstRarita->nome }}
                        </span>
                    @endif
                </div>
            @endif

            {{-- Versione selectors --}}
            @if ($hasVersioni)
                {{-- Mobile: unica <select> con tutte le versioni --}}
                <div class="d-md-none mb-1 px-1">
                    <select class="versione-select-mobile w-100" data-carta-id="{{ $carta->id_carta }}">
                        <option value="" selected>Base</option>
                        @foreach ($regularVersions as $v)
                            <option value="{{ $v['id'] }}" data-versione-id="{{ $v['id'] }}">
                                {{ $v['label'] }}@if ($v['qty'] > 0)
                                    ({{ $v['qty'] }})
                                @endif
                            </option>
                        @endforeach
                        @foreach ($nestedVersionGroups as $groupName => $options)
                            <optgroup label="{{ $groupName }}">
                                @foreach ($options as $option)
                                    <option value="{{ $option['id'] }}" data-versione-id="{{ $option['id'] }}">
                                        {{ $option['label'] }}@if ($option['qty'] > 0)
                                            ({{ $option['qty'] }})
                                        @endif
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                {{-- Desktop: radio button per versioni + dropdown solo per gruppi nested --}}
                <div class="d-none d-md-flex justify-content-center gap-1 flex-wrap mb-1">
                    <input type="radio" class="btn-check versione-radio" name="versione_sel_{{ $carta->id_carta }}"
                        id="versione_{{ $carta->id_carta }}_base" value="" autocomplete="off" checked>
                    <label class="btn btn-sm btn-outline-secondary py-1 px-2 version-control-btn"
                        for="versione_{{ $carta->id_carta }}_base">Base</label>
                    @foreach ($regularVersions as $v)
                        <input type="radio" class="btn-check versione-radio"
                            name="versione_sel_{{ $carta->id_carta }}"
                            id="versione_{{ $carta->id_carta }}_{{ $v['id'] }}" value="{{ $v['id'] }}"
                            autocomplete="off">
                        <label class="btn btn-sm btn-outline-secondary py-1 px-2 version-control-btn"
                            for="versione_{{ $carta->id_carta }}_{{ $v['id'] }}">
                            {{ $v['label'] }}@if ($v['qty'] > 0)
                                ({{ $v['qty'] }})
                            @endif
                        </label>
                    @endforeach
                    @foreach ($nestedVersionGroups as $groupName => $options)
                        <div class="dropdown versione-dropdown">
                            <button type="button"
                                class="btn btn-sm btn-outline-secondary dropdown-toggle versione-dropdown-btn"
                                data-bs-toggle="dropdown" aria-expanded="false" data-versione-id=""
                                data-group-name="{{ $groupName }}">
                                {{ $groupName }}
                            </button>
                            <ul class="dropdown-menu versione-dropdown-menu">
                                @foreach ($options as $option)
                                    <li>
                                        <a class="dropdown-item versione-dropdown-item" href="#"
                                            data-versione-id="{{ $option['id'] }}">
                                            {{ $option['label'] }}@if ($option['qty'] > 0)
                                                ({{ $option['qty'] }})
                                            @endif
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            @endif
            @if ($carta->artista)
                <a class="card-text mb-1 text-center card-artista-link"
                    href="{{ route('artisti.show', ['id' => $carta->artista->id_artista]) }}">
                    {{ $carta->artista->nominativo ?? '' }}
                </a>
            @endif

            {{-- Counter --}}
            @auth
                <div class="btn-container d-flex align-items-center gap-1 mt-auto">
                    <div class="col-3 px-0">
                        <x-button color="transparent" size="md" class="shadow-sm w-100" data-btn-type="decrement">
                            <x-icon name="dash-lg" color="black" />
                        </x-button>
                    </div>
                    <input type="text" name="numero_in_collezione" data-id-carta="{{ $carta->id_carta }}"
                        data-rarita-id="{{ $firstRaritaId }}" data-versione-id=""
                        class="rounded border border-secondary text-center col-6" value="{{ $initialQty }}">
                    <div class="col-3 px-0">
                        <x-button color="transparent" size="md" class="shadow-sm w-100" data-btn-type="increment">
                            <x-icon name="plus-lg" color="black" />
                        </x-button>
                    </div>
                </div>
            @endauth
        </div>
    </div>
</div>

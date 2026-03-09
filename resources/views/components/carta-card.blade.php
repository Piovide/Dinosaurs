@props(['carta'])
@php
    $raritas  = $carta->raritas;
    $firstRarita = $raritas->first();
    $versioni = $carta->versioni ?? collect();
    $hasVersioni = $versioni->isNotEmpty();
    $tipologie = $carta->tipologie ?? collect();

    // Load user quantities indexed by "raritaId__versioneId"
    $combos = [];
    if (auth()->check()) {
        Auth::user()->collezione_utente()
            ->where('car_id_carta', $carta->id_carta)
            ->get(['rar_id_collezione_rarita', 'ver_id_versione', 'quantita'])
            ->each(function ($r) use (&$combos) {
                $k = ($r->rar_id_collezione_rarita ?? '') . '__' . ($r->ver_id_versione ?? '');
                $combos[$k] = (int)$r->quantita;
            });
    }
    $combosJson = json_encode($combos);

    // Initial state: first rarity (or null) + no version
    $firstRaritaId = $firstRarita?->id_collezione_rarita ?? '';
    $initialKey    = $firstRaritaId . '__';
    $initialQty    = $combos[$initialKey] ?? 0;
@endphp
<div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3 mb-md-4">
    <div class="card h-100 shadow-sm p-3 rounded"
         data-carta-id="{{ $carta->id_carta }}"
         data-combos="{{ $combosJson }}">
        <div class="img-container position-relative">
            <img src="{{ $carta->immagine_asset }}" class="card-img-top carta-image"
                 alt="{{ $carta->titolo }}"
                 data-carta-id="{{ $carta->id_carta }}"
                 style="cursor: pointer;">
            <div class="card-info position-absolute bottom-0 start-0 p-2 bg-dark bg-opacity-50 text-white rounded-end rounded-bottom-0">
                <small class="d-flex align-items-center gap-1 flex-wrap">
                    {{ $carta->prefisso ? $carta->prefisso : '' }}{{ $carta->numero }}{{ $carta->suffisso ? $carta->suffisso : '' }} / {{ $carta->collezione->numero_carte }}
                    @if($firstRarita)
                        <x-icona-badge :record="$firstRarita" size="14px" />
                    @endif
                </small>
            </div>
        </div>
        <div class="card-body d-flex flex-column">
            <h5 class="card-title text-center">{{ $carta->titolo }}</h5>

            {{-- Tipologia + Rarità badges side by side --}}
            @if($tipologie->isNotEmpty() || $firstRarita)
                <div class="d-flex justify-content-center align-items-center gap-1 flex-wrap mb-1">
                    @foreach($tipologie as $t)
                        <span class="badge bg-info text-dark d-inline-flex align-items-center gap-1">
                            <x-icona-badge :record="$t" size="12px" />
                            {{ $t->nome }}
                        </span>
                    @endforeach
                    @if($firstRarita)
                        <span class="badge bg-warning text-dark d-inline-flex align-items-center gap-1">
                            <x-icona-badge :record="$firstRarita" size="12px" />
                            {{ $firstRarita->nome }}
                        </span>
                    @endif
                </div>
            @endif

            {{-- Versione radio selectors --}}
            @if($hasVersioni)
                <div class="d-flex justify-content-center gap-1 flex-wrap mb-1 versione-selector" data-carta-id="{{ $carta->id_carta }}">
                    <input type="radio" class="btn-check versione-radio"
                           name="versione_sel_{{ $carta->id_carta }}"
                           id="versione_{{ $carta->id_carta }}_base"
                           value="" autocomplete="off" checked>
                    <label class="btn btn-sm btn-outline-secondary py-1 px-2"
                           style="font-size:.7rem;"
                           for="versione_{{ $carta->id_carta }}_base">Base</label>
                    @foreach($versioni as $v)
                        <input type="radio" class="btn-check versione-radio"
                               name="versione_sel_{{ $carta->id_carta }}"
                               id="versione_{{ $carta->id_carta }}_{{ $v->id_versione }}"
                               value="{{ $v->id_versione }}" autocomplete="off">
                        <label class="btn btn-sm btn-outline-secondary py-1 px-2"
                               style="font-size:.7rem;"
                               for="versione_{{ $carta->id_carta }}_{{ $v->id_versione }}">{{ $v->nome }}</label>
                    @endforeach
                </div>
            @endif

            <p class="card-text mb-1 text-center">{{ $carta->artista->nominativo ?? '' }}</p>

            {{-- Counter --}}
            @auth
                <div class="btn-container d-flex align-items-center gap-1 mt-auto">
                    <div class="col-3 px-0">
                        <x-button color="transparent" size="md" class="shadow-sm w-100" data-btn-type="decrement">
                            <x-icon name="dash-lg" color="black" />
                        </x-button>
                    </div>
                    <input type="text" name="numero_in_collezione"
                    data-id-carta="{{ $carta->id_carta }}"
                    data-rarita-id="{{ $firstRaritaId }}"
                    data-versione-id=""
                    class="rounded border border-secondary text-center col-6"
                    value="{{ $initialQty }}">
                    <div class="col-3 px-0">
                        <x-button color="transparent" size="md" class="shadow-sm w-100" data-btn-type="increment">
                            <x-icon name="plus-lg" color="black"/>
                        </x-button>
                    </div>
                </div>
            @endauth
        </div>
    </div>
</div>

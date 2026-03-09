<x-admin-layout title="Carte della Collezione">
    <div class="d-flex align-items-center gap-2 mb-4 flex-wrap">
        <a href="{{ route('admin.collezioni.index') }}" class="btn btn-sm btn-outline-secondary">&larr; Collezioni</a>
        <h2 class="mb-0">{{ $collezione->nome }}</h2>
        @if($collezione->data_uscita)
            <span class="badge bg-secondary">{{ $collezione->data_uscita }}</span>
        @endif
        <div class="ms-auto d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.collezioni.carta.create', $collezione->id_collezione) }}"
               class="btn btn-success">+ Aggiungi Carta</a>
            <a href="{{ route('admin.collezioni.edit', $collezione->id_collezione) }}"
               class="btn btn-outline-primary">Modifica Collezione</a>
        </div>
    </div>

    @if($collezione->descrizione)
        <p class="text-muted mb-4">{{ $collezione->descrizione }}</p>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>N°</th>
                        <th>Immagine</th>
                        <th>Titolo</th>
                        <th>Artista</th>
                        <th>Rarità</th>
                        <th>Tipo</th>
                        <th class="text-end">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($carte as $carta)
                        <tr>
                            <td class="text-muted small">{{ $carta->numero ?? '—' }}</td>
                            <td>
                                @if($carta->immagine_url)
                                    <img src="{{ $carta->immagine_asset }}"
                                         alt="{{ $carta->titolo }}"
                                         style="width:50px;height:50px;object-fit:cover;border-radius:4px;">
                                @else
                                    <div style="width:50px;height:50px;background:#eee;border-radius:4px;display:flex;align-items:center;justify-content:center;color:#aaa;font-size:20px;">?</div>
                                @endif
                            </td>
                            <td class="fw-semibold">{{ $carta->titolo }}</td>
                            <td>{{ $carta->artista?->nominativo }}</td>
                            <td>
                                @forelse($carta->raritas as $r)
                                    <span class="badge bg-warning text-dark d-inline-flex align-items-center gap-1">
                                        <x-icona-badge :record="$r" size="14px" />
                                        {{ $r->nome }}
                                    </span>
                                @empty
                                    <span class="text-muted">—</span>
                                @endforelse
                            </td>
                            <td>
                                @forelse($carta->tipologie as $t)
                                    <span class="d-inline-flex align-items-center gap-1">
                                        <x-icona-badge :record="$t" size="14px" />
                                        {{ $t->nome }}
                                    </span>@if(!$loop->last), @endif
                                @empty
                                    <span class="text-muted">—</span>
                                @endforelse
                            </td>
                            <td class="text-end">
                                <a href="{{ route('carte.show', $carta->id_carta) }}"
                                   class="btn btn-sm btn-outline-secondary" target="_blank">Vedi</a>
                                <a href="{{ route('admin.carte.edit', $carta->id_carta) }}"
                                   class="btn btn-sm btn-outline-primary">Modifica</a>
                                <form method="POST"
                                      action="{{ route('admin.carte.destroy', $carta->id_carta) }}"
                                      class="d-inline"
                                      onsubmit="return confirm('Eliminare la carta {{ addslashes($carta->titolo) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Elimina</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">Nessuna carta in questa collezione.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $carte->links() }}
    </div>

    {{-- ── Gestione Rarità e Tipologie della Collezione ─────────────────────── --}}

    {{-- Datalist defaults (shared) --}}
    <datalist id="bi-rarita-defaults">
        <option value="circle">Comune</option>
        <option value="circle-half">Non Comune</option>
        <option value="diamond">Rara</option>
        <option value="diamond-fill">Ultra Rara</option>
        <option value="star">Epica</option>
        <option value="star-fill">Leggendaria</option>
        <option value="trophy-fill">Mitica</option>
        <option value="gem">Segreta</option>
    </datalist>
    <datalist id="bi-tipologia-defaults">
        <option value="lightning-fill">Fulmine / Elettrico</option>
        <option value="fire">Fuoco</option>
        <option value="droplet-fill">Acqua</option>
        <option value="tree-fill">Natura</option>
        <option value="shield-fill">Difesa</option>
        <option value="person-fill">Creatura</option>
        <option value="gear-fill">Meccanico</option>
        <option value="eye-fill">Visione</option>
        <option value="heart-fill">Vita</option>
        <option value="wind">Vento / Aria</option>
        <option value="snow">Ghiaccio</option>
        <option value="moon-fill">Oscurità</option>
        <option value="sun-fill">Luce</option>
    </datalist>

    <div class="row g-4 mt-2">
        {{-- Rarità --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning bg-opacity-25"><strong>Rarità della Collezione</strong></div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0 align-middle">
                        <thead class="table-light"><tr><th style="width:40px;">Icona</th><th>Nome</th><th style="width:60px;"></th></tr></thead>
                        <tbody>
                            @forelse($rarita as $r)
                                <tr>
                                    <td>
                                        @if($r->has_icona)
                                            <x-icona-badge :record="$r" size="24px" />
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td>{{ $r->nome }}</td>
                                    <td class="text-end">
                                        <form method="POST"
                                              action="{{ route('admin.collezioni.rarita.destroy', [$collezione->id_collezione, $r->id_collezione_rarita]) }}"
                                              class="d-inline"
                                              onsubmit="return confirm('Eliminare la rarità {{ addslashes($r->nome) }}?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger py-0">✕</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-muted small text-center py-2">Nessuna rarità definita.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-transparent">
                    <form method="POST"
                          action="{{ route('admin.collezioni.rarita.store', $collezione->id_collezione) }}"
                          enctype="multipart/form-data">
                        @csrf
                        <div class="row g-2 align-items-end">
                            <div class="col-sm-4">
                                <label class="form-label small mb-1">Nome <span class="text-danger">*</span></label>
                                <input type="text" name="nome" class="form-control form-control-sm" placeholder="es. Comune" required>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label small mb-1">Tipo icona</label>
                                <select name="tipo_icona" class="form-select form-select-sm"
                                        onchange="toggleIconoForm(this,'rar-add')">
                                    <option value="">— Nessuna —</option>
                                    <option value="bootstrap">Bootstrap Icon</option>
                                    <option value="file">File immagine</option>
                                </select>
                            </div>
                            <div class="col-sm-4 d-none" id="rar-add-bi">
                                <label class="form-label small mb-1">Nome icona BI</label>
                                <input type="text" name="icona_bootstrap"
                                       class="form-control form-control-sm"
                                       list="bi-rarita-defaults"
                                       placeholder="es. diamond-fill">
                            </div>
                            <div class="col-sm-4 d-none" id="rar-add-file">
                                <label class="form-label small mb-1">File (max 1&nbsp;MB)</label>
                                <input type="file" name="icona" accept="image/*" class="form-control form-control-sm">
                            </div>
                            <div class="col-sm-auto">
                                <button type="submit" class="btn btn-sm btn-warning">+ Aggiungi</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Tipologie --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info bg-opacity-25"><strong>Tipologie della Collezione</strong></div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0 align-middle">
                        <thead class="table-light"><tr><th style="width:40px;">Icona</th><th>Nome</th><th style="width:60px;"></th></tr></thead>
                        <tbody>
                            @forelse($tipologie as $t)
                                <tr>
                                    <td>
                                        @if($t->has_icona)
                                            <x-icona-badge :record="$t" size="24px" />
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td>{{ $t->nome }}</td>
                                    <td class="text-end">
                                        <form method="POST"
                                              action="{{ route('admin.collezioni.tipologie.destroy', [$collezione->id_collezione, $t->id_collezione_tipologia]) }}"
                                              class="d-inline"
                                              onsubmit="return confirm('Eliminare la tipologia {{ addslashes($t->nome) }}?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger py-0">✕</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-muted small text-center py-2">Nessuna tipologia definita.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-transparent">
                    <form method="POST"
                          action="{{ route('admin.collezioni.tipologie.store', $collezione->id_collezione) }}"
                          enctype="multipart/form-data">
                        @csrf
                        <div class="row g-2 align-items-end">
                            <div class="col-sm-4">
                                <label class="form-label small mb-1">Nome <span class="text-danger">*</span></label>
                                <input type="text" name="nome" class="form-control form-control-sm" placeholder="es. Creatura" required>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label small mb-1">Tipo icona</label>
                                <select name="tipo_icona" class="form-select form-select-sm"
                                        onchange="toggleIconoForm(this,'tip-add')">
                                    <option value="">— Nessuna —</option>
                                    <option value="bootstrap">Bootstrap Icon</option>
                                    <option value="file">File immagine</option>
                                </select>
                            </div>
                            <div class="col-sm-4 d-none" id="tip-add-bi">
                                <label class="form-label small mb-1">Nome icona BI</label>
                                <input type="text" name="icona_bootstrap"
                                       class="form-control form-control-sm"
                                       list="bi-tipologia-defaults"
                                       placeholder="es. lightning-fill">
                            </div>
                            <div class="col-sm-4 d-none" id="tip-add-file">
                                <label class="form-label small mb-1">File (max 1&nbsp;MB)</label>
                                <input type="file" name="icona" accept="image/*" class="form-control form-control-sm">
                            </div>
                            <div class="col-sm-auto">
                                <button type="submit" class="btn btn-sm btn-info">+ Aggiungi</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Versioni Alternative --}}
    <div class="row g-4 mt-1">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-secondary bg-opacity-15"><strong>Versioni Alternative della Collezione</strong></div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0 align-middle">
                        <thead class="table-light"><tr><th>Nome</th><th style="width:60px;"></th></tr></thead>
                        <tbody>
                            @forelse($versioni as $v)
                                <tr>
                                    <td>{{ $v->nome }}</td>
                                    <td class="text-end">
                                        <form method="POST"
                                              action="{{ route('admin.collezioni.versioni.destroy', [$collezione->id_collezione, $v->id_versione]) }}"
                                              class="d-inline"
                                              onsubmit="return confirm('Eliminare la versione {{ addslashes($v->nome) }}?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger py-0">✕</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-muted small text-center py-2">Nessuna versione alternativa definita.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-transparent">
                    <form method="POST" action="{{ route('admin.collezioni.versioni.store', $collezione->id_collezione) }}">
                        @csrf
                        <div class="row g-2 align-items-end">
                            <div class="col">
                                <label class="form-label small mb-1">Nome <span class="text-danger">*</span></label>
                                <input type="text" name="nome" class="form-control form-control-sm"
                                       placeholder="es. Olografica, 1ª Edizione, Promo" required>
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-sm btn-secondary">+ Aggiungi</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    function toggleIconoForm(sel, prefix) {
        const biEl   = document.getElementById(prefix + '-bi');
        const fileEl = document.getElementById(prefix + '-file');
        biEl.classList.add('d-none');
        fileEl.classList.add('d-none');
        if (sel.value === 'bootstrap') biEl.classList.remove('d-none');
        if (sel.value === 'file')      fileEl.classList.remove('d-none');
    }
    </script>
</x-admin-layout>

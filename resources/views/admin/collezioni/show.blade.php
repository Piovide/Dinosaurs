<x-admin-layout title="Carte della Collezione">
    <div class="d-flex align-items-center gap-2 mb-4 flex-wrap">
        <a href="{{ route('admin.collezioni.index') }}" class="btn btn-sm btn-outline-secondary">&larr; Collezioni</a>
        <h2 class="mb-0">{{ $collezione->nome }}</h2>
        @if ($collezione->data_uscita)
            <span class="badge bg-secondary">{{ $collezione->data_uscita }}</span>
        @endif
        <div class="ms-auto d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.collezioni.carta.create', $collezione->id_collezione) }}" class="btn btn-success">+
                Aggiungi Carta</a>
            <a href="{{ route('admin.collezioni.edit', $collezione->id_collezione) }}"
                class="btn btn-outline-primary">Modifica Collezione</a>
        </div>
    </div>

    @if ($collezione->descrizione)
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
                                @if ($carta->immagine_url)
                                    <img src="{{ $carta->immagine_asset }}" alt="{{ $carta->titolo }}"
                                        style="width:50px;height:50px;object-fit:cover;border-radius:4px;">
                                @else
                                    <div
                                        style="width:50px;height:50px;background:#eee;border-radius:4px;display:flex;align-items:center;justify-content:center;color:#aaa;font-size:20px;">
                                        ?</div>
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
                                    </span>
                                    @if (!$loop->last)
                                        ,
                                    @endif
                                @empty
                                    <span class="text-muted">—</span>
                                @endforelse
                            </td>
                            <td class="text-end">
                                <a href="{{ route('carte.show', $carta->id_carta) }}"
                                    class="btn btn-sm btn-outline-secondary" target="_blank">Vedi</a>
                                <a href="{{ route('admin.carte.edit', ['id' => $carta->id_carta, 'page' => $carte->currentPage()]) }}"
                                    class="btn btn-sm btn-outline-primary">Modifica</a>
                                <form method="POST" action="{{ route('admin.carte.destroy', $carta->id_carta) }}"
                                    class="d-inline"
                                    onsubmit="return confirm('Eliminare la carta {{ addslashes($carta->titolo) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Elimina</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Nessuna carta in questa collezione.
                            </td>
                        </tr>
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
                        <thead class="table-light">
                            <tr>
                                <th style="width:40px;">Icona</th>
                                <th>Nome</th>
                                <th style="width:60px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rarita as $r)
                                <tr>
                                    <td>
                                        @if ($r->has_icona)
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
                                <tr>
                                    <td colspan="3" class="text-muted small text-center py-2">Nessuna rarità
                                        definita.</td>
                                </tr>
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
                                <input type="text" name="nome" class="form-control form-control-sm"
                                    placeholder="es. Comune" required>
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
                                <input type="text" name="icona_bootstrap" class="form-control form-control-sm"
                                    list="bi-rarita-defaults" placeholder="es. diamond-fill">
                            </div>
                            <div class="col-sm-4 d-none" id="rar-add-file">
                                <label class="form-label small mb-1">File (max 1&nbsp;MB)</label>
                                <input type="file" name="icona" accept="image/*"
                                    class="form-control form-control-sm">
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
                        <thead class="table-light">
                            <tr>
                                <th style="width:40px;">Icona</th>
                                <th>Nome</th>
                                <th style="width:60px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tipologie as $t)
                                <tr>
                                    <td>
                                        @if ($t->has_icona)
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
                                <tr>
                                    <td colspan="3" class="text-muted small text-center py-2">Nessuna tipologia
                                        definita.</td>
                                </tr>
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
                                <input type="text" name="nome" class="form-control form-control-sm"
                                    placeholder="es. Creatura" required>
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
                                <input type="text" name="icona_bootstrap" class="form-control form-control-sm"
                                    list="bi-tipologia-defaults" placeholder="es. lightning-fill">
                            </div>
                            <div class="col-sm-4 d-none" id="tip-add-file">
                                <label class="form-label small mb-1">File (max 1&nbsp;MB)</label>
                                <input type="file" name="icona" accept="image/*"
                                    class="form-control form-control-sm">
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
                <div class="card-header bg-secondary bg-opacity-15"><strong>Versioni Alternative della
                        Collezione</strong></div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nome</th>
                                <th style="width:60px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($versioni as $v)
                                <tr>
                                    <td>
                                        @php preg_match('/^__\[([^\]]+)\]-(.+)$/u', $v->nome, $vm); @endphp
                                        @if ($vm)
                                            <span class="text-muted small">{{ trim($vm[1]) }} /</span>
                                            {{ trim($vm[2]) }}
                                        @else
                                            {{ $v->nome }}
                                        @endif
                                    </td>
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
                                <tr>
                                    <td colspan="2" class="text-muted small text-center py-2">Nessuna versione
                                        alternativa definita.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-transparent">
                    <form method="POST"
                        action="{{ route('admin.collezioni.versioni.store', $collezione->id_collezione) }}"
                        onsubmit="return assemblaVersNome(this)">
                        @csrf
                        <input type="hidden" name="nome" id="vers_nome_hidden">

                        {{-- Mode toggle --}}
                        <div class="mb-2">
                            <div class="btn-group btn-group-sm" role="group">
                                <input type="radio" class="btn-check" name="vers_tipo" id="vers_tipo_semplice"
                                    value="semplice" checked autocomplete="off" onchange="toggleVersMode()">
                                <label class="btn btn-outline-secondary" for="vers_tipo_semplice">Semplice</label>
                                <input type="radio" class="btn-check" name="vers_tipo" id="vers_tipo_annidata"
                                    value="annidata" autocomplete="off" onchange="toggleVersMode()">
                                <label class="btn btn-outline-secondary" for="vers_tipo_annidata">Annidata
                                    (gruppo)</label>
                            </div>
                        </div>

                        {{-- Semplice --}}
                        <div id="vers-fields-semplice" class="row g-2 align-items-center">
                            <div class="col">
                                <input type="text" id="vers_nome_semplice" class="form-control form-control-sm"
                                    placeholder="es. Olografica, 1ª Edizione, Promo">
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-sm btn-secondary">+ Aggiungi</button>
                            </div>
                        </div>

                        {{-- Annidata --}}
                        <div id="vers-fields-annidata" style="display:none;">
                            <div class="row g-2 align-items-center">
                                <div class="col">
                                    <input type="text" id="vers_gruppo" class="form-control form-control-sm"
                                        placeholder="Nome gruppo (es. Foil)" oninput="aggiornaVersPreview()">
                                </div>
                                <div class="col">
                                    <input type="text" id="vers_opzione" class="form-control form-control-sm"
                                        placeholder="Opzione (es. Standard)" oninput="aggiornaVersPreview()">
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-sm btn-secondary">+ Aggiungi</button>
                                </div>
                            </div>
                            <div class="mt-1 text-muted small">
                                Salvato come: <code id="vers-preview">__[Gruppo]-Opzione</code>
                            </div>
                        </div>
                    </form>
                    <script>
                        function toggleVersMode() {
                            const isAnnidata = document.getElementById('vers_tipo_annidata').checked;
                            document.getElementById('vers-fields-semplice').style.display = isAnnidata ? 'none' : '';
                            document.getElementById('vers-fields-annidata').style.display = isAnnidata ? '' : 'none';
                        }

                        function aggiornaVersPreview() {
                            const g = document.getElementById('vers_gruppo').value.trim() || 'Gruppo';
                            const o = document.getElementById('vers_opzione').value.trim() || 'Opzione';
                            document.getElementById('vers-preview').textContent = `__[${g}]-${o}`;
                        }

                        function assemblaVersNome() {
                            const isAnnidata = document.getElementById('vers_tipo_annidata').checked;
                            let nome;
                            if (isAnnidata) {
                                const g = document.getElementById('vers_gruppo').value.trim();
                                const o = document.getElementById('vers_opzione').value.trim();
                                if (!g || !o) {
                                    alert('Inserisci nome gruppo e opzione.');
                                    return false;
                                }
                                nome = `__[${g}]-${o}`;
                            } else {
                                nome = document.getElementById('vers_nome_semplice').value.trim();
                                if (!nome) {
                                    alert('Il nome è obbligatorio.');
                                    return false;
                                }
                            }
                            document.getElementById('vers_nome_hidden').value = nome;
                            return true;
                        }
                    </script>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary bg-opacity-15"><strong>Regole Versioni per Rarita</strong></div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Rarita</th>
                                <th>Versioni automatiche</th>
                                <th style="width:60px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rarita as $r)
                                @php($regoleRarita = $regoleVersioni->get($r->id_collezione_rarita, collect()))
                                <tr>
                                    <td class="fw-semibold">{{ $r->nome }}</td>
                                    <td>
                                        @if ($regoleRarita->isEmpty())
                                            <span class="text-muted">Nessuna</span>
                                        @else
                                            {{ $regoleRarita->pluck('versione_nome')->join(', ') }}
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <form method="POST"
                                            action="{{ route('admin.collezioni.regole-versioni.destroy', [$collezione->id_collezione, $r->id_collezione_rarita]) }}"
                                            class="d-inline"
                                            onsubmit="return confirm('Eliminare le regole automatiche per la rarita {{ addslashes($r->nome) }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger py-0"
                                                title="Rimuovi regola">✕</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-muted small text-center py-2">Definisci prima
                                        almeno una
                                        rarita.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-transparent">
                    <form method="POST"
                        action="{{ route('admin.collezioni.regole-versioni.store', $collezione->id_collezione) }}">
                        @csrf
                        <div class="row g-2 align-items-end">
                            <div class="col-sm-4">
                                <label class="form-label small mb-1">Rarita <span class="text-danger">*</span></label>
                                <select name="rarita_id" class="form-select form-select-sm" required>
                                    <option value="">Seleziona</option>
                                    @foreach ($rarita as $r)
                                        <option value="{{ $r->id_collezione_rarita }}">{{ $r->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label small mb-1">Versioni automatiche</label>
                                <select name="versione_ids[]" class="form-select form-select-sm" multiple
                                    style="min-height: 92px;">
                                    @foreach ($versioni as $v)
                                        <option value="{{ $v->id_versione }}">{{ $v->nome }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text">Se non selezioni versioni, la regola viene svuotata.</div>
                            </div>
                            <div class="col-sm-2">
                                <button type="submit" class="btn btn-sm btn-primary w-100">Salva</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-primary bg-opacity-10">
                        <strong>Import massivo carte da JSON</strong>
                    </div>
                    <div class="card-body">
                        <form method="POST"
                            action="{{ route('admin.collezioni.import.json', $collezione->id_collezione) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <label class="form-label fw-semibold">File JSON</label>
                            <input type="file" name="json_file" accept=".json,application/json,text/plain"
                                class="form-control form-control-sm" required>
                            <div class="form-text mt-2">
                                Struttura richiesta: root con chiave <code>cards</code> e lista oggetti.
                            </div>
                            <pre class="bg-light border rounded p-2 mt-2 small mb-3" style="white-space:pre-wrap;">{
  "cards": [
    {
      "numero": 1,
      "titolo": "Nome carta",
      "descrizione": "Testo opzionale",
      "artista": "Nome artista",
      "prefisso": "M",
      "suffisso": "a",
      "rarita": "Rara",
      "tipologie": ["Creatura", "Acqua"],
      "versioni": ["Promo"]
    }
  ]
}</pre>
                            <button type="submit" class="btn btn-sm btn-primary">Importa JSON</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-success bg-opacity-10">
                        <strong>Assegnazione massiva immagini</strong>
                    </div>
                    <div class="card-body">
                        <form method="POST"
                            action="{{ route('admin.collezioni.import.immagini', $collezione->id_collezione) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <label class="form-label fw-semibold">Immagini carte</label>
                            <input type="file" name="immagini[]" accept="image/*"
                                class="form-control form-control-sm" multiple required>
                            <div class="form-text mt-2 mb-3">
                                Ogni file deve chiamarsi con il numero della carta, ad esempio: <code>1.png</code>,
                                <code>25.jpg</code>, <code>100.webp</code>.
                            </div>
                            <button type="submit" class="btn btn-sm btn-success">Importa immagini</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        function toggleIconoForm(sel, prefix) {
            const biEl = document.getElementById(prefix + '-bi');
            const fileEl = document.getElementById(prefix + '-file');
            biEl.classList.add('d-none');
            fileEl.classList.add('d-none');
            if (sel.value === 'bootstrap') biEl.classList.remove('d-none');
            if (sel.value === 'file') fileEl.classList.remove('d-none');
        }
    </script>
</x-admin-layout>

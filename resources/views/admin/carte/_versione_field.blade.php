{{--
  Partial: admin/carte/_versione_field.blade.php
  Props: $versioni (collection of VersioneCollezione), $selectedVersioneIds (array), $collezioneId (int)
--}}
@php
    $selectedVersioneIds = $selectedVersioneIds ?? [];

    // Separate regular vs nested versions
    $regularVers = [];
    $nestedGroups = [];
    foreach ($versioni as $v) {
        $nome = trim((string) $v->nome);
        if (preg_match('/^__\[([^\]]+)\]-(.+)$/u', $nome, $m)) {
            $gName = trim($m[1]);
            $nestedGroups[$gName][] = ['versione' => $v, 'label' => trim($m[2])];
        } else {
            $regularVers[] = $v;
        }
    }
@endphp

<div class="col-md-6" id="versione-field-wrapper">
    <label class="form-label fw-semibold">Versioni alternative</label>

    <div id="versione-checkboxes" class="d-flex flex-column gap-1 mb-2">

        {{-- Regular versions --}}
        @foreach ($regularVers as $v)
            <div class="form-check d-flex align-items-center gap-2">
                <input class="form-check-input" type="checkbox" name="versione_ids[]" id="versione_cb_{{ $v->id_versione }}"
                    value="{{ $v->id_versione }}"
                    {{ in_array($v->id_versione, (array) $selectedVersioneIds) ? 'checked' : '' }}>
                <label class="form-check-label" for="versione_cb_{{ $v->id_versione }}">{{ $v->nome }}</label>
            </div>
        @endforeach

        {{-- Nested groups --}}
        @foreach ($nestedGroups as $gName => $opts)
            @php $gSlug = 'grp_' . preg_replace('/[^a-z0-9]+/', '_', strtolower($gName)); @endphp
            <div class="versioni-group border rounded p-2 mt-1" data-group="{{ $gSlug }}">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="small fw-semibold">{{ $gName }}</span>
                    <div class="form-check form-switch mb-0 ms-auto"
                        title="Se attivo, selezionare una voce seleziona automaticamente tutto il gruppo">
                        <input class="form-check-input versioni-group-toggle" type="checkbox" role="switch"
                            id="toggle_{{ $gSlug }}" data-group="{{ $gSlug }}" checked>
                        <label class="form-check-label text-muted small" for="toggle_{{ $gSlug }}">Seleziona
                            tutto</label>
                    </div>
                </div>
                <div class="d-flex flex-column gap-1 ms-1">
                    @foreach ($opts as $opt)
                        <div class="form-check d-flex align-items-center gap-2">
                            <input class="form-check-input versioni-group-cb" type="checkbox" name="versione_ids[]"
                                id="versione_cb_{{ $opt['versione']->id_versione }}"
                                value="{{ $opt['versione']->id_versione }}" data-group="{{ $gSlug }}"
                                {{ in_array($opt['versione']->id_versione, (array) $selectedVersioneIds) ? 'checked' : '' }}>
                            <label class="form-check-label" for="versione_cb_{{ $opt['versione']->id_versione }}">
                                {{ $opt['label'] }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        @if (empty($regularVers) && empty($nestedGroups))
            <p class="text-muted small mb-1">Nessuna versione alternativa definita per questa collezione.</p>
        @endif
    </div>

    @error('versione_ids')
        <div class="text-danger small mb-1">{{ $message }}</div>
    @enderror

    <button type="button" class="btn btn-sm btn-outline-info" onclick="toggleNewVersioneForm()">&#43; Nuova
        versione</button>
</div>

{{-- Inline create panel --}}
<div class="col-12" id="new-versione-form" style="display:none;">
    <div class="card border-info border-opacity-50 bg-info bg-opacity-10 p-3 mt-1">
        <h6 class="mb-2 text-info fw-semibold">&#43; Nuova versione alternativa</h6>

        {{-- Mode toggle --}}
        <div class="mb-2">
            <div class="btn-group btn-group-sm" role="group">
                <input type="radio" class="btn-check" name="nv_tipo" id="nv_tipo_semplice" value="semplice" checked
                    autocomplete="off" onchange="aggiornaNVMode()">
                <label class="btn btn-outline-secondary" for="nv_tipo_semplice">Semplice</label>
                <input type="radio" class="btn-check" name="nv_tipo" id="nv_tipo_annidata" value="annidata"
                    autocomplete="off" onchange="aggiornaNVMode()">
                <label class="btn btn-outline-secondary" for="nv_tipo_annidata">Annidata (gruppo)</label>
            </div>
        </div>

        {{-- Semplice --}}
        <div id="nv-fields-semplice">
            <input type="text" id="new_versione_nome" class="form-control form-control-sm"
                placeholder="es. Prima edizione, Olografica, Promo">
        </div>

        {{-- Annidata --}}
        <div id="nv-fields-annidata" style="display:none;">
            <div class="row g-2">
                <div class="col-md-4">
                    <label class="form-label small mb-1">Nome gruppo</label>
                    <input type="text" id="nv_gruppo" class="form-control form-control-sm" placeholder="es. Foil"
                        oninput="aggiornaNVPreview()">
                </div>
                <div class="col-md-4">
                    <label class="form-label small mb-1">Opzione</label>
                    <input type="text" id="nv_opzione" class="form-control form-control-sm"
                        placeholder="es. Standard" oninput="aggiornaNVPreview()">
                </div>
            </div>
            <div class="mt-1 text-muted small">
                Salvato come: <code id="nv-preview">__[Gruppo]-Opzione</code>
            </div>
        </div>

        <div class="mt-2 d-flex gap-2 align-items-center">
            <button type="button" class="btn btn-info btn-sm" onclick="saveNewVersione()">Crea e aggiungi</button>
            <button type="button" class="btn btn-outline-secondary btn-sm"
                onclick="toggleNewVersioneForm()">Annulla</button>
            <span id="new-versione-feedback" class="small"></span>
        </div>
    </div>
</div>

<script>
    function toggleNewVersioneForm() {
        const f = document.getElementById('new-versione-form');
        f.style.display = f.style.display === 'none' ? 'block' : 'none';
    }

    function aggiornaNVMode() {
        const isAnnidata = document.getElementById('nv_tipo_annidata').checked;
        document.getElementById('nv-fields-semplice').style.display = isAnnidata ? 'none' : '';
        document.getElementById('nv-fields-annidata').style.display = isAnnidata ? '' : 'none';
    }

    function aggiornaNVPreview() {
        const g = document.getElementById('nv_gruppo').value.trim() || 'Gruppo';
        const o = document.getElementById('nv_opzione').value.trim() || 'Opzione';
        document.getElementById('nv-preview').textContent = `__[${g}]-${o}`;
    }

    function saveNewVersione() {
        const fb = document.getElementById('new-versione-feedback');
        let nome;
        if (document.getElementById('nv_tipo_annidata').checked) {
            const g = document.getElementById('nv_gruppo').value.trim();
            const o = document.getElementById('nv_opzione').value.trim();
            if (!g || !o) {
                fb.textContent = 'Inserisci nome gruppo e opzione.';
                fb.className = 'small text-danger';
                return;
            }
            nome = `__[${g}]-${o}`;
        } else {
            nome = document.getElementById('new_versione_nome').value.trim();
            if (!nome) {
                fb.textContent = 'Il nome è obbligatorio.';
                fb.className = 'small text-danger';
                return;
            }
        }
        fb.textContent = 'Salvataggio...';
        fb.className = 'small text-muted';
        const fd = new FormData();
        fd.append('nome', nome);
        fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        fetch('{{ route('admin.collezioni.versioni.store', $collezioneId) }}', {
            method: 'POST',
            headers: {
                'Accept': 'application/json'
            },
            body: fd,
        }).then(r => r.json()).then(data => {
            if (!data.id) {
                fb.textContent = data.message ?? 'Errore.';
                fb.className = 'small text-danger';
                return;
            }
            appendNewVersioneCheckbox(data.id, data.label);
            fb.textContent = 'Versione creata e aggiunta.';
            fb.className = 'small text-success';
            document.getElementById('new_versione_nome').value = '';
            document.getElementById('nv_gruppo').value = '';
            document.getElementById('nv_opzione').value = '';
            aggiornaNVPreview();
            setTimeout(() => {
                document.getElementById('new-versione-form').style.display = 'none';
                fb.textContent = '';
            }, 1500);
        }).catch(() => {
            fb.textContent = 'Errore di rete.';
            fb.className = 'small text-danger';
        });
    }

    function appendNewVersioneCheckbox(id, nomeRaw) {
        const container = document.getElementById('versione-checkboxes');
        const placeholder = container.querySelector('p.text-muted');
        if (placeholder) placeholder.remove();

        const match = nomeRaw.match(/^__\[([^\]]+)\]-(.+)$/u);
        if (match) {
            const gName = match[1].trim();
            const label = match[2].trim();
            const gSlug = 'grp_' + gName.toLowerCase().replace(/[^a-z0-9]+/g, '_');

            let group = container.querySelector(`.versioni-group[data-group="${gSlug}"]`);
            if (!group) {
                group = document.createElement('div');
                group.className = 'versioni-group border rounded p-2 mt-1';
                group.dataset.group = gSlug;
                group.innerHTML = `
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="small fw-semibold">${gName}</span>
                    <div class="form-check form-switch mb-0 ms-auto">
                        <input class="form-check-input versioni-group-toggle" type="checkbox" role="switch"
                               id="toggle_${gSlug}" data-group="${gSlug}" checked>
                        <label class="form-check-label text-muted small" for="toggle_${gSlug}">Seleziona tutto</label>
                    </div>
                </div>
                <div class="d-flex flex-column gap-1 ms-1"></div>`;
                container.appendChild(group);
            }

            const optContainer = group.querySelector('.d-flex.flex-column');
            const wrapper = document.createElement('div');
            wrapper.className = 'form-check d-flex align-items-center gap-2';
            wrapper.innerHTML =
                `<input class="form-check-input versioni-group-cb" type="checkbox" name="versione_ids[]" id="versione_cb_${id}" value="${id}" data-group="${gSlug}" checked><label class="form-check-label" for="versione_cb_${id}">${label}</label>`;
            optContainer.appendChild(wrapper);
            wireGroupCheckbox(wrapper.querySelector('.versioni-group-cb'));
        } else {
            const wrapper = document.createElement('div');
            wrapper.className = 'form-check d-flex align-items-center gap-2';
            wrapper.innerHTML =
                `<input class="form-check-input" type="checkbox" name="versione_ids[]" id="versione_cb_${id}" value="${id}" checked><label class="form-check-label" for="versione_cb_${id}">${nomeRaw}</label>`;
            container.appendChild(wrapper);
        }
    }

    function wireGroupCheckbox(cb) {
        cb.addEventListener('change', function() {
            if (!this.checked) return;
            const group = this.dataset.group;
            const toggle = document.querySelector(`.versioni-group-toggle[data-group="${group}"]`);
            if (!toggle || !toggle.checked) return;
            document.querySelectorAll(`.versioni-group-cb[data-group="${group}"]`).forEach(other => {
                other.checked = true;
            });
        });
    }

    // Wire auto-check on existing checkboxes at load
    document.querySelectorAll('.versioni-group-cb').forEach(wireGroupCheckbox);
</script>

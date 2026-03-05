{{--
  Partial: admin/carte/_tipologia_field.blade.php
  Props: $tipologie (collection), $selectedTipologiaIds (array), $collezioneId (int)
--}}
@php
    $biSprite2 = asset('/build/assets/svg/bootstrap-icons.svg');
    $selectedTipologiaIds = $selectedTipologiaIds ?? [];
@endphp

<div class="col-md-6" id="tipologia-field-wrapper">
    <label class="form-label fw-semibold">Tipologie</label>

    <div id="tipologia-checkboxes" class="d-flex flex-column gap-1 mb-2">
        @forelse($tipologie as $t)
            <div class="form-check d-flex align-items-center gap-2">
                <input class="form-check-input" type="checkbox"
                       name="tipologia_ids[]"
                       id="tipologia_cb_{{ $t->id_collezione_tipologia }}"
                       value="{{ $t->id_collezione_tipologia }}"
                       {{ in_array($t->id_collezione_tipologia, (array) $selectedTipologiaIds) ? 'checked' : '' }}>
                <label class="form-check-label d-inline-flex align-items-center gap-1"
                       for="tipologia_cb_{{ $t->id_collezione_tipologia }}">
                    @if($t->has_icona)
                        <x-icona-badge :record="$t" size="16px" />
                    @endif
                    {{ $t->nome }}
                </label>
            </div>
        @empty
            <p class="text-muted small mb-1">Nessuna tipologia definita per questa collezione.</p>
        @endforelse
    </div>

    @error('tipologia_ids')
        <div class="text-danger small mb-1">{{ $message }}</div>
    @enderror

    <button type="button" class="btn btn-sm btn-outline-info"
            onclick="toggleNewTipologiaForm()">&#43; Nuova tipologia</button>
</div>

{{-- Inline create panel --}}
<div class="col-12" id="new-tipologia-form" style="display:none;">
    <div class="card border-info border-opacity-50 bg-info bg-opacity-10 p-3 mt-1">
        <h6 class="mb-3 text-info fw-semibold">&#43; Nuova Tipologia</h6>

        <datalist id="bi-tipologia-defaults">
            <option value="lightning-fill">Elettrico / Fulmine</option>
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

        <div class="row g-2">
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Nome <span class="text-danger">*</span></label>
                <input type="text" id="new_tipologia_nome" class="form-control form-control-sm"
                       placeholder="es. Creatura, Magia, Trappola">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Tipo icona</label>
                <select id="new_tipologia_tipo" class="form-select form-select-sm"
                        onchange="toggleTipologiaIconaType()">
                    <option value="">— Nessuna —</option>
                    <option value="bootstrap">Bootstrap Icon</option>
                    <option value="file">File immagine</option>
                </select>
            </div>
            <div class="col d-none" id="new-tipologia-bi-group">
                <label class="form-label small fw-semibold">Nome icona BI</label>
                <div class="d-flex gap-1 align-items-center">
                    <input type="text" id="new_tipologia_bi"
                           class="form-control form-control-sm"
                           list="bi-tipologia-defaults"
                           placeholder="es. lightning-fill"
                           oninput="previewSvg('new-tipologia-bi-prev',this.value,'{{ $biSprite2 }}')">
                    <span id="new-tipologia-bi-prev" style="flex-shrink:0;"></span>
                </div>
            </div>
            <div class="col d-none" id="new-tipologia-file-group">
                <label class="form-label small fw-semibold">File (max 1&nbsp;MB)</label>
                <input type="file" id="new_tipologia_file" accept="image/*"
                       class="form-control form-control-sm"
                       onchange="previewImgFile(this,'new-tipologia-file-prev')">
                <img id="new-tipologia-file-prev" src="#" alt=""
                     style="display:none;height:28px;margin-top:4px;border-radius:3px;">
            </div>
        </div>
        <div class="mt-2 d-flex gap-2 align-items-center">
            <button type="button" class="btn btn-info btn-sm" onclick="saveNewTipologia()">Crea e aggiungi</button>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleNewTipologiaForm()">Annulla</button>
            <span id="new-tipologia-feedback" class="small"></span>
        </div>
    </div>
</div>

<script>
function toggleNewTipologiaForm() {
    const f = document.getElementById('new-tipologia-form');
    f.style.display = f.style.display === 'none' ? 'block' : 'none';
}
function toggleTipologiaIconaType() {
    const tipo = document.getElementById('new_tipologia_tipo').value;
    document.getElementById('new-tipologia-bi-group').classList.toggle('d-none', tipo !== 'bootstrap');
    document.getElementById('new-tipologia-file-group').classList.toggle('d-none', tipo !== 'file');
}
function saveNewTipologia() {
    const nome = document.getElementById('new_tipologia_nome').value.trim();
    const tipo = document.getElementById('new_tipologia_tipo').value;
    const fb   = document.getElementById('new-tipologia-feedback');
    if (!nome) { fb.textContent = 'Il nome è obbligatorio.'; fb.className = 'small text-danger'; return; }
    fb.textContent = 'Salvataggio…'; fb.className = 'small text-muted';
    const fd = new FormData();
    fd.append('nome', nome);
    fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    if (tipo) fd.append('tipo_icona', tipo);
    if (tipo === 'bootstrap') { const v = document.getElementById('new_tipologia_bi').value.trim(); if(v) fd.append('icona_bootstrap', v); }
    if (tipo === 'file') { const f2 = document.getElementById('new_tipologia_file'); if(f2.files[0]) fd.append('icona', f2.files[0]); }
    fetch('{{ route('admin.collezioni.tipologie.store', $collezioneId) }}', {
        method: 'POST', headers: { 'Accept': 'application/json' }, body: fd,
    }).then(r => r.json()).then(data => {
        if (!data.id) { fb.textContent = data.message ?? 'Errore.'; fb.className = 'small text-danger'; return; }
        const container = document.getElementById('tipologia-checkboxes');
        const placeholder = container.querySelector('p.text-muted');
        if (placeholder) placeholder.remove();
        const wrapper = document.createElement('div');
        wrapper.className = 'form-check d-flex align-items-center gap-2';
        wrapper.innerHTML = `<input class="form-check-input" type="checkbox" name="tipologia_ids[]" id="tipologia_cb_${data.id}" value="${data.id}" checked><label class="form-check-label" for="tipologia_cb_${data.id}">${data.label}</label>`;
        container.appendChild(wrapper);
        fb.textContent = '✓ Tipologia creata e aggiunta.'; fb.className = 'small text-success';
        document.getElementById('new_tipologia_nome').value = '';
        document.getElementById('new_tipologia_tipo').value = '';
        toggleTipologiaIconaType();
        setTimeout(() => { document.getElementById('new-tipologia-form').style.display = 'none'; fb.textContent = ''; }, 1500);
    }).catch(() => { fb.textContent = 'Errore di rete.'; fb.className = 'small text-danger'; });
}
if (typeof previewSvg === 'undefined') {
    window.previewSvg = function(targetId, name, sprite) {
        const el = document.getElementById(targetId);
        if (!el) return;
        el.innerHTML = name ? `<svg style="width:26px;height:26px;vertical-align:middle;"><use href="${sprite}#${name}"></use></svg>` : '';
    };
    window.previewImgFile = function(input, targetId) {
        const img = document.getElementById(targetId);
        if (!img || !input.files || !input.files[0]) return;
        const reader = new FileReader();
        reader.onload = e => { img.src = e.target.result; img.style.display = 'inline'; };
        reader.readAsDataURL(input.files[0]);
    };
}
</script>

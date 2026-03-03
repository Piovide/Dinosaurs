{{--
  Partial: admin/carte/_tipologia_field.blade.php
  Props: $tipologie (collection), $selectedTipologiaId (nullable), $collezioneId (int)
--}}
@php $biSprite2 = asset('/build/assets/svg/bootstrap-icons.svg'); @endphp

<div class="col-md-6" id="tipologia-field-wrapper">
    <label class="form-label fw-semibold">Tipologia</label>
    <div class="input-group">
        <span class="input-group-text bg-transparent p-0" id="tipologia-icon-live"
              style="min-width:38px;justify-content:center;display:none;"></span>
        <select name="tip_id_tipologia" id="tip_id_tipologia"
                class="form-select @error('tip_id_tipologia') is-invalid @enderror"
                onchange="updateTipologiaPreview(this.value)">
            <option value="">— Nessuna —</option>
            @foreach($tipologie as $t)
                <option value="{{ $t->id_collezione_tipologia }}"
                        data-tipo="{{ $t->tipo_icona }}"
                        data-icona-val="{{ $t->icona }}"
                        data-icona-url="{{ $t->icona_url }}"
                    {{ old('tip_id_tipologia', $selectedTipologiaId ?? '') == $t->id_collezione_tipologia ? 'selected' : '' }}>
                    {{ $t->nome }}
                </option>
            @endforeach
        </select>
        <button type="button" class="btn btn-outline-success"
                onclick="toggleNewTipologiaForm()" title="Crea nuova tipologia">
            &#43; Nuova
        </button>
    </div>
    @error('tip_id_tipologia')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
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
            <button type="button" class="btn btn-info btn-sm" onclick="saveNewTipologia()">Crea e seleziona</button>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleNewTipologiaForm()">Annulla</button>
            <span id="new-tipologia-feedback" class="small"></span>
        </div>
    </div>
</div>

<script>
const _tipBiSprite = '{{ $biSprite2 }}';

function updateTipologiaPreview(val) {
    const live = document.getElementById('tipologia-icon-live');
    live.innerHTML = '';
    live.style.display = 'none';
    if (!val) return;
    const opt = document.querySelector(`#tip_id_tipologia option[value="${val}"]`);
    if (!opt) return;
    const tipo = opt.dataset.tipo, iconaVal = opt.dataset.iconaVal, iconaUrl = opt.dataset.iconaUrl;
    if ((tipo === 'bootstrap' && iconaVal) || iconaUrl) {
        renderIconInto(live, tipo, iconaVal, iconaUrl, _tipBiSprite, 22);
        live.style.display = 'flex';
    }
}
(function(){ const s = document.getElementById('tip_id_tipologia'); if(s && s.value) updateTipologiaPreview(s.value); })();

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
        const sel = document.getElementById('tip_id_tipologia');
        const opt = document.createElement('option');
        opt.value = data.id; opt.text = data.label;
        opt.dataset.tipo = data.tipo_icona ?? '';
        opt.dataset.iconaVal = data.icona_val ?? '';
        opt.dataset.iconaUrl = data.icona_url ?? '';
        opt.selected = true;
        sel.appendChild(opt); sel.value = data.id;
        updateTipologiaPreview(data.id);
        fb.textContent = '✓ Tipologia creata e selezionata.'; fb.className = 'small text-success';
        document.getElementById('new_tipologia_nome').value = '';
        document.getElementById('new_tipologia_tipo').value = '';
        document.getElementById('new_tipologia_bi').value = '';
        document.getElementById('new_tipologia_file').value = '';
        document.getElementById('new-tipologia-bi-prev').innerHTML = '';
        document.getElementById('new-tipologia-file-prev').style.display = 'none';
        toggleTipologiaIconaType();
        setTimeout(() => { document.getElementById('new-tipologia-form').style.display = 'none'; fb.textContent = ''; }, 1500);
    }).catch(() => { fb.textContent = 'Errore di rete.'; fb.className = 'small text-danger'; });
}
</script>

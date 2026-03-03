{{--
  Partial: admin/carte/_rarita_field.blade.php
  Props: $rarita (collection), $selectedRaritaId (nullable), $collezioneId (int)
--}}
@php $biSprite = asset('/build/assets/svg/bootstrap-icons.svg'); @endphp

<div class="col-md-6" id="rarita-field-wrapper">
    <label class="form-label fw-semibold">Rarità</label>
    <div class="input-group">
        <span class="input-group-text bg-transparent p-0" id="rarita-icon-live"
              style="min-width:38px;justify-content:center;display:none;"></span>
        <select name="rar_id_rarita" id="rar_id_rarita"
                class="form-select @error('rar_id_rarita') is-invalid @enderror"
                onchange="updateRaritaPreview(this.value)">
            <option value="">— Nessuna —</option>
            @foreach($rarita as $r)
                <option value="{{ $r->id_collezione_rarita }}"
                        data-tipo="{{ $r->tipo_icona }}"
                        data-icona-val="{{ $r->icona }}"
                        data-icona-url="{{ $r->icona_url }}"
                    {{ old('rar_id_rarita', $selectedRaritaId ?? '') == $r->id_collezione_rarita ? 'selected' : '' }}>
                    {{ $r->nome }}
                </option>
            @endforeach
        </select>
        <button type="button" class="btn btn-outline-success"
                onclick="toggleNewRaritaForm()" title="Crea nuova rarità">
            &#43; Nuova
        </button>
    </div>
    @error('rar_id_rarita')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
</div>

{{-- Inline create panel --}}
<div class="col-12" id="new-rarita-form" style="display:none;">
    <div class="card border-success border-opacity-50 bg-success bg-opacity-10 p-3 mt-1">
        <h6 class="mb-3 text-success fw-semibold">&#43; Nuova Rarità</h6>

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

        <div class="row g-2">
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Nome <span class="text-danger">*</span></label>
                <input type="text" id="new_rarita_nome" class="form-control form-control-sm"
                       placeholder="es. Comune, Rara, Leggendaria">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Tipo icona</label>
                <select id="new_rarita_tipo" class="form-select form-select-sm"
                        onchange="toggleRaritaIconaType()">
                    <option value="">— Nessuna —</option>
                    <option value="bootstrap">Bootstrap Icon</option>
                    <option value="file">File immagine</option>
                </select>
            </div>
            <div class="col d-none" id="new-rarita-bi-group">
                <label class="form-label small fw-semibold">Nome icona BI</label>
                <div class="d-flex gap-1 align-items-center">
                    <input type="text" id="new_rarita_bi"
                           class="form-control form-control-sm"
                           list="bi-rarita-defaults"
                           placeholder="es. diamond-fill"
                           oninput="previewSvg('new-rarita-bi-prev',this.value,'{{ $biSprite }}')">
                    <span id="new-rarita-bi-prev" style="flex-shrink:0;"></span>
                </div>
            </div>
            <div class="col d-none" id="new-rarita-file-group">
                <label class="form-label small fw-semibold">File (max 1&nbsp;MB)</label>
                <input type="file" id="new_rarita_file" accept="image/*"
                       class="form-control form-control-sm"
                       onchange="previewImgFile(this,'new-rarita-file-prev')">
                <img id="new-rarita-file-prev" src="#" alt=""
                     style="display:none;height:28px;margin-top:4px;border-radius:3px;">
            </div>
        </div>
        <div class="mt-2 d-flex gap-2 align-items-center">
            <button type="button" class="btn btn-success btn-sm" onclick="saveNewRarita()">Crea e seleziona</button>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleNewRaritaForm()">Annulla</button>
            <span id="new-rarita-feedback" class="small"></span>
        </div>
    </div>
</div>

<script>
const _rarBiSprite = '{{ $biSprite }}';

function updateRaritaPreview(val) {
    const live = document.getElementById('rarita-icon-live');
    live.innerHTML = '';
    live.style.display = 'none';
    if (!val) return;
    const opt = document.querySelector(`#rar_id_rarita option[value="${val}"]`);
    if (!opt) return;
    const tipo = opt.dataset.tipo, iconaVal = opt.dataset.iconaVal, iconaUrl = opt.dataset.iconaUrl;
    if ((tipo === 'bootstrap' && iconaVal) || iconaUrl) {
        renderIconInto(live, tipo, iconaVal, iconaUrl, _rarBiSprite, 22);
        live.style.display = 'flex';
    }
}
(function(){ const s = document.getElementById('rar_id_rarita'); if(s && s.value) updateRaritaPreview(s.value); })();

function toggleNewRaritaForm() {
    const f = document.getElementById('new-rarita-form');
    f.style.display = f.style.display === 'none' ? 'block' : 'none';
}
function toggleRaritaIconaType() {
    const tipo = document.getElementById('new_rarita_tipo').value;
    document.getElementById('new-rarita-bi-group').classList.toggle('d-none', tipo !== 'bootstrap');
    document.getElementById('new-rarita-file-group').classList.toggle('d-none', tipo !== 'file');
}
function saveNewRarita() {
    const nome = document.getElementById('new_rarita_nome').value.trim();
    const tipo = document.getElementById('new_rarita_tipo').value;
    const fb   = document.getElementById('new-rarita-feedback');
    if (!nome) { fb.textContent = 'Il nome è obbligatorio.'; fb.className = 'small text-danger'; return; }
    fb.textContent = 'Salvataggio…'; fb.className = 'small text-muted';
    const fd = new FormData();
    fd.append('nome', nome);
    fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    if (tipo) fd.append('tipo_icona', tipo);
    if (tipo === 'bootstrap') { const v = document.getElementById('new_rarita_bi').value.trim(); if(v) fd.append('icona_bootstrap', v); }
    if (tipo === 'file') { const f2 = document.getElementById('new_rarita_file'); if(f2.files[0]) fd.append('icona', f2.files[0]); }
    fetch('{{ route('admin.collezioni.rarita.store', $collezioneId) }}', {
        method: 'POST', headers: { 'Accept': 'application/json' }, body: fd,
    }).then(r => r.json()).then(data => {
        if (!data.id) { fb.textContent = data.message ?? 'Errore.'; fb.className = 'small text-danger'; return; }
        const sel = document.getElementById('rar_id_rarita');
        const opt = document.createElement('option');
        opt.value = data.id; opt.text = data.label;
        opt.dataset.tipo = data.tipo_icona ?? '';
        opt.dataset.iconaVal = data.icona_val ?? '';
        opt.dataset.iconaUrl = data.icona_url ?? '';
        opt.selected = true;
        sel.appendChild(opt); sel.value = data.id;
        updateRaritaPreview(data.id);
        fb.textContent = '✓ Rarità creata e selezionata.'; fb.className = 'small text-success';
        document.getElementById('new_rarita_nome').value = '';
        document.getElementById('new_rarita_tipo').value = '';
        document.getElementById('new_rarita_bi').value = '';
        document.getElementById('new_rarita_file').value = '';
        document.getElementById('new-rarita-bi-prev').innerHTML = '';
        document.getElementById('new-rarita-file-prev').style.display = 'none';
        toggleRaritaIconaType();
        setTimeout(() => { document.getElementById('new-rarita-form').style.display = 'none'; fb.textContent = ''; }, 1500);
    }).catch(() => { fb.textContent = 'Errore di rete.'; fb.className = 'small text-danger'; });
}
// Shared helpers (guarded to avoid redefinition)
if (typeof renderIconInto === 'undefined') {
    window.renderIconInto = function(el, tipo, val, url, sprite, size) {
        if (tipo === 'bootstrap' && val) {
            el.innerHTML = `<svg style="width:${size}px;height:${size}px;vertical-align:middle;"><use href="${sprite}#${val}"></use></svg>`;
        } else if (url) {
            el.innerHTML = `<img src="${url}" alt="" style="height:${size}px;vertical-align:middle;border-radius:3px;">`;
        } else { el.innerHTML = ''; }
    };
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

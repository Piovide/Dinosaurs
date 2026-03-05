{{--
  Partial: admin/carte/_versione_field.blade.php
  Props: $versioni (collection of VersioneCollezione), $selectedVersioneIds (array), $collezioneId (int)
--}}
@php
    $selectedVersioneIds = $selectedVersioneIds ?? [];
@endphp

<div class="col-md-6" id="versione-field-wrapper">
    <label class="form-label fw-semibold">Versioni alternative</label>

    <div id="versione-checkboxes" class="d-flex flex-column gap-1 mb-2">
        @forelse($versioni as $v)
            <div class="form-check d-flex align-items-center gap-2">
                <input class="form-check-input" type="checkbox"
                       name="versione_ids[]"
                       id="versione_cb_{{ $v->id_versione }}"
                       value="{{ $v->id_versione }}"
                       {{ in_array($v->id_versione, (array) $selectedVersioneIds) ? 'checked' : '' }}>
                <label class="form-check-label"
                       for="versione_cb_{{ $v->id_versione }}">{{ $v->nome }}</label>
            </div>
        @empty
            <p class="text-muted small mb-1">Nessuna versione alternativa definita per questa collezione.</p>
        @endforelse
    </div>

    @error('versione_ids')
        <div class="text-danger small mb-1">{{ $message }}</div>
    @enderror

    <button type="button" class="btn btn-sm btn-outline-info"
            onclick="toggleNewVersioneForm()">&#43; Nuova versione</button>
</div>

{{-- Inline create panel --}}
<div class="col-12" id="new-versione-form" style="display:none;">
    <div class="card border-info border-opacity-50 bg-info bg-opacity-10 p-3 mt-1">
        <h6 class="mb-3 text-info fw-semibold">&#43; Nuova versione alternativa</h6>
        <div class="row g-2">
            <div class="col-md-5">
                <label class="form-label small fw-semibold">Nome <span class="text-danger">*</span></label>
                <input type="text" id="new_versione_nome" class="form-control form-control-sm"
                       placeholder="es. Prima edizione, Olografica, Promo">
            </div>
        </div>
        <div class="mt-2 d-flex gap-2 align-items-center">
            <button type="button" class="btn btn-info btn-sm" onclick="saveNewVersione()">Crea e aggiungi</button>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleNewVersioneForm()">Annulla</button>
            <span id="new-versione-feedback" class="small"></span>
        </div>
    </div>
</div>

<script>
function toggleNewVersioneForm() {
    const f = document.getElementById('new-versione-form');
    f.style.display = f.style.display === 'none' ? 'block' : 'none';
}
function saveNewVersione() {
    const nome = document.getElementById('new_versione_nome').value.trim();
    const fb   = document.getElementById('new-versione-feedback');
    if (!nome) { fb.textContent = 'Il nome è obbligatorio.'; fb.className = 'small text-danger'; return; }
    fb.textContent = 'Salvataggio...'; fb.className = 'small text-muted';
    const fd = new FormData();
    fd.append('nome', nome);
    fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    fetch('{{ route("admin.collezioni.versioni.store", $collezioneId) }}', {
        method: 'POST', headers: { 'Accept': 'application/json' }, body: fd,
    }).then(r => r.json()).then(data => {
        if (!data.id) { fb.textContent = data.message ?? 'Errore.'; fb.className = 'small text-danger'; return; }
        const container = document.getElementById('versione-checkboxes');
        // Remove "no versions" placeholder if present
        const placeholder = container.querySelector('p.text-muted');
        if (placeholder) placeholder.remove();
        const wrapper = document.createElement('div');
        wrapper.className = 'form-check d-flex align-items-center gap-2';
        wrapper.innerHTML = `<input class="form-check-input" type="checkbox" name="versione_ids[]" id="versione_cb_${data.id}" value="${data.id}" checked><label class="form-check-label" for="versione_cb_${data.id}">${data.label}</label>`;
        container.appendChild(wrapper);
        fb.textContent = 'Versione creata e aggiunta.'; fb.className = 'small text-success';
        document.getElementById('new_versione_nome').value = '';
        setTimeout(() => { document.getElementById('new-versione-form').style.display = 'none'; fb.textContent = ''; }, 1500);
    }).catch(() => { fb.textContent = 'Errore di rete.'; fb.className = 'small text-danger'; });
}
</script>

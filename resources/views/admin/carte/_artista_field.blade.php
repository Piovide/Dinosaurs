{{--
  Partial: admin/carte/_artista_field.blade.php
  Props: $artisti (collection), $selectedArtistaId (nullable)
--}}
<div class="col-md-8" id="artista-field-wrapper">
    <label class="form-label fw-semibold">Artista</label>
    <div class="input-group">
        <select name="art_id_artista" id="art_id_artista"
                class="form-select @error('art_id_artista') is-invalid @enderror">
            <option value="">— Nessuno —</option>
            @foreach($artisti as $artista)
                <option value="{{ $artista->id_artista }}"
                    {{ old('art_id_artista', $selectedArtistaId ?? '') == $artista->id_artista ? 'selected' : '' }}>
                    {{ $artista->cognome }} {{ $artista->nome }}
                </option>
            @endforeach
        </select>
        <button type="button" class="btn btn-outline-success"
                onclick="toggleNewArtistaForm()" title="Crea nuovo artista">
            &#43; Nuovo
        </button>
    </div>
    @error('art_id_artista')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
</div>

{{-- Inline "crea artista" panel --}}
<div class="col-12" id="new-artista-form" style="display:none;">
    <div class="card border-success border-opacity-50 bg-success bg-opacity-10 p-3 mt-1">
        <h6 class="mb-3 text-success fw-semibold">&#43; Nuovo Artista</h6>
        <div class="row g-2">
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Nome <span class="text-danger">*</span></label>
                <input type="text" id="new_artista_nome" class="form-control form-control-sm" placeholder="Nome">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Cognome <span class="text-danger">*</span></label>
                <input type="text" id="new_artista_cognome" class="form-control form-control-sm" placeholder="Cognome">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Data di nascita</label>
                <input type="date" id="new_artista_data" class="form-control form-control-sm">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Sito web</label>
                <input type="url" id="new_artista_sito" class="form-control form-control-sm" placeholder="https://...">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Social</label>
                <input type="url" id="new_artista_social" class="form-control form-control-sm" placeholder="https://...">
            </div>
        </div>
        <div class="mt-2 d-flex gap-2 align-items-center">
            <button type="button" class="btn btn-success btn-sm" onclick="saveNewArtista()">
                Crea e seleziona
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleNewArtistaForm()">
                Annulla
            </button>
            <span id="new-artista-feedback" class="small"></span>
        </div>
    </div>
</div>

<script>
function toggleNewArtistaForm() {
    const f = document.getElementById('new-artista-form');
    f.style.display = f.style.display === 'none' ? 'block' : 'none';
}

function saveNewArtista() {
    const nome    = document.getElementById('new_artista_nome').value.trim();
    const cognome = document.getElementById('new_artista_cognome').value.trim();
    const data    = document.getElementById('new_artista_data').value;
    const sito    = document.getElementById('new_artista_sito').value.trim();
    const social  = document.getElementById('new_artista_social').value.trim();
    const fb      = document.getElementById('new-artista-feedback');

    if (!nome || !cognome) {
        fb.textContent = 'Nome e Cognome sono obbligatori.';
        fb.className = 'small text-danger';
        return;
    }

    fb.textContent = 'Salvataggio…';
    fb.className = 'small text-muted';

    fetch('{{ route('admin.artisti.store') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ nome, cognome, data_nascita: data || null, link_sito: sito || null, link_social: social || null }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.id) {
            const sel = document.getElementById('art_id_artista');
            const opt = new Option(data.label, data.id, true, true);
            sel.appendChild(opt);
            sel.value = data.id;
            fb.textContent = '✓ Artista creato e selezionato.';
            fb.className = 'small text-success';
            // Reset fields
            ['new_artista_nome','new_artista_cognome','new_artista_data','new_artista_sito','new_artista_social']
                .forEach(id => document.getElementById(id).value = '');
            setTimeout(() => { document.getElementById('new-artista-form').style.display = 'none'; fb.textContent = ''; }, 1500);
        } else {
            fb.textContent = data.message ?? 'Errore durante il salvataggio.';
            fb.className = 'small text-danger';
        }
    })
    .catch(() => { fb.textContent = 'Errore di rete.'; fb.className = 'small text-danger'; });
}
</script>

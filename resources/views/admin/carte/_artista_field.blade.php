{{--
  Partial: admin/carte/_artista_field.blade.php
  Props: $artisti (collection), $selectedArtistaId (nullable),
         $selectedArtistaSecondarioId (nullable), $selectedArtistaBackId (nullable),
         $collapseExtraArtists (bool)
--}}
@php
    $selectedArtistaSecondarioId = $selectedArtistaSecondarioId ?? null;
    $selectedArtistaBackId = $selectedArtistaBackId ?? null;
    $collapseExtraArtists = (bool) ($collapseExtraArtists ?? false);
    $showExtraArtists =
        old('art_id_artista_secondario', $selectedArtistaSecondarioId) ||
        old('art_id_artista_back', $selectedArtistaBackId) ||
        $errors->has('art_id_artista_secondario') ||
        $errors->has('art_id_artista_back');
    $extraArtistsPanelId = 'extra-artisti-panel-' . uniqid();
@endphp

<div class="col-md-8" id="artista-field-wrapper">
    <label class="form-label fw-semibold">Artista</label>
    <div class="input-group">
        <select name="art_id_artista" id="art_id_artista"
            class="form-select artist-select @error('art_id_artista') is-invalid @enderror">
            <option value="">— Nessuno —</option>
            @foreach ($artisti as $artista)
                <option value="{{ $artista->id_artista }}"
                    {{ old('art_id_artista', $selectedArtistaId ?? '') == $artista->id_artista ? 'selected' : '' }}>
                    {{ $artista->nominativo }}
                </option>
            @endforeach
        </select>
        <button type="button" class="btn btn-outline-success" onclick="toggleNewArtistaForm()"
            title="Crea nuovo artista">
            &#43; Nuovo
        </button>
        @if ($collapseExtraArtists)
            <div class="ms-2">
                <button class="btn btn-sm btn-outline-secondary" type="button"
                    onclick="toggleExtraArtisti('{{ $extraArtistsPanelId }}', this)"
                    aria-expanded="{{ $showExtraArtists ? 'true' : 'false' }}"
                    aria-controls="{{ $extraArtistsPanelId }}">
                    Artisti extra
                </button>
            </div>
        @endif
    </div>
    @error('art_id_artista')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>

<div class="col-12 {{ $collapseExtraArtists && !$showExtraArtists ? 'd-none' : '' }}" id="{{ $extraArtistsPanelId }}">
    <div class="row g-3 {{ $collapseExtraArtists ? 'mt-1' : '' }}">
        <div class="col-md-6">
            <label class="form-label fw-semibold">Artista secondario</label>
            <select name="art_id_artista_secondario" id="art_id_artista_secondario"
                class="form-select artist-select @error('art_id_artista_secondario') is-invalid @enderror">
                <option value="">— Nessuno —</option>
                @foreach ($artisti as $artista)
                    <option value="{{ $artista->id_artista }}"
                        {{ old('art_id_artista_secondario', $selectedArtistaSecondarioId ?? '') == $artista->id_artista ? 'selected' : '' }}>
                        {{ $artista->nominativo }}
                    </option>
                @endforeach
            </select>
            @error('art_id_artista_secondario')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">Artista back</label>
            <select name="art_id_artista_back" id="art_id_artista_back"
                class="form-select artist-select @error('art_id_artista_back') is-invalid @enderror">
                <option value="">— Nessuno —</option>
                @foreach ($artisti as $artista)
                    <option value="{{ $artista->id_artista }}"
                        {{ old('art_id_artista_back', $selectedArtistaBackId ?? '') == $artista->id_artista ? 'selected' : '' }}>
                        {{ $artista->nominativo }}
                    </option>
                @endforeach
            </select>
            @error('art_id_artista_back')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

{{-- Inline "crea artista" panel --}}
<div class="col-12" id="new-artista-form" style="display:none;">
    <div class="card border-success border-opacity-50 bg-success bg-opacity-10 p-3 mt-1">
        <h6 class="mb-3 text-success fw-semibold">&#43; Nuovo Artista</h6>
        <div class="row g-2">
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Nominativo <span class="text-danger">*</span></label>
                <input type="text" id="new_artista_nominativo" class="form-control form-control-sm"
                    placeholder="Nome completo artista">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Data di nascita</label>
                <input type="date" id="new_artista_data" class="form-control form-control-sm">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Sito web</label>
                <input type="url" id="new_artista_sito" class="form-control form-control-sm"
                    placeholder="https://...">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Social</label>
                <input type="url" id="new_artista_social" class="form-control form-control-sm"
                    placeholder="https://...">
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
    function toggleExtraArtisti(panelId, triggerButton) {
        const panel = document.getElementById(panelId);
        if (!panel) return;

        panel.classList.toggle('d-none');
        const expanded = !panel.classList.contains('d-none');

        if (triggerButton) {
            triggerButton.setAttribute('aria-expanded', expanded ? 'true' : 'false');
        }
    }

    function toggleNewArtistaForm() {
        const f = document.getElementById('new-artista-form');
        f.style.display = f.style.display === 'none' ? 'block' : 'none';
    }

    function saveNewArtista() {
        const nominativo = document.getElementById('new_artista_nominativo').value.trim();
        const data = document.getElementById('new_artista_data').value;
        const sito = document.getElementById('new_artista_sito').value.trim();
        const social = document.getElementById('new_artista_social').value.trim();
        const fb = document.getElementById('new-artista-feedback');

        if (!nominativo) {
            fb.textContent = 'Il nominativo è obbligatorio.';
            fb.className = 'small text-danger';
            return;
        }

        fb.textContent = 'Salvataggio...';
        fb.className = 'small text-muted';

        fetch('{{ route('admin.artisti.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    nominativo,
                    data_nascita: data || null,
                    link_sito: sito || null,
                    link_social: social || null
                }),
            })
            .then(r => r.json())
            .then(data => {
                if (data.id) {
                    const allArtistSelects = document.querySelectorAll('select.artist-select');
                    allArtistSelects.forEach(sel => {
                        const exists = Array.from(sel.options).some(opt => Number(opt.value) === Number(data
                            .id));
                        if (!exists) {
                            const opt = new Option(data.label, data.id, false, false);
                            sel.appendChild(opt);
                        }
                    });

                    const mainSelect = document.getElementById('art_id_artista');
                    if (mainSelect) {
                        mainSelect.value = String(data.id);
                    }

                    fb.textContent = 'Artista creato e selezionato.';
                    fb.className = 'small text-success';
                    ['new_artista_nominativo', 'new_artista_data', 'new_artista_sito', 'new_artista_social']
                    .forEach(id => document.getElementById(id).value = '');
                    setTimeout(() => {
                        document.getElementById('new-artista-form').style.display = 'none';
                        fb.textContent = '';
                    }, 1500);
                } else {
                    fb.textContent = data.message ?? 'Errore durante il salvataggio.';
                    fb.className = 'small text-danger';
                }
            })
            .catch(() => {
                fb.textContent = 'Errore di rete.';
                fb.className = 'small text-danger';
            });
    }
</script>

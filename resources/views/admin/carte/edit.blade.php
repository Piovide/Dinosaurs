<x-admin-layout title="Modifica Carta">
    @php($returnPage = old('page', request('page')))

    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('admin.collezioni.show', ['id' => $carta->col_id_collezione, 'page' => $returnPage]) }}"
            class="btn btn-sm btn-outline-secondary">&larr; {{ $carta->collezione?->nome ?? 'Collezione' }}</a>
        <h2 class="mb-0">Modifica Carta: {{ $carta->titolo }}</h2>
    </div>

    <div class="card border-0 shadow-sm" style="max-width: 640px;">
        <div class="card-body">
            <form method="POST"
                action="{{ route('admin.carte.update', ['id' => $carta->id_carta, 'page' => $returnPage]) }}"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="page" value="{{ $returnPage }}">

                <div class="mb-3">
                    <label class="form-label fw-semibold">Collezione <span class="text-danger">*</span></label>
                    <select name="col_id_collezione"
                        class="form-select @error('col_id_collezione') is-invalid @enderror" required>
                        @foreach ($collezioni as $col)
                            <option value="{{ $col->id_collezione }}"
                                {{ old('col_id_collezione', $carta->col_id_collezione) == $col->id_collezione ? 'selected' : '' }}>
                                {{ $col->nome }}
                            </option>
                        @endforeach
                    </select>
                    @error('col_id_collezione')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Titolo <span class="text-danger">*</span></label>
                    <input type="text" name="titolo" class="form-control @error('titolo') is-invalid @enderror"
                        value="{{ old('titolo', $carta->titolo) }}" required>
                    @error('titolo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Descrizione</label>
                    <textarea name="descrizione" rows="3" class="form-control @error('descrizione') is-invalid @enderror">{{ old('descrizione', $carta->descrizione) }}</textarea>
                    @error('descrizione')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Numero</label>
                        <input type="number" name="numero" min="1"
                            class="form-control @error('numero') is-invalid @enderror"
                            value="{{ old('numero', $carta->numero) }}">
                        @error('numero')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Prefisso</label>
                        <input type="text" name="prefisso" maxlength="20"
                            class="form-control @error('prefisso') is-invalid @enderror"
                            value="{{ old('prefisso', $carta->prefisso) }}" placeholder="es. M, P">
                        @error('prefisso')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-1">
                        <label class="form-label fw-semibold">Suffisso</label>
                        <input type="text" name="suffisso" maxlength="20"
                            class="form-control @error('suffisso') is-invalid @enderror"
                            value="{{ old('suffisso', $carta->suffisso) }}" placeholder="a, b">
                        @error('suffisso')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @include('admin.carte._artista_field', ['selectedArtistaId' => $carta->art_id_artista])
                </div>

                <div class="row g-3 mb-3">
                    @include('admin.carte._rarita_field', [
                        'selectedRaritaId' => $carta->raritas->first()?->id_collezione_rarita,
                        'collezioneId' => $carta->col_id_collezione,
                    ])
                    @include('admin.carte._tipologia_field', [
                        'selectedTipologiaIds' => $carta->tipologie->pluck('id_collezione_tipologia')->toArray(),
                        'tipologie' => $tipologie,
                        'collezioneId' => $carta->col_id_collezione,
                    ])
                </div>

                <div class="row g-3 mb-3">
                    @include('admin.carte._versione_field', [
                        'selectedVersioneIds' => $carta->versioni->pluck('id_versione')->toArray(),
                        'versioni' => $versioni,
                        'collezioneId' => $carta->col_id_collezione,
                    ])
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Immagine</label>
                    @if ($carta->immagine_url)
                        <div class="mb-2">
                            <img src="{{ $carta->immagine_asset }}" alt="{{ $carta->titolo }}" id="imgPreview"
                                style="max-height:160px;border-radius:8px;border:1px solid #ddd;">
                        </div>
                        <p class="text-muted small">Carica una nuova immagine per sostituire quella attuale.</p>
                    @else
                        <div class="mt-1">
                            <img id="imgPreview" src="#" alt="Anteprima"
                                style="display:none;max-height:160px;border-radius:8px;border:1px solid #ddd;">
                        </div>
                    @endif
                    <input type="file" name="immagine" accept="image/*"
                        class="form-control @error('immagine') is-invalid @enderror" onchange="previewImg(this)">
                    @error('immagine')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Formati accettati: JPEG, PNG, GIF, WebP — max 4 MB</div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Salva modifiche</button>
                    <a href="{{ route('admin.collezioni.show', ['id' => $carta->col_id_collezione, 'page' => $returnPage]) }}"
                        class="btn btn-outline-secondary">Annulla</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewImg(input) {
            const preview = document.getElementById('imgPreview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</x-admin-layout>

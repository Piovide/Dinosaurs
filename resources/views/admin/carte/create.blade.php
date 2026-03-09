<x-admin-layout title="Aggiungi Carta">
    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('admin.collezioni.show', $collezione->id_collezione) }}"
           class="btn btn-sm btn-outline-secondary">&larr; {{ $collezione->nome }}</a>
        <h2 class="mb-0">Aggiungi Carta</h2>
    </div>

    <div class="card border-0 shadow-sm" style="max-width: 640px;">
        <div class="card-body">
            <form method="POST"
                  action="{{ route('admin.collezioni.carta.store', $collezione->id_collezione) }}"
                  enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">Titolo <span class="text-danger">*</span></label>
                    <input type="text" name="titolo"
                           class="form-control @error('titolo') is-invalid @enderror"
                           value="{{ old('titolo') }}" required>
                    @error('titolo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Descrizione</label>
                    <textarea name="descrizione" rows="3"
                              class="form-control @error('descrizione') is-invalid @enderror">{{ old('descrizione') }}</textarea>
                    @error('descrizione')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Numero</label>
                        <input type="number" name="numero" min="1"
                               class="form-control @error('numero') is-invalid @enderror"
                               value="{{ old('numero') }}">
                        @error('numero')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Prefisso</label>
                        <input type="text" name="prefisso" maxlength="20"
                               class="form-control @error('prefisso') is-invalid @enderror"
                               value="{{ old('prefisso') }}"
                               placeholder="es. M, P">
                        @error('prefisso')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-1">
                        <label class="form-label fw-semibold">Suffisso</label>
                        <input type="text" name="suffisso" maxlength="20"
                               class="form-control @error('suffisso') is-invalid @enderror"
                               value="{{ old('suffisso') }}"
                               placeholder="a, b">
                        @error('suffisso')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    @include('admin.carte._artista_field', ['selectedArtistaId' => null])
                </div>

                <div class="row g-3 mb-3">
                    @include('admin.carte._rarita_field', ['selectedRaritaId' => null, 'collezioneId' => $collezione->id_collezione])
                    @include('admin.carte._tipologia_field', ['selectedTipologiaIds' => [], 'tipologie' => $tipologie, 'collezioneId' => $collezione->id_collezione])
                </div>

                <div class="row g-3 mb-3">
                    @include('admin.carte._versione_field', ['selectedVersioneIds' => [], 'versioni' => $versioni, 'collezioneId' => $collezione->id_collezione])
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Immagine</label>
                    <input type="file" name="immagine" accept="image/*"
                           class="form-control @error('immagine') is-invalid @enderror"
                           id="imgInput" onchange="previewImg(this)">
                    @error('immagine')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="mt-2">
                        <img id="imgPreview" src="#" alt="Anteprima"
                             style="display:none;max-height:160px;border-radius:8px;border:1px solid #ddd;">
                    </div>
                    <div class="form-text">Formati accettati: JPEG, PNG, GIF, WebP — max 4 MB</div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">Aggiungi Carta</button>
                    <a href="{{ route('admin.collezioni.show', $collezione->id_collezione) }}"
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
                reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</x-admin-layout>

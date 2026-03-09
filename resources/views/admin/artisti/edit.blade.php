<x-admin-layout title="Modifica Artista">
    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('admin.artisti.index') }}" class="btn btn-sm btn-outline-secondary">&larr; Artisti</a>
        <h2 class="mb-0">Modifica: {{ $artista->nominativo }}</h2>
    </div>

    <div class="card border-0 shadow-sm" style="max-width: 540px;">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.artisti.update', $artista->id_artista) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nominativo <span class="text-danger">*</span></label>
                    <input type="text" name="nominativo"
                           class="form-control @error('nominativo') is-invalid @enderror"
                           value="{{ old('nominativo', $artista->nominativo) }}" required>
                    @error('nominativo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Data di nascita</label>
                    <input type="date" name="data_nascita"
                           class="form-control @error('data_nascita') is-invalid @enderror"
                           value="{{ old('data_nascita', $artista->data_nascita) }}">
                    @error('data_nascita')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Sito web</label>
                    <input type="url" name="link_sito"
                           class="form-control @error('link_sito') is-invalid @enderror"
                           placeholder="https://..."
                           value="{{ old('link_sito', $artista->link_sito) }}">
                    @error('link_sito')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Social</label>
                    <input type="url" name="link_social"
                           class="form-control @error('link_social') is-invalid @enderror"
                           placeholder="https://..."
                           value="{{ old('link_social', $artista->link_social) }}">
                    @error('link_social')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Salva modifiche</button>
                    <a href="{{ route('admin.artisti.index') }}" class="btn btn-outline-secondary">Annulla</a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>

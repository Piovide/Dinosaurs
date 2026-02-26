<x-admin-layout title="Modifica Collezione">
    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('admin.collezioni.show', $collezione->id_collezione) }}"
           class="btn btn-sm btn-outline-secondary">&larr; Carte</a>
        <h2 class="mb-0">Modifica Collezione: {{ $collezione->nome }}</h2>
    </div>

    <div class="card border-0 shadow-sm" style="max-width: 560px;">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.collezioni.update', $collezione->id_collezione) }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nome <span class="text-danger">*</span></label>
                    <input type="text" name="nome" class="form-control @error('nome') is-invalid @enderror"
                           value="{{ old('nome', $collezione->nome) }}" required>
                    @error('nome')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Descrizione</label>
                    <textarea name="descrizione" rows="3"
                              class="form-control @error('descrizione') is-invalid @enderror">{{ old('descrizione', $collezione->descrizione) }}</textarea>
                    @error('descrizione')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Numero di carte <span class="text-danger">*</span></label>
                    <input type="number" name="numero_carte" min="1"
                           class="form-control @error('numero_carte') is-invalid @enderror"
                           value="{{ old('numero_carte', $collezione->numero_carte) }}" required>
                    @error('numero_carte')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Data di uscita</label>
                    <input type="date" name="data_uscita"
                           class="form-control @error('data_uscita') is-invalid @enderror"
                           value="{{ old('data_uscita', $collezione->data_uscita) }}">
                    @error('data_uscita')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Salva modifiche</button>
                    <a href="{{ route('admin.collezioni.show', $collezione->id_collezione) }}"
                       class="btn btn-outline-secondary">Annulla</a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>

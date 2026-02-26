<x-admin-layout title="Modifica Utente">
    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('admin.utenti.index') }}" class="btn btn-sm btn-outline-secondary">&larr; Utenti</a>
        <h2 class="mb-0">Modifica utente: {{ $utente->username }}</h2>
    </div>

    <div class="card border-0 shadow-sm" style="max-width: 540px;">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.utenti.update', $utente->id_utente) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $utente->email) }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Ruolo <span class="text-danger">*</span></label>
                    <select name="ruolo" class="form-select @error('ruolo') is-invalid @enderror" required>
                        <option value="utente"     {{ old('ruolo', $utente->ruolo) === 'utente'     ? 'selected' : '' }}>Utente</option>
                        <option value="moderatore" {{ old('ruolo', $utente->ruolo) === 'moderatore' ? 'selected' : '' }}>Moderatore</option>
                        <option value="admin"      {{ old('ruolo', $utente->ruolo) === 'admin'      ? 'selected' : '' }}>Admin</option>
                    </select>
                    @error('ruolo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Salva modifiche</button>
                    <a href="{{ route('admin.utenti.index') }}" class="btn btn-outline-secondary">Annulla</a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>

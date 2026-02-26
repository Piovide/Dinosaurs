<x-admin-layout title="Gestione Utenti">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="mb-0">Utenti</h2>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Ruolo</th>
                        <th class="text-end">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($utenti as $utente)
                        <tr>
                            <td class="text-muted small">{{ $utente->id_utente }}</td>
                            <td class="fw-semibold">{{ $utente->username }}</td>
                            <td>{{ $utente->email }}</td>
                            <td>
                                @if($utente->ruolo === 'admin')
                                    <span class="badge bg-danger">Admin</span>
                                @elseif($utente->ruolo === 'moderatore')
                                    <span class="badge bg-warning text-dark">Moderatore</span>
                                @else
                                    <span class="badge bg-secondary">Utente</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.utenti.edit', $utente->id_utente) }}"
                                   class="btn btn-sm btn-outline-primary">Modifica</a>
                                @if($utente->id_utente !== Auth::id())
                                    <form method="POST"
                                          action="{{ route('admin.utenti.destroy', $utente->id_utente) }}"
                                          class="d-inline"
                                          onsubmit="return confirm('Eliminare l\'utente {{ addslashes($utente->username) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Elimina</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Nessun utente trovato.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $utenti->links() }}
    </div>
</x-admin-layout>

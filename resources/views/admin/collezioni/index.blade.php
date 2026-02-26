<x-admin-layout title="Gestione Collezioni">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="mb-0">Collezioni</h2>
        <a href="{{ route('admin.collezioni.create') }}" class="btn btn-success">+ Nuova Collezione</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Data uscita</th>
                        <th class="text-center">Carte</th>
                        <th class="text-end">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($collezioni as $collezione)
                        <tr>
                            <td class="text-muted small">{{ $collezione->id_collezione }}</td>
                            <td class="fw-semibold">{{ $collezione->nome }}</td>
                            <td>{{ $collezione->data_uscita ?? '—' }}</td>
                            <td class="text-center">
                                <span class="badge bg-info text-dark">{{ $collezione->carte_count }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.collezioni.show', $collezione->id_collezione) }}"
                                   class="btn btn-sm btn-outline-success">Gestisci carte</a>
                                <a href="{{ route('admin.collezioni.edit', $collezione->id_collezione) }}"
                                   class="btn btn-sm btn-outline-primary">Modifica</a>
                                <form method="POST"
                                      action="{{ route('admin.collezioni.destroy', $collezione->id_collezione) }}"
                                      class="d-inline"
                                      onsubmit="return confirm('Eliminare la collezione {{ addslashes($collezione->nome) }} e tutte le sue carte?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Elimina</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Nessuna collezione trovata.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $collezioni->links() }}
    </div>
</x-admin-layout>

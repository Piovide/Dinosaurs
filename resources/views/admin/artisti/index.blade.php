<x-admin-layout title="Gestione Artisti">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="mb-0">Artisti</h2>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Data nascita</th>
                        <th>Sito / Social</th>
                        <th class="text-center">Carte</th>
                        <th class="text-end">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($artisti as $artista)
                        <tr>
                            <td class="text-muted small">{{ $artista->id_artista }}</td>
                            <td class="fw-semibold">{{ $artista->nominativo }}</td>
                            <td>{{ $artista->data_nascita ?? '—' }}</td>
                            <td>
                                @if($artista->link_sito)
                                    <a href="{{ $artista->link_sito }}" target="_blank" rel="noopener" class="me-2 small">Sito</a>
                                @endif
                                @if($artista->link_social)
                                    <a href="{{ $artista->link_social }}" target="_blank" rel="noopener" class="small">Social</a>
                                @endif
                                @if(!$artista->link_sito && !$artista->link_social)
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info text-dark">{{ $artista->carte_count }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.artisti.edit', $artista->id_artista) }}"
                                   class="btn btn-sm btn-outline-primary">Modifica</a>
                                <form method="POST"
                                      action="{{ route('admin.artisti.destroy', $artista->id_artista) }}"
                                      class="d-inline"
                                      onsubmit="return confirm('Eliminare {{ addslashes($artista->nominativo) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Elimina</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Nessun artista trovato.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $artisti->links() }}
    </div>
</x-admin-layout>

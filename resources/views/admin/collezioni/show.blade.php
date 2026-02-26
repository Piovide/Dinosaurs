<x-admin-layout title="Carte della Collezione">
    <div class="d-flex align-items-center gap-2 mb-4 flex-wrap">
        <a href="{{ route('admin.collezioni.index') }}" class="btn btn-sm btn-outline-secondary">&larr; Collezioni</a>
        <h2 class="mb-0">{{ $collezione->nome }}</h2>
        @if($collezione->data_uscita)
            <span class="badge bg-secondary">{{ $collezione->data_uscita }}</span>
        @endif
        <div class="ms-auto">
            <a href="{{ route('admin.collezioni.carta.create', $collezione->id_collezione) }}"
               class="btn btn-success">+ Aggiungi Carta</a>
            <a href="{{ route('admin.collezioni.edit', $collezione->id_collezione) }}"
               class="btn btn-outline-primary">Modifica Collezione</a>
        </div>
    </div>

    @if($collezione->descrizione)
        <p class="text-muted mb-4">{{ $collezione->descrizione }}</p>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>N°</th>
                        <th>Immagine</th>
                        <th>Titolo</th>
                        <th>Artista</th>
                        <th>Rarità</th>
                        <th>Tipo</th>
                        <th class="text-end">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($carte as $carta)
                        <tr>
                            <td class="text-muted small">{{ $carta->numero ?? '—' }}</td>
                            <td>
                                @if($carta->immagine_url)
                                    <img src="{{ $carta->immagine_asset }}"
                                         alt="{{ $carta->titolo }}"
                                         style="width:50px;height:50px;object-fit:cover;border-radius:4px;">
                                @else
                                    <div style="width:50px;height:50px;background:#eee;border-radius:4px;display:flex;align-items:center;justify-content:center;color:#aaa;font-size:20px;">?</div>
                                @endif
                            </td>
                            <td class="fw-semibold">{{ $carta->titolo }}</td>
                            <td>{{ $carta->artista?->cognome }} {{ $carta->artista?->nome }}</td>
                            <td>
                                @if($carta->rarita)
                                    <span class="badge bg-warning text-dark">{{ $carta->rarita->descrizione }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $carta->tipo?->descrizione ?? '—' }}</td>
                            <td class="text-end">
                                <a href="{{ route('carte.show', $carta->id_carta) }}"
                                   class="btn btn-sm btn-outline-secondary" target="_blank">Vedi</a>
                                <a href="{{ route('admin.carte.edit', $carta->id_carta) }}"
                                   class="btn btn-sm btn-outline-primary">Modifica</a>
                                <form method="POST"
                                      action="{{ route('admin.carte.destroy', $carta->id_carta) }}"
                                      class="d-inline"
                                      onsubmit="return confirm('Eliminare la carta {{ addslashes($carta->titolo) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Elimina</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">Nessuna carta in questa collezione.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $carte->links() }}
    </div>
</x-admin-layout>

<x-app-layout title="Artisti" description="Scopri tutti gli artisti che hanno illustrato le carte Tomodachi Press.">

    <div class="px-2 px-md-4 px-lg-5">
        <h1 class="text-white mb-4">Artisti</h1>

        @if ($artisti->isEmpty())
            <p class="text-white-50">Nessun artista trovato.</p>
        @else
            <div class="row g-3">
                @foreach ($artisti as $artista)
                    <div class="col-12 col-sm-6 col-lg-4">
                        <a href="{{ route('artisti.show', $artista->id_artista) }}" class="text-decoration-none">
                            <div class="card h-100 shadow-sm p-3 d-flex flex-row align-items-center gap-3">
                                {{-- Avatar placeholder --}}
                                <div class="rounded-circle bg-success d-flex align-items-center justify-content-center flex-shrink-0"
                                    style="width:56px;height:56px;font-size:1.4rem;color:#fff;">
                                    {{ mb_strtoupper(mb_substr($artista->nominativo, 0, 1)) }}
                                </div>

                                <div class="flex-grow-1 overflow-hidden">
                                    <h5 class="mb-0 text-dark">
                                        {{ $artista->nominativo }}
                                    </h5>
                                    <small class="text-muted">
                                        {{ $artista->carte_count }}
                                        {{ $artista->carte_count === 1 ? 'carta' : 'carte' }}
                                    </small>
                                    @if ($artista->link_sito || $artista->link_social)
                                        <div class="mt-1 d-flex gap-2 flex-wrap">
                                            @if ($artista->link_sito)
                                                <span onclick="event.preventDefault()" class="badge bg-secondary">
                                                    <a href="{{ $artista->link_sito }}" target="_blank" rel="noopener"
                                                        class="text-white text-decoration-none"
                                                        onclick="event.stopPropagation()">
                                                        Sito
                                                    </a>
                                                </span>
                                            @endif
                                            @if ($artista->link_social)
                                                <span class="badge bg-info text-dark">
                                                    <a href="{{ $artista->link_social }}" target="_blank" rel="noopener"
                                                        class="text-dark text-decoration-none"
                                                        onclick="event.stopPropagation()">
                                                        Social
                                                    </a>
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                <x-icon name="chevron-right" color="#086D56" size="md" />
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</x-app-layout>

<x-app-layout title="{{ $artista->nominativo }}">

<div class="px-2 px-md-4 px-lg-5">

    {{-- Artist header --}}
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('artisti.index') }}" class="text-white-50 text-decoration-none">
            &#8592; Artisti
        </a>
    </div>

    <div class="card shadow p-4 mb-5">
        <div class="d-flex align-items-center gap-4 flex-wrap">
            <div class="rounded-circle bg-success d-flex align-items-center justify-content-center flex-shrink-0"
                 style="width:80px;height:80px;font-size:2rem;color:#fff;">
                {{ mb_strtoupper(mb_substr($artista->nominativo, 0, 1)) }}
            </div>
            <div>
                <h2 class="mb-1">{{ $artista->nominativo }}</h2>
                @if($artista->data_nascita)
                    <p class="text-muted mb-1">
                        <x-icon name="calendar3" size="sm" /> {{ \Carbon\Carbon::parse($artista->data_nascita)->translatedFormat('d F Y') }}
                    </p>
                @endif
                <div class="d-flex gap-2 flex-wrap mt-1">
                    @if($artista->link_sito)
                        <a href="{{ $artista->link_sito }}" target="_blank" rel="noopener"
                           class="btn btn-sm btn-outline-secondary">
                            <x-icon name="globe" size="sm" /> Sito web
                        </a>
                    @endif
                    @if($artista->link_social)
                        <a href="{{ $artista->link_social }}" target="_blank" rel="noopener"
                           class="btn btn-sm btn-outline-info">
                            <x-icon name="person-circle" size="sm" /> Social
                        </a>
                    @endif
                </div>
            </div>
            <div class="ms-auto text-end">
                <span class="badge bg-success fs-6 px-3 py-2">
                    {{ $artista->carte_count }} {{ $artista->carte_count === 1 ? 'carta' : 'carte' }}
                </span>
            </div>
        </div>
    </div>

    {{-- Cards grid --}}
    <h4 class="text-white mb-3">Carte di {{ $artista->nominativo }}</h4>

    @if($carte->isEmpty())
        <p class="text-white-50">Nessuna carta presente.</p>
    @else
        <div class="row" id="cards-grid">
            @foreach($carte as $carta)
                <x-carta-card :carta="$carta" />
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($carte->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $carte->links() }}
            </div>
        @endif
    @endif

</div>

</x-app-layout>

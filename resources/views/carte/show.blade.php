<x-app-layout title="{{ $carta->titolo }}">

<div class="row g-4 px-2 px-md-4">
    <div class="col-12 col-md-5 d-flex justify-content-center justify-content-md-end">
        <img src="{{ $carta->immagine_asset }}" class="img-fluid rounded shadow" alt="{{ $carta->titolo }}"
             style="max-height:70vh; object-fit:contain;">
    </div>
    <div class="col-12 col-md-7 text-white">
        <h2>{{ $carta->titolo }}</h2>
        <p><strong>Collezione:</strong> {{ $carta->collezione->nome ?? '-' }}</p>
        <p><strong>Artista:</strong> {{ $carta->artista->nominativo ?? '-' }}</p>
        <p><strong>Rarità:</strong>
            @forelse($carta->raritas as $r)
                <x-icona-badge :record="$r" size="18px" class="me-1" />
                {{ $r->nome }}@if(!$loop->last), @endif
            @empty
                —
            @endforelse
        </p>
        <p><strong>Tipo:</strong>
            @forelse($carta->tipologie as $t)
                <x-icona-badge :record="$t" size="18px" class="me-1" />
                {{ $t->nome }}@if(!$loop->last), @endif
            @empty
                —
            @endforelse
        </p>
        <p>{{ $carta->descrizione }}</p>
        <div class="d-flex flex-wrap gap-2 mt-3">
            <a href="{{ route('home') }}" class="btn btn-secondary">Torna all'elenco</a>
            @auth
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('admin.carte.edit', $carta->id_carta) }}" class="btn btn-warning">&#9998; Modifica</a>
                    <a href="{{ route('admin.collezioni.show', $carta->col_id_collezione) }}" class="btn btn-outline-warning">Vai alla Collezione</a>
                @endif
            @endauth
        </div>
    </div>
</div>

</x-app-layout>

<x-app-layout title="{{ $carta->titolo }}">

<div class="row g-4">
    <div class="col-md-6 d-flex justify-content-end">
        <img src="{{ $carta->immagine_asset }}" class="img-fluid rounded shadow" alt="{{ $carta->titolo }}">
    </div>
    <div class="col-md-6 text-white">
        <h2>{{ $carta->titolo }}</h2>
        <p><strong>Collezione:</strong> {{ $carta->collezione->nome ?? '-' }}</p>
        <p><strong>Artista:</strong> {{ $carta->artista->nome ?? '-' }} {{ $carta->artista->cognome ?? '' }}</p>
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
        <a href="{{ route('home') }}" class="btn btn-secondary mt-3">Torna all'elenco</a>
        @auth
            @if(Auth::user()->isAdmin())
                <a href="{{ route('admin.carte.edit', $carta->id_carta) }}"
                   class="btn btn-warning mt-3">&#9998; Modifica (Admin)</a>
                <a href="{{ route('admin.collezioni.show', $carta->col_id_collezione) }}"
                   class="btn btn-outline-warning mt-3">Vai alla Collezione (Admin)</a>
            @endif
        @endauth
    </div>
</div>

</x-app-layout>

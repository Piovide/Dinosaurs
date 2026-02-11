<x-app-layout title="{{ $carta->titolo }}">

<div class="row g-4">
    <div class="col-md-6">
        <img src="{{ $carta->immagine_url }}" class="img-fluid rounded shadow" alt="{{ $carta->titolo }}">
    </div>
    <div class="col-md-6">
        <h2>{{ $carta->titolo }}</h2>
        <p><strong>Collezione:</strong> {{ $carta->collezione->nome ?? '-' }}</p>
        <p><strong>Artista:</strong> {{ $carta->artista->nome ?? '-' }} {{ $carta->artista->cognome ?? '' }}</p>
        <p><strong>Rarità:</strong> {{ $carta->rarita->descrizione ?? '-' }}</p>
        <p><strong>Tipo:</strong> {{ $carta->tipo->descrizione ?? '-' }}</p>
        <p>{{ $carta->descrizione }}</p>
        <a href="{{ route('home') }}" class="btn btn-secondary mt-3">Torna all'elenco</a>
    </div>
</div>

</x-app-layout>

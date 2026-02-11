@props(['carta'])
<div class="col-md-3 mb-4">
    <div class="card h-100 shadow-sm">
        <img src="{{ $carta->immagine_url }}" class="card-img-top" alt="{{ $carta->titolo }}">
        <div class="card-body d-flex flex-column">
            <h5 class="card-title">{{ $carta->titolo }}</h5>
            <p class="card-text mb-1"><strong>Rarità:</strong> {{ $carta->rarita->descrizione ?? '-' }}</p>
            <p class="card-text mb-3"><strong>Tipo:</strong> {{ $carta->tipo->descrizione ?? '-' }}</p>
            <a href="{{ route('carte.show', $carta->id_carta) }}" class="btn btn-success mt-auto">Dettaglio</a>
        </div>
    </div>
</div>

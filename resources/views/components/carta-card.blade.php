@props(['carta'])
<div class="col-md-2 mb-4">
    <div class="card h-100 shadow-sm p-3 rounded" data-carta-id="{{ $carta->id_carta }}">
        <div class="img-container position-relative">
            <img src="{{ $carta->immagine_asset }}" class="card-img-top carta-image" alt="{{ $carta->titolo }}" data-carta-id="{{ $carta->id_carta }}" style="cursor: pointer;" >
            <div class="card-info position-absolute bottom-0 start-0 p-2 bg-dark bg-opacity-50 text-white rounded-end rounded-bottom-0">
                <small class="d-flex">{{ $carta->numero }} / {{ $carta->collezione->numero_carte }}<x-icon name="star" color="white" class="ms-2" /></small>
            </div>
        </div>
        <div class="card-body d-flex flex-column">
            <h5 class="card-title text-center">{{ $carta->titolo }}</h5>
            <p class="card-text mb-1 text-center">{{ $carta->artista->nome }} {{ $carta->artista->cognome }}</p>
            <div class="row btn-container">
                <div class="col-4">
                    <x-button color="transparent" size="md" class="shadow-sm w-100" data-btn-type="decrement">
                        <x-icon name="dash-lg" color="black" />
                    </x-button>
                </div>
                <input type="text" name="numero_in_collezione" data-id-carta="{{ $carta->id_carta }}" class="rounded border border-secondary text-center col-4" value="{{ auth()->check() ? auth()->user()->collezioniUtenti()->where('car_id_carta', $carta->id_carta)->value('quantita') ?? 0 : 0 }}">
                <div class="col-4">
                    <x-button color="transparent" size="md" class="shadow-sm w-100" data-btn-type="increment">
                        <x-icon name="plus-lg" color="black"/>
                    </x-button>
                </div>
            </div>
        </div>
    </div>
</div>

@props(['carta'])

<style>
    .card {
        position: relative;
        width: 100%;
        & .img-container {
            aspect-ratio: 63.5 / 88;
        }
    }
</style>

<div class="col-md-3 mb-4">
    <div class="card h-100 shadow-sm p-3 rounded">
        <div class="img-container position-relative">
            <img src="{{ $carta->immagine_url }}" class="card-img-top" alt="{{ $carta->titolo }}" >
            <div class="card-info position-absolute bottom-0 start-0 p-2 bg-dark bg-opacity-50 text-white rounded-end rounded-bottom-0">
                <small class="d-flex">{{ $carta->numero }} / {{ $carta->collezione->numero_carte }}<x-icon name="star" color="white" class="ms-2" /></small>
            </div>
        </div>
        <div class="card-body d-flex flex-column">
            <h5 class="card-title text-center">{{ $carta->titolo }}</h5>
            <p class="card-text mb-1 text-center">{{ $carta->artista->nome }} {{ $carta->artista->cognome }}</p>
            <div class="row btn-container">
                <div class="col-4">
                    <x-button color="transparent" size="md" class="shadow-sm">
                        <x-icon name="dash-lg" color="black" />
                    </x-button>{{-- //TODO impostare il value iniziale tramite $utente->collezione_utente->where('car_id_carta', $carta->id_carta)->quantita --}}
                </div>
                    <input type="text" name="numero_in_collezione" data-id-carta="{{ $carta->id_carta }}" class="rounded border border-secondary text-center col-4">
                <div class="col-4">
                    <x-button color="transparent" size="md" class="shadow-sm">
                        <x-icon name="plus-lg" color="black"/>
                    </x-button>
                </div>
            </div>
        </div>
    </div>
</div>

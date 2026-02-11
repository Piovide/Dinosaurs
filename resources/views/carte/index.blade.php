<x-app-layout title="Enciclopedia Carte Dinosaurs">

<h1 class="mb-4">Enciclopedia Carte</h1>

<form method="GET" class="mb-4">
    <div class="row g-2">
        <div class="col-md-3">
            <select name="rarita" class="form-select">
                <option value="">Tutte le rarità</option>
                @foreach($rarita as $r)
                    <option value="{{ $r->id_dizionario }}" {{ request('rarita') == $r->id_dizionario ? 'selected' : '' }}>
                        {{ $r->descrizione }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="tipo" class="form-select">
                <option value="">Tutti i tipi</option>
                @foreach($tipi as $t)
                    <option value="{{ $t->id_dizionario }}" {{ request('tipo') == $t->id_dizionario ? 'selected' : '' }}>
                        {{ $t->descrizione }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-success w-100">Filtra</button>
        </div>
    </div>
</form>

<div class="row">
    @foreach($carte as $carta)
        <x-carta-card :carta="$carta" />
    @endforeach
</div>

<div class="mt-4">
    {{ $pagination->withQueryString()->links() }}
</div>

</x-app-layout>

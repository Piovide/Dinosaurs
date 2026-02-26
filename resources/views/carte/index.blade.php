@props(['username' => null])
<x-app-layout title="{{ isset($collezione) && $collezione ? $collezione->nome . ' — Dinosaurs' : 'Enciclopedia Carte Dinosaurs' }}">
    @if($username)
        <h1 class="text-center mb-4">Collezione di {{ $username }}</h1>
    @endif
    @if(isset($collezione) && $collezione)
        <h2 class="text-center mb-1">{{ $collezione->nome }}</h2>
        <p class="text-center text-muted mb-4 small">
            {{ $collezione->descrizione }}
            &nbsp;&middot;&nbsp;
            <a href="{{ route('home') }}">Vedi tutte le carte</a>
        </p>
    @endif
    <form method="GET" class="mb-4 mx-auto w-50">
        @if(request('collezione'))
            <input type="hidden" name="collezione" value="{{ request('collezione') }}">
        @endif
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
<div class="w-100 mx-auto px-5">

    <div class="row">
        @if($carte->isEmpty())
            <p class="text-center">Nessuna carta trovata.</p>
        @endif
        @foreach($carte as $carta)
            <x-carta-card :carta="$carta" />
        @endforeach
    </div>

    <div class="mt-4">
        {{ $pagination->withQueryString()->links() }}
    </div>
</div>
</x-app-layout>

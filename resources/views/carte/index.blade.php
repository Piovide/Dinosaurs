@props(['username' => null])
<x-app-layout title="{{ isset($collezione) && $collezione ? $collezione->nome : 'Tomodachi tracker' }}">
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
    <form method="GET" class="mb-4 mx-auto" style="max-width:600px;">
        @if(request('collezione'))
            <input type="hidden" name="collezione" value="{{ request('collezione') }}">
        @endif
        <div class="row g-2">
            <div class="col-12 col-sm-5">
                <select name="rarita" class="form-select">
                    <option value="">Tutte le rarità</option>
                    @foreach($rarita as $r)
                        <option value="{{ $r->id_collezione_rarita }}" {{ request('rarita') == $r->id_collezione_rarita ? 'selected' : '' }}>
                            {{ $r->nome }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-sm-5">
                <select name="tipologia" class="form-select">
                    <option value="">Tutte le tipologie</option>
                    @foreach($tipologie as $t)
                        <option value="{{ $t->id_collezione_tipologia }}" {{ request('tipologia') == $t->id_collezione_tipologia ? 'selected' : '' }}>
                            {{ $t->nome }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-sm-2">
                <button class="btn btn-success w-100">Filtra</button>
            </div>
        </div>
    </form>
<div class="w-100 mx-auto px-2 px-md-4 px-lg-5">

    <div class="row" id="cards-grid">
        @if($carte->isEmpty())
            <p class="text-center" id="no-cards-msg">Nessuna carta trovata.</p>
        @endif
        @include('carte._cards', ['carte' => $carte])
    </div>

    {{-- Sentinel for infinite scroll --}}
    <div id="scroll-sentinel" class="mt-4 pb-4 text-center" style="min-height:40px;">
        <div id="loading-spinner" class="spinner-border text-secondary" role="status" style="display:none;">
            <span class="visually-hidden">Caricamento...</span>
        </div>
    </div>
</div>

<script>
(function () {
    let hasMore  = @json($pagination->hasMorePages());
    let nextPage = {{ $pagination->currentPage() + 1 }};
    let loading  = false;

    const grid     = document.getElementById('cards-grid');
    const sentinel = document.getElementById('scroll-sentinel');
    const spinner  = document.getElementById('loading-spinner');

    if (!hasMore) return;

    const observer = new IntersectionObserver(async function (entries) {
        if (!entries[0].isIntersecting || loading || !hasMore) return;
        loading = true;
        spinner.style.display = 'inline-block';
        try {
            const params = new URLSearchParams(window.location.search);
            params.set('page', nextPage);
            const resp = await fetch('?' + params.toString(), {
                headers: { 'Accept': 'application/json' },
            });
            const data = await resp.json();
            const noMsg = document.getElementById('no-cards-msg');
            if (noMsg) noMsg.remove();
            grid.insertAdjacentHTML('beforeend', data.html);
            hasMore  = data.hasMore;
            nextPage = data.nextPage;
        } catch (e) {
            console.error('Lazy load error:', e);
        } finally {
            loading = false;
            spinner.style.display   = 'none';
            if (!hasMore) observer.disconnect();
        }
    }, { rootMargin: '300px' });

    observer.observe(sentinel);
})();
</script>
</x-app-layout>

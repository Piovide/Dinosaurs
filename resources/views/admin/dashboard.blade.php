<x-admin-layout title="Dashboard">
    <h2 class="mb-4">Dashboard</h2>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center p-4">
                <div class="fs-1 text-success mb-2">&#128196;</div>
                <h5 class="mb-0">Collezioni &amp; Carte</h5>
                <p class="text-muted small mt-1">Gestisci le serie e aggiungi nuove carte</p>
                <a href="{{ route('admin.collezioni.index') }}" class="btn btn-success btn-sm mt-2">Vai</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center p-4">
                <div class="fs-1 text-primary mb-2">&#128100;</div>
                <h5 class="mb-0">Utenti</h5>
                <p class="text-muted small mt-1">Gestisci gli utenti e i loro ruoli</p>
                <a href="{{ route('admin.utenti.index') }}" class="btn btn-primary btn-sm mt-2">Vai</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center p-4">
                <div class="fs-1 text-warning mb-2">&#127760;</div>
                <h5 class="mb-0">Sito pubblico</h5>
                <p class="text-muted small mt-1">Visualizza il sito come un utente normale</p>
                <a href="{{ route('home') }}" class="btn btn-warning btn-sm mt-2">Vai</a>
            </div>
        </div>
    </div>
</x-admin-layout>

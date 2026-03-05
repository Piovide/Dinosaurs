<p class="text-uppercase text-white-50 px-3 small fw-bold mt-2 mb-1">Gestione</p>
<nav class="nav flex-column">
    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
       href="{{ route('admin.dashboard') }}">&#9632; Dashboard</a>
    <a class="nav-link {{ request()->routeIs('admin.collezioni.*') ? 'active' : '' }}"
       href="{{ route('admin.collezioni.index') }}">&#9632; Collezioni &amp; Carte</a>
    <a class="nav-link {{ request()->routeIs('admin.utenti.*') ? 'active' : '' }}"
       href="{{ route('admin.utenti.index') }}">&#9632; Utenti</a>
    <a class="nav-link {{ request()->routeIs('admin.artisti.*') ? 'active' : '' }}"
       href="{{ route('admin.artisti.index') }}">&#9632; Artisti</a>
</nav>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin Panel' }} — Dinosaurs</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('/build/assets/app-DNLR3VPH.css') }}">
    <style>
        body { background-color: #f8f9fa; }
        .admin-sidebar {
            min-height: calc(100vh - 56px);
            background: #1a1a2e;
            color: #eee;
            width: 240px;
            flex-shrink: 0;
        }
        .admin-sidebar .nav-link {
            color: #ccc;
            padding: .5rem 1.25rem;
            border-radius: 6px;
            margin: 2px 8px;
        }
        .admin-sidebar .nav-link:hover,
        .admin-sidebar .nav-link.active {
            background: #2a7f62;
            color: #fff;
        }
        .admin-sidebar .nav-link i { margin-right: 8px; }
        .admin-topbar {
            background: #2a7f62;
            color: #fff;
        }
        .admin-topbar a { color: #fff; text-decoration: none; }
        .admin-content { flex: 1; padding: 2rem; overflow-x: auto; }
        .badge-admin   { background: #dc3545; }
        .badge-mod     { background: #fd7e14; }
        .badge-utente  { background: #6c757d; }
    </style>
</head>
<body>
<nav class="navbar admin-topbar px-3 py-2 d-flex align-items-center justify-content-between">
    <a href="{{ route('admin.dashboard') }}" class="fw-bold fs-5">&#9998; Admin Panel — Dinosaurs</a>
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('home') }}" class="btn btn-sm btn-outline-light">&#8592; Sito</a>
        <span class="text-white-50 small">{{ Auth::user()->username }}</span>
        <form method="POST" action="{{ route('auth.logout') }}" class="d-inline m-0">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-light">Logout</button>
        </form>
    </div>
</nav>

<div class="d-flex">
    <aside class="admin-sidebar py-3">
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
    </aside>

    <main class="admin-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{ $slot }}
    </main>
</div>
</body>
</html>

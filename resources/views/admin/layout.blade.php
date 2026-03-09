<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin Panel' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { background-color: #f8f9fa; }

        /* ── Sidebar (desktop) ───────────────────────────────── */
        .admin-sidebar {
            min-height: calc(100vh - 56px);
            background: #1a1a2e;
            color: #eee;
            width: 220px;
            flex-shrink: 0;
        }
        /* ── Offcanvas sidebar shares the same look ──────────── */
        #adminOffcanvas { background: #1a1a2e; color: #eee; width: 220px; }
        #adminOffcanvas .btn-close { filter: invert(1); }

        .admin-sidebar .nav-link,
        #adminOffcanvas .nav-link {
            color: #ccc;
            padding: .5rem 1.25rem;
            border-radius: 6px;
            margin: 2px 8px;
        }
        .admin-sidebar .nav-link:hover,  .admin-sidebar .nav-link.active,
        #adminOffcanvas .nav-link:hover, #adminOffcanvas .nav-link.active {
            background: #2a7f62;
            color: #fff;
        }

        .admin-topbar { background: #2a7f62; color: #fff; }
        .admin-topbar a { color: #fff; text-decoration: none; }
        .admin-content { flex: 1; min-width: 0; padding: 1.5rem; overflow-x: auto; }
    </style>
</head>
<body>

{{-- ── Top bar ──────────────────────────────────────────────── --}}
<nav class="navbar admin-topbar px-3 py-2 d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-2">
        {{-- Hamburger: only visible on small screens --}}
        <button class="btn btn-sm btn-outline-light d-md-none"
                type="button"
                data-bs-toggle="offcanvas"
                data-bs-target="#adminOffcanvas"
                aria-controls="adminOffcanvas">
            &#9776;
        </button>
        <a href="{{ route('admin.dashboard') }}" class="fw-bold fs-6 fs-sm-5 text-white text-decoration-none">&#9998; Admin — Tomodachi tracker</a>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('home') }}" class="btn btn-sm btn-outline-light">&#8592; Sito</a>
        <span class="text-white-50 small d-none d-sm-inline">{{ Auth::user()->username }}</span>
        <form method="POST" action="{{ route('auth.logout') }}" class="d-inline m-0">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-light">Logout</button>
        </form>
    </div>
</nav>

{{-- ── Offcanvas sidebar (mobile) ───────────────────────────── --}}
<div class="offcanvas offcanvas-start" tabindex="-1" id="adminOffcanvas" aria-labelledby="adminOffcanvasLabel">
    <div class="offcanvas-header">
        <h6 class="offcanvas-title text-white" id="adminOffcanvasLabel">&#9998; Admin Panel</h6>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        @include('admin._sidebar_nav')
    </div>
</div>

{{-- ── Page body ─────────────────────────────────────────────── --}}
<div class="d-flex">

    {{-- Desktop sidebar: hidden on xs/sm, visible md+ --}}
    <aside class="admin-sidebar py-3 d-none d-md-block">
        @include('admin._sidebar_nav')
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

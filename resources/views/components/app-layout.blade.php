@props(['title' => 'Tomodachi tracker'])

<!DOCTYPE html>
<html lang="it">
<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Gestisci e traccia la tua collezione di carte Tomodachi press.">
    <meta name="keywords" content="tomodachi tracker, collezione carte, card tracker, tracker tomodachi press, tracker, lista carte tomodachi press">
    <meta name="robots" content="index, follow">
    <meta property="og:title" content="Tomodachi Tracker">
    <meta property="og:description" content="Traccia la tua collezione di carte Tomodachi">
    <meta property="og:image" content="https://www.tomodachi-tracker.it/preview.png">
    <meta property="og:url" content="https://www.tomodachi-tracker.it">
    <meta property="og:type" content="website">
    <link rel="canonical" href="https://www.tomodachi-tracker.it">
    <title>{{ $title }}</title>
    <script type="application/ld+json">
        {
         "@context": "https://schema.org",
         "@type": "WebSite",
         "name": "Tomodachi Tracker",
         "url": "https://www.tomodachi-tracker.it",
         "description": "Gestisci e traccia la tua collezione di carte Tomodachi."
        }
    </script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4" id="main-navbar">
    <div class="container-fluid px-3 px-lg-5">

        {{-- Brand / Logo --}}
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ asset('img/dario-moccia.png') }}" alt="Logo"
                 style="width: 8rem; mix-blend-mode: multiply;">
        </a>

        {{-- Mobile toggler --}}
        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#mainNavCollapse"
                aria-controls="mainNavCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        {{-- Collapsible nav --}}
        @php $navCollezioni = \App\Models\Collezione::orderBy('data_uscita', 'desc')->get(); @endphp
        <div class="collapse navbar-collapse" id="mainNavCollapse">

            {{-- Centre links --}}
            <ul class="navbar-nav mx-auto gap-1 gap-lg-2 my-2 my-lg-0">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="{{ route('home') }}"
                       role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Home
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('home') }}">Tutte le carte</a></li>
                        @if($navCollezioni->isNotEmpty())
                            <li><hr class="dropdown-divider"></li>
                            @foreach($navCollezioni as $col)
                                <li>
                                    <a class="dropdown-item"
                                       href="{{ route('home', ['collezione' => $col->id_collezione]) }}">
                                        {{ $col->nome }}
                                    </a>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link"
                       @auth href="{{ route('collezione') }}"
                       @else onclick="openLoginModal(); return false;" @endauth>
                        Collezione
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('artisti.index') }}">Artisti</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('crediti') }}">Crediti</a>
                </li>
            </ul>

            {{-- Right: profile + ko-fi --}}
            <ul class="navbar-nav gap-1 align-items-lg-center my-2 my-lg-0">
                <li class="nav-item dropdown">
                    <button class="nav-link btn btn-link d-flex align-items-center gap-1"
                            id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                            style="border: none; cursor: pointer;">
                        <x-icon name="person" size="lg" color="black" />
                        @auth
                            <span class="d-lg-none small">{{ Auth::user()->username }}</span>
                        @endauth
                    </button>
                    <ul class="dropdown-menu dropdown-menu-lg-end" aria-labelledby="profileDropdown">
                        @auth
                            <li><h6 class="dropdown-header">{{ Auth::user()->username }}</h6></li>
                            @if(Auth::user()->isAdmin())
                                <li>
                                    <a class="dropdown-item text-warning fw-semibold"
                                       href="{{ route('admin.dashboard') }}">
                                        &#9998; Admin Panel
                                    </a>
                                </li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('auth.logout') }}" class="d-inline w-100">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">Logout</button>
                                </form>
                            </li>
                        @else
                            <li>
                                <a class="dropdown-item" href="#" onclick="openLoginModal(); return false;">Login</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" onclick="openRegisterModal(); return false;">Registrati</a>
                            </li>
                        @endauth
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center" href="https://ko-fi.com/Piovide">
                        <img src="https://storage.ko-fi.com/cdn/logomarkLogo.png" alt="Ko-fi"
                             class="icon icon-lg" style="aspect-ratio: auto;">
                    </a>
                </li>
            </ul>

        </div>
    </div>
</nav>

<div class="container mw-100" id="app-content">
    {{ $slot }}
</div>

<x-auth-modal />

</body>
</html>

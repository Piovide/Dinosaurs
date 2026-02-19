@props(['title' => 'Dinosaurs'])

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4" id="main-navbar">
    <div class="w-100 d-flex justify-content-between align-items-center mx-5 px-5 py-3">
        <div class="">
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('img/logo_rubato.png') }}" alt="Logo" class="d-inline-block align-top" style="width: 15rem">
            </a>
        </div>
        <div id="nav-bar-home">
           {{-- nav centrale: carte, artisti, collezione --}}
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-5 d-flex align-items-center">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" @auth href="{{ route('collezione') }}" @else onclick="openLoginModal(); return false;" @endauth>Collezione</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="">Artisti (WIP)</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="">Crediti</a>
                </li>
            </ul>
        </div>
        <div class="">
            <ul class="navbar-nav ms-auto gap-3 mb-2 mb-lg-0 d-flex align-items-center">
                <li class="nav-item dropdown">
                    <button class="nav-link btn btn-link d-flex align-items-center" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="border: none; cursor: pointer;">
                        <x-icon name="person" size="lg" color="black" />
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        @auth
                            <li><h6 class="dropdown-header">{{ Auth::user()->username }}</h6></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('auth.logout') }}" class="d-inline w-100">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        Logout
                                    </button>
                                </form>
                            </li>
                        @else
                            <li>
                                <a class="dropdown-item" href="#" onclick="openLoginModal(); return false;">
                                    Login
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" onclick="openRegisterModal(); return false;">
                                    Registrati
                                </a>
                            </li>
                        @endauth
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center" href="https://ko-fi.com/Piovide">
                        <img src="https://storage.ko-fi.com/cdn/logomarkLogo.png" alt="Ko-fi Logo" class="icon icon-lg" style="aspect-ratio: auto;">
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

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dinosaurs Card Encyclopedia' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background-color: #f7f7f7;
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 1.8rem;
            color: #2a7f62 !important;
        }
        .card-title {
            font-weight: 600;
        }
        .card-text {
            font-size: 0.9rem;
        }
        .card img {
            max-height: 200px;
            object-fit: cover;
        }
        footer {
            margin-top: 50px;
            padding: 20px 0;
            background-color: #2a7f62;
            color: white;
            text-align: center;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">Dinosaurs</a>
        <div class="ms-auto">
            @auth
                <span class="me-3">{{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('auth.logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger">Logout</button>
                </form>
            @else
                <button class="btn btn-sm btn-primary me-2" onclick="openLoginModal()">Login</button>
                <button class="btn btn-sm btn-outline-primary" onclick="openRegisterModal()">Registrati</button>
            @endauth
        </div>
    </div>
</nav>

<div class="container">
    {{ $slot }}
</div>

<footer>
    &copy; 2026 copy
</footer>

<x-auth-modal />

</body>
</html>

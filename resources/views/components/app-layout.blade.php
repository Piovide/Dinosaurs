@props(['title' => 'Dinosaurs'])

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
    <div class="w-100 d-flex justify-content-between align-items-center mx-5 px-5">
        <div class="">
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('img/logo_rubato.png') }}" alt="Logo" width="150" height="75" class="d-inline-block align-top">
            </a>
        </div>
        <div id="nav-bar-home">
           {{-- nav centrale: carte, artisti, collezione --}}
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-5 d-flex align-items-center">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="">Collezione</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="">Artisti</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="">Crediti</a>
                </li>
            </ul>
        </div>
        <div class="">
            {{-- opzioni: profilo(icona), ko-fi (link icona)  --}}
            <ul class="navbar-nav ms-auto gap-3 mb-2 mb-lg-0 d-flex align-items-center">
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center" href="">
                        <x-icon name="person" size="lg" color="black" />
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center" href="https://ko-fi.com/Piovide">
                        <img src="https://storage.ko-fi.com/cdn/logomarkLogo.png" alt="Ko-fi Logo" class="icon icon-lg">
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    {{ $slot }}
</div>

</body>
</html>

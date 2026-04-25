@php
    $appName = config('app.name');
    $pageTitle = trim(($title ?? 'Errore') . ' - ' . $appName);
@endphp
<!DOCTYPE html>
<html lang="it">

<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $pageTitle }}</title>
</head>

<body class="error-page-body">
    <div class="error-page-background"></div>

    <main class="error-page-wrap container position-relative">
        <section class="error-card">
            <a href="{{ route('home') }}" aria-label="Torna alla home">
                <img src="{{ asset('logo.png') }}" alt="Logo {{ $appName }}" class="error-logo">
            </a>

            <p class="error-code">{{ $code ?? 'Errore' }}</p>
            <h1 class="error-title">{{ $title ?? 'Si e verificato un errore' }}</h1>

            <p class="error-message">
                {{ $message ?? 'Non siamo riusciti a completare la richiesta.' }}
            </p>

            <div class="d-flex justify-content-center flex-wrap gap-2">
                <a href="{{ url()->previous() }}" class="btn btn-light">Torna indietro</a>
                <a href="{{ route('home') }}" class="btn btn-outline-light">Vai alla home</a>
            </div>

            @if (!empty($help))
                <p class="error-help">{{ $help }}</p>
            @endif
        </section>
    </main>

    <footer class="site-footer position-relative">
        <p class="mb-0">
            Le immagini e i diritti delle carte sono di esclusiva proprieta di
            <strong>Tomodachi Press</strong>. Questo sito e un progetto fan-made non ufficiale.
        </p>
        <p class="footer-copy mb-0">&copy; {{ date('Y') }} {{ $appName }}</p>
    </footer>
</body>

</html>

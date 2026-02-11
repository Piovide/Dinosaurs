<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    </div>
</nav>

<div class="container">
    {{ $slot }}
</div>

<footer>
    &copy; 2026 copy 
</footer>

</body>
</html>

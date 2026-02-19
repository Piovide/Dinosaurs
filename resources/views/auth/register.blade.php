<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Registrazione
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">Crea un nuovo account</h3>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('auth.register') }}">
                @csrf

                <div class="mb-4">
                    <label for="nome" class="block text-sm font-medium text-gray-700">Nome</label>
                    <input type="text" id="nome" name="nome" value="{{ old('nome') }}" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm" autofocus>
                    @error('nome')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="cognome" class="block text-sm font-medium text-gray-700">Cognome</label>
                    <input type="text" id="cognome" name="cognome" value="{{ old('cognome') }}" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm">
                    @error('cognome')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm">
                    @error('username')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" id="password" name="password" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm">
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Conferma Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm">
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-2 rounded-md hover:bg-blue-700">
                    Registrati
                </button>
            </form>

            <p class="text-center mt-4 text-sm">
                Hai già un account? <a href="{{ route('auth.login') }}" class="text-blue-600 hover:underline">Accedi qui</a>
            </p>
        </div>
    </div>
</x-app-layout>

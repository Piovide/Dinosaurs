<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Login
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">Accedi al tuo account</h3>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if (session('info'))
                <div class="alert alert-info">{{ session('info') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('auth.login') }}">
                @csrf

                <div class="mb-4">
                    <label for="username" class="block text-sm font-medium text-gray-700">Username o Email</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm" autofocus>
                    @error('username')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" id="password" name="password" required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm">
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 text-white font-semibold py-2 rounded-md hover:bg-blue-700">
                    Accedi
                </button>
            </form>

            <p class="text-center mt-3 small">
                <a href="{{ route('password.request') }}">Password dimenticata?</a>
            </p>

            <p class="text-center mt-2 text-sm">
                Non hai un account? <a href="{{ route('auth.register') }}"
                    class="text-blue-600 hover:underline">Registrati qui</a>
            </p>
        </div>
    </div>
</x-app-layout>

<x-app-layout title="Password dimenticata">
    <div class="py-5">
        <div class="mx-auto bg-white rounded-lg shadow-md p-4" style="max-width:480px;">
            <h3 class="text-lg font-semibold mb-3">Reimposta la password</h3>

            <p class="text-muted mb-3">
                Inserisci la tua email e ti invieremo un link per reimpostare la password.
            </p>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
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

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                        class="form-control">
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    Invia link di reset
                </button>
            </form>

            <p class="text-center mt-3 small">
                <a href="{{ route('auth.login') }}">Torna al login</a>
            </p>
        </div>
    </div>
</x-app-layout>

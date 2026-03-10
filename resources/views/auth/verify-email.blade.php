<x-app-layout title="Verifica Email">
    <div class="py-5">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-4" style="max-width:480px;">
            <h3 class="text-lg font-semibold mb-3">Verifica il tuo indirizzo email</h3>

            <p class="text-muted mb-3">
                Grazie per esserti registrato! Prima di continuare, controlla la tua casella di posta e clicca sul link
                di verifica che ti abbiamo inviato.
            </p>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn btn-primary w-100">
                    Invia di nuovo il link di verifica
                </button>
            </form>

            <form method="POST" action="{{ route('auth.logout') }}" class="mt-3">
                @csrf
                <button type="submit" class="btn btn-link text-muted p-0">Esci</button>
            </form>
        </div>
    </div>
</x-app-layout>

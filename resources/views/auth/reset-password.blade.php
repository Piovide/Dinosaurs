<x-app-layout title="Reimposta Password">
    <div class="py-5">
        <div class="mx-auto bg-white rounded-lg shadow-md p-4" style="max-width:480px;">
            <h3 class="text-lg font-semibold mb-3">Scegli una nuova password</h3>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $email) }}" required
                        autofocus class="form-control">
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Nuova password</label>
                    <input type="password" id="password" name="password" required class="form-control">
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Conferma password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        class="form-control">
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    Reimposta password
                </button>
            </form>
        </div>
    </div>
</x-app-layout>

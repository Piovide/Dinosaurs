<!-- Modal Login -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loginModalLabel">Accedi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="loginForm" method="POST" action="{{ route('auth.login') }}">
                @csrf
                <div class="modal-body">
                    @if(session('open_modal') === 'login' && $errors->any())
                        <div class="alert alert-danger py-2 mb-3">
                            <ul class="mb-0 ps-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label for="login-username" class="form-label">Username o Email</label>
                        <input type="text" class="form-control @if(session('open_modal')==='login' && $errors->has('username')) is-invalid @endif"
                               id="login-username" name="username"
                               value="{{ session('open_modal') === 'login' ? old('username') : '' }}" required>
                        @if(session('open_modal') === 'login' && $errors->has('username'))
                            <div class="invalid-feedback">{{ $errors->first('username') }}</div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="login-password" class="form-label">Password</label>
                        <input type="password" class="form-control @if(session('open_modal')==='login' && $errors->has('password')) is-invalid @endif"
                               id="login-password" name="password" required>
                        @if(session('open_modal') === 'login' && $errors->has('password'))
                            <div class="invalid-feedback">{{ $errors->first('password') }}</div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <p class="me-auto text-sm">Non hai un account? <a href="#" onclick="switchToRegister()">Registrati qui</a></p>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-primary">Accedi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Registrazione -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registerModalLabel">Registrati</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="registerForm" method="POST" action="{{ route('auth.register') }}">
                @csrf
                <div class="modal-body">
                    @if(session('open_modal') === 'register' && $errors->any())
                        <div class="alert alert-danger py-2 mb-3">
                            <ul class="mb-0 ps-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label for="register-username" class="form-label">Username <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @if(session('open_modal')==='register' && $errors->has('username')) is-invalid @endif"
                               id="register-username" name="username"
                               value="{{ session('open_modal') === 'register' ? old('username') : '' }}" required>
                        @if(session('open_modal') === 'register' && $errors->has('username'))
                            <div class="invalid-feedback">{{ $errors->first('username') }}</div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="register-email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @if(session('open_modal')==='register' && $errors->has('email')) is-invalid @endif"
                               id="register-email" name="email"
                               value="{{ session('open_modal') === 'register' ? old('email') : '' }}" required>
                        @if(session('open_modal') === 'register' && $errors->has('email'))
                            <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="register-password" class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @if(session('open_modal')==='register' && $errors->has('password')) is-invalid @endif"
                               id="register-password" name="password" required>
                        @if(session('open_modal') === 'register' && $errors->has('password'))
                            <div class="invalid-feedback">{{ $errors->first('password') }}</div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="register-password-confirm" class="form-label">Conferma Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="register-password-confirm" name="password_confirmation" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <p class="me-auto text-sm">Hai già un account? <a href="#" onclick="switchToLogin()">Accedi qui</a></p>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-primary">Registrati</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openLoginModal() {
    const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
    loginModal.show();
}

function openRegisterModal() {
    const registerModal = new bootstrap.Modal(document.getElementById('registerModal'));
    registerModal.show();
}

function switchToLogin() {
    const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
    const registerModal = bootstrap.Modal.getInstance(document.getElementById('registerModal'));
    if (registerModal) registerModal.hide();
    loginModal.show();
}

function switchToRegister() {
    const registerModal = new bootstrap.Modal(document.getElementById('registerModal'));
    const loginModal = bootstrap.Modal.getInstance(document.getElementById('loginModal'));
    if (loginModal) loginModal.hide();
    registerModal.show();
}

// Auto-open modal if server returned validation errors
@if(session('open_modal') === 'login')
document.addEventListener('DOMContentLoaded', function () {
    openLoginModal();
});
@elseif(session('open_modal') === 'register')
document.addEventListener('DOMContentLoaded', function () {
    openRegisterModal();
});
@endif
</script>

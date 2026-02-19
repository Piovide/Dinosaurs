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
                    <div class="mb-3">
                        <label for="login-username" class="form-label">Username o Email</label>
                        <input type="text" class="form-control" id="login-username" name="username" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="login-password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="login-password" name="password" required>
                        <div class="invalid-feedback"></div>
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
                    <div class="mb-3">
                        <label for="register-nome" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="register-nome" name="nome" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="register-cognome" class="form-label">Cognome</label>
                        <input type="text" class="form-control" id="register-cognome" name="cognome" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="register-username" class="form-label">Username <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="register-username" name="username" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="register-email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="register-email" name="email" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="register-password" class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="register-password" name="password" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="register-password-confirm" class="form-label">Conferma Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="register-password-confirm" name="password_confirmation" required>
                        <div class="invalid-feedback"></div>
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
</script>

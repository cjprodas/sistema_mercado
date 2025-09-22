<!-- Formulario de Login -->
<form id="loginForm" class="needs-validation" novalidate>
    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

    <!-- Campo Email -->
    <div class="mb-3">
        <label for="email" class="form-label">
            <i class="bi bi-envelope me-2"></i>Correo Electrónico
        </label>
        <input type="email" class="form-control" id="email" name="email" placeholder="Ingresa tu correo electrónico"
            required autocomplete="email" value="">
        <div class="invalid-feedback">
            Por favor ingresa un correo electrónico válido.
        </div>
    </div>

    <!-- Campo Contraseña -->
    <div class="mb-3">
        <label for="password" class="form-label">
            <i class="bi bi-lock me-2"></i>Contraseña
        </label>
        <div class="input-group">
            <input type="password" class="form-control" id="password" name="password"
                placeholder="Ingresa tu contraseña" required autocomplete="current-password" minlength="6">
            <button type="button" class="password-toggle" onclick="togglePassword('password')" tabindex="-1">
                <i class="bi bi-eye"></i>
            </button>
        </div>
        <div class="invalid-feedback">
            La contraseña debe tener al menos 6 caracteres.
        </div>
    </div>

    <!-- Recordar sesión -->
    <div class="mb-4">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="remember" name="remember" value="1">
            <label class="form-check-label" for="remember">
                Recordarme en este dispositivo
            </label>
        </div>
    </div>

    <!-- Botón de login -->
    <div class="d-grid mb-3">
        <button type="submit" class="btn btn-primary btn-lg" id="loginBtn">
            <span class="btn-text">
                <i class="bi bi-box-arrow-in-right me-2"></i>
                Iniciar Sesión
            </span>
            <span class="btn-loading d-none">
                <span class="loading-spinner"></span>
                Iniciando sesión...
            </span>
        </button>
    </div>
</form>

<!-- Información del sistema -->
<div class="text-center mt-4">
    <small class="text-muted">
        <i class="bi bi-shield-check me-1"></i>
        Acceso seguro al sistema
    </small>
    <br>
    <small class="text-muted">
        Versión <?= $appVersion ?>
    </small>
</div>

<!-- Credenciales de prueba (solo en desarrollo) -->
<?php if (ENVIRONMENT === 'development'): ?>
    <div class="mt-4 p-3"
        style="background-color: rgba(25, 135, 84, 0.1); border-radius: 8px; border-left: 4px solid #198754;">
        <h6 class="text-success mb-2">
            <i class="bi bi-info-circle me-2"></i>
            Credenciales de Prueba
        </h6>
        <small class="text-muted d-block mb-1">
            <strong>Administrador:</strong> admin@mercado.com / admin123
        </small>
        <small class="text-muted d-block">
            <strong>Usuario:</strong> Crear desde el administrador
        </small>
    </div>
<?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const loginForm = document.getElementById('loginForm');
        const loginBtn = document.getElementById('loginBtn');
        const btnText = loginBtn.querySelector('.btn-text');
        const btnLoading = loginBtn.querySelector('.btn-loading');

        // Manejar envío del formulario
        loginForm.addEventListener('submit', function (e) {
            e.preventDefault();

            // Validar formulario
            if (!loginForm.checkValidity()) {
                loginForm.classList.add('was-validated');
                return;
            }

            // Mostrar estado de carga
            btnText.classList.add('d-none');
            btnLoading.classList.remove('d-none');
            loginBtn.disabled = true;

            // Obtener datos del formulario
            const formData = new FormData(loginForm);

            // Enviar petición de login
            fetch(window.App.baseUrl + 'process-login', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': window.App.csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Login exitoso
                        Swal.fire({
                            icon: 'success',
                            title: '¡Bienvenido!',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.href = data.redirect || window.App.baseUrl + 'dashboard';
                        });
                    } else {
                        // Error en login
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de Acceso',
                            text: data.message,
                            confirmButtonColor: '#667eea'
                        });

                        // Resetear botón
                        resetLoginButton();

                        // Limpiar contraseña
                        document.getElementById('password').value = '';
                        document.getElementById('password').focus();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);

                    Swal.fire({
                        icon: 'error',
                        title: 'Error de Conexión',
                        text: 'No se pudo conectar con el servidor. Por favor, intenta nuevamente.',
                        confirmButtonColor: '#667eea'
                    });

                    resetLoginButton();
                });
        });

        // Función para resetear el botón de login
        function resetLoginButton() {
            btnText.classList.remove('d-none');
            btnLoading.classList.add('d-none');
            loginBtn.disabled = false;
        }

        // Auto-focus en el primer campo
        document.getElementById('email').focus();

        // Manejar Enter en los campos
        loginForm.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                loginForm.dispatchEvent(new Event('submit'));
            }
        });

        // Validación en tiempo real
        const inputs = loginForm.querySelectorAll('input[required]');
        inputs.forEach(input => {
            input.addEventListener('blur', function () {
                if (this.checkValidity()) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else {
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                }
            });

            input.addEventListener('input', function () {
                if (this.classList.contains('is-invalid') && this.checkValidity()) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                }
            });
        });

        // Prellenar campos en desarrollo
        <?php if (ENVIRONMENT === 'development'): ?>
            document.getElementById('email').value = 'admin@mercado.com';
            document.getElementById('password').value = 'admin123';
        <?php endif; ?>
    });

    // Función global para toggle de contraseña
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const icon = input.parentNode.querySelector('.password-toggle i');

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }
</script>
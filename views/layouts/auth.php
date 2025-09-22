<!DOCTYPE html>
<html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Sistema de Administración de Mercado Municipal - Acceso">
        <meta name="author" content="Sistema Mercado">
        <meta name="csrf-token" content="<?= $csrfToken ?>">

        <title><?= isset($pageTitle) ? $pageTitle . ' - ' : '' ?><?= $appName ?></title>

        <!-- Bootstrap 5 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

        <!-- Bootstrap Icons -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

        <!-- SweetAlert2 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.9.0/dist/sweetalert2.min.css" rel="stylesheet">

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="<?= asset('images/favicon.ico') ?>">

        <style>
            :root {
                --primary-color: #667eea;
                --secondary-color: #764ba2;
                --accent-color: #f093fb;
                --text-dark: #2d3748;
                --text-light: #718096;
            }

            body {
                background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 50%, var(--accent-color) 100%);
                min-height: 100vh;
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            }

            .auth-container {
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 2rem 1rem;
            }

            .auth-card {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border-radius: 20px;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
                border: 1px solid rgba(255, 255, 255, 0.2);
                overflow: hidden;
                max-width: 450px;
                width: 100%;
                transition: transform 0.3s ease;
            }

            .auth-card:hover {
                transform: translateY(-5px);
            }

            .auth-header {
                background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
                color: white;
                padding: 2.5rem 2rem 2rem;
                text-align: center;
                position: relative;
            }

            .auth-header::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 20"><defs><pattern id="grain" width="100" height="20" patternUnits="userSpaceOnUse"><circle cx="2" cy="2" r="1" fill="white" opacity="0.1"/><circle cx="12" cy="8" r="1" fill="white" opacity="0.05"/><circle cx="25" cy="15" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="20" fill="url(%23grain)"/></svg>');
                opacity: 0.3;
            }

            .auth-logo {
                width: 80px;
                height: 80px;
                background: rgba(255, 255, 255, 0.2);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 1rem;
                position: relative;
                z-index: 1;
            }

            .auth-logo i {
                font-size: 2.5rem;
                color: white;
            }

            .auth-title {
                font-size: 1.8rem;
                font-weight: 700;
                margin-bottom: 0.5rem;
                position: relative;
                z-index: 1;
            }

            .auth-subtitle {
                opacity: 0.9;
                font-size: 0.95rem;
                position: relative;
                z-index: 1;
            }

            .auth-body {
                padding: 2.5rem 2rem;
            }

            .form-control {
                border: 2px solid #e2e8f0;
                border-radius: 12px;
                padding: 0.75rem 1rem;
                font-size: 1rem;
                transition: all 0.3s ease;
                background-color: #f8fafc;
            }

            .form-control:focus {
                border-color: var(--primary-color);
                box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
                background-color: white;
            }

            .form-label {
                font-weight: 600;
                color: var(--text-dark);
                margin-bottom: 0.5rem;
            }

            .btn-primary {
                background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
                border: none;
                border-radius: 12px;
                padding: 0.75rem 2rem;
                font-weight: 600;
                font-size: 1rem;
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
            }

            .btn-primary::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
                transition: left 0.5s;
            }

            .btn-primary:hover::before {
                left: 100%;
            }

            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
            }

            .form-check-input:checked {
                background-color: var(--primary-color);
                border-color: var(--primary-color);
            }

            .form-check-label {
                color: var(--text-light);
                font-size: 0.9rem;
            }

            .alert {
                border-radius: 12px;
                border: none;
                padding: 1rem 1.25rem;
            }

            .alert-danger {
                background-color: rgba(220, 53, 69, 0.1);
                color: #dc3545;
            }

            .alert-success {
                background-color: rgba(25, 135, 84, 0.1);
                color: #198754;
            }

            .loading-spinner {
                border: 3px solid rgba(255, 255, 255, 0.3);
                border-top: 3px solid white;
                border-radius: 50%;
                width: 20px;
                height: 20px;
                animation: spin 1s linear infinite;
                display: inline-block;
                margin-right: 0.5rem;
            }

            @keyframes spin {
                0% {
                    transform: rotate(0deg);
                }

                100% {
                    transform: rotate(360deg);
                }
            }

            .floating-elements {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                pointer-events: none;
                z-index: -1;
            }

            .floating-elements::before,
            .floating-elements::after {
                content: '';
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.1);
                animation: float 6s ease-in-out infinite;
            }

            .floating-elements::before {
                width: 200px;
                height: 200px;
                top: 10%;
                left: 10%;
                animation-delay: 0s;
            }

            .floating-elements::after {
                width: 150px;
                height: 150px;
                bottom: 20%;
                right: 15%;
                animation-delay: 3s;
            }

            @keyframes float {

                0%,
                100% {
                    transform: translateY(0) rotate(0deg);
                }

                50% {
                    transform: translateY(-20px) rotate(180deg);
                }
            }

            .password-toggle {
                position: absolute;
                right: 12px;
                top: 50%;
                transform: translateY(-50%);
                background: none;
                border: none;
                color: var(--text-light);
                cursor: pointer;
                z-index: 10;
            }

            .password-toggle:hover {
                color: var(--primary-color);
            }

            .input-group {
                position: relative;
            }

            @media (max-width: 576px) {
                .auth-container {
                    padding: 1rem 0.5rem;
                }

                .auth-card {
                    margin: 0;
                    border-radius: 15px;
                }

                .auth-header {
                    padding: 2rem 1.5rem 1.5rem;
                }

                .auth-body {
                    padding: 2rem 1.5rem;
                }

                .auth-title {
                    font-size: 1.5rem;
                }
            }
        </style>
    </head>

    <body>
        <!-- Elementos flotantes de fondo -->
        <div class="floating-elements"></div>

        <div class="auth-container">
            <div class="auth-card">
                <div class="auth-header">
                    <div class="auth-logo">
                        <i class="bi bi-shop"></i>
                    </div>
                    <h1 class="auth-title"><?= $appName ?></h1>
                    <p class="auth-subtitle">Sistema de Administración</p>
                </div>

                <div class="auth-body">
                    <!-- Contenido específico de la vista -->
                    <?php
                    if (isset($viewFile) && !empty($viewFile)) {
                        include ROOT_PATH . 'views/' . $viewFile . '.php';
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- Scripts -->
        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

        <!-- Bootstrap 5 JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

        <!-- SweetAlert2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.9.0/dist/sweetalert2.all.min.js"></script>

        <script>
            // Configuración global
            window.App = {
                baseUrl: '<?= $baseUrl ?>',
                csrfToken: '<?= $csrfToken ?>'
            };

            // Configuración de AJAX global
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': window.App.csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            // Funcionalidad para mostrar/ocultar contraseña
            function togglePassword(inputId) {
                const input = document.getElementById(inputId);
                const icon = input.nextElementSibling.querySelector('i');

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

            // Validación en tiempo real
            function setupFormValidation() {
                const forms = document.querySelectorAll('.needs-validation');

                Array.from(forms).forEach(form => {
                    form.addEventListener('submit', event => {
                        if (!form.checkValidity()) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }

            // Inicializar cuando el documento esté listo
            document.addEventListener('DOMContentLoaded', function () {
                setupFormValidation();
            });
        </script>

        <!-- Scripts específicos de la página -->
        <?php if (isset($pageScripts)): ?>
            <?= $pageScripts ?>
        <?php endif; ?>
    </body>

</html>
<?php
// Archivo: views/layouts/main.php
// Verificar que las variables necesarias existen
if (!isset($router)) {
    $router = new Router();
}

if (!isset($currentUser) || empty($currentUser)) {
    header('Location: ' . BASE_URL . 'login');
    exit;
}

// Obtener permisos del usuario
$permisos = json_decode($currentUser['rol_permisos'] ?? '{}', true);
if (!$permisos) {
    $permisos = [];
}
?>
<!DOCTYPE html>
<html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Sistema de Administración de Mercado Municipal">
        <meta name="author" content="Sistema Mercado">
        <meta name="csrf-token" content="<?= $csrfToken ?? '' ?>">

        <title><?= isset($pageTitle) ? $pageTitle . ' - ' : '' ?><?= $appName ?? 'Sistema Mercado' ?></title>

        <!-- Bootstrap 5 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

        <!-- Bootstrap Icons -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

        <!-- DataTables CSS -->
        <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">

        <!-- SweetAlert2 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.9.0/dist/sweetalert2.min.css" rel="stylesheet">

        <!-- Custom CSS -->
        <link href="<?= BASE_URL ?>assets/css/style.css" rel="stylesheet">

        <style>
            :root {
                --primary-color: #0d6efd;
                --secondary-color: #6c757d;
                --success-color: #198754;
                --danger-color: #dc3545;
                --warning-color: #ffc107;
                --info-color: #0dcaf0;
            }

            .sidebar {
                min-height: 100vh;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            }

            .sidebar .nav-link {
                color: rgba(255, 255, 255, 0.9);
                border-radius: 8px;
                margin: 2px 0;
                transition: all 0.3s ease;
            }

            .sidebar .nav-link:hover,
            .sidebar .nav-link.active {
                background-color: rgba(255, 255, 255, 0.2);
                color: #fff;
                transform: translateX(5px);
            }

            .main-content {
                background-color: #f8f9fa;
                min-height: 100vh;
            }

            .navbar-custom {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }

            .card {
                border: none;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                border-radius: 12px;
            }

            .btn {
                border-radius: 8px;
                font-weight: 500;
            }

            @media (max-width: 768px) {
                .sidebar {
                    position: fixed;
                    top: 0;
                    left: -250px;
                    width: 250px;
                    height: 100vh;
                    z-index: 1050;
                    transition: left 0.3s ease;
                }

                .sidebar.show {
                    left: 0;
                }

                .main-content {
                    margin-left: 0;
                }
            }
        </style>
    </head>

    <body>
        <div class="container-fluid">
            <div class="row">
                <!-- Sidebar -->
                <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse" id="sidebar">
                    <div class="position-sticky pt-3">
                        <!-- Logo y título -->
                        <div class="text-center mb-4">
                            <i class="bi bi-shop text-white" style="font-size: 3rem;"></i>
                            <h6 class="text-white fw-bold"><?= $mercadoNombre ?? 'Sistema Mercado' ?></h6>
                            <small class="text-white-50"><?= $mercadoDireccion ?? '' ?></small>
                        </div>

                        <!-- Información del usuario -->
                        <div class="text-center mb-4 p-3"
                            style="background-color: rgba(255,255,255,0.1); border-radius: 10px;">
                            <i class="bi bi-person-circle text-white" style="font-size: 50px;"></i>
                            <div class="text-white">
                                <small
                                    class="d-block fw-bold"><?= htmlspecialchars($currentUser['nombre'] ?? '') ?></small>
                                <small
                                    class="text-white-50"><?= htmlspecialchars($currentUser['rol_nombre'] ?? '') ?></small>
                            </div>
                        </div>

                        <!-- Menú de navegación -->
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link <?= ($router->getCurrentRoute() === 'dashboard') ? 'active' : '' ?>"
                                    href="<?= BASE_URL ?>dashboard">
                                    <i class="bi bi-speedometer2 me-2"></i>
                                    Dashboard
                                </a>
                            </li>

                            <?php if (isset($permisos['inquilinos']) && $permisos['inquilinos'] !== 'none'): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= (strpos($router->getCurrentRoute(), 'inquilinos') === 0) ? 'active' : '' ?>"
                                        href="<?= BASE_URL ?>inquilinos">
                                        <i class="bi bi-people me-2"></i>
                                        Inquilinos
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if (isset($permisos['pagos']) && $permisos['pagos'] !== 'none'): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= (strpos($router->getCurrentRoute(), 'pagos') === 0) ? 'active' : '' ?>"
                                        href="<?= BASE_URL ?>pagos">
                                        <i class="bi bi-cash-coin me-2"></i>
                                        Pagos
                                    </a>
                                </li>
                            <?php endif; ?>

                            <li class="nav-item">
                                <a class="nav-link <?= (strpos($router->getCurrentRoute(), 'estados-cuenta') === 0) ? 'active' : '' ?>"
                                    href="<?= BASE_URL ?>estados-cuenta">
                                    <i class="bi bi-file-earmark-text me-2"></i>
                                    Estados de Cuenta
                                </a>
                            </li>

                            <?php if (isset($permisos['reportes']) && $permisos['reportes'] !== 'none'): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= (strpos($router->getCurrentRoute(), 'reportes') === 0) ? 'active' : '' ?>"
                                        href="<?= BASE_URL ?>reportes">
                                        <i class="bi bi-graph-up me-2"></i>
                                        Reportes
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if (isset($permisos['usuarios']) && $permisos['usuarios'] === 'all'): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= (strpos($router->getCurrentRoute(), 'usuarios') === 0) ? 'active' : '' ?>"
                                        href="<?= BASE_URL ?>usuarios">
                                        <i class="bi bi-person-gear me-2"></i>
                                        Usuarios
                                    </a>
                                </li>
                            <?php endif; ?>

                            <hr class="text-white-50">

                            <li class="nav-item">
                                <a class="nav-link" href="<?= BASE_URL ?>logout"
                                    onclick="return confirm('¿Estás seguro que deseas cerrar sesión?')">
                                    <i class="bi bi-box-arrow-right me-2"></i>
                                    Cerrar Sesión
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>

                <!-- Contenido Principal -->
                <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                    <!-- Navbar superior -->
                    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom mb-4">
                        <div class="container-fluid">
                            <button class="btn btn-outline-light d-md-none me-2" type="button" id="sidebarToggle">
                                <i class="bi bi-list"></i>
                            </button>

                            <span class="navbar-brand mb-0 h1">
                                <?= isset($pageTitle) ? $pageTitle : 'Dashboard' ?>
                            </span>

                            <div class="navbar-nav ms-auto">
                                <div class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#"
                                        id="userDropdown" role="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-person-circle me-2" style="font-size: 32px;"></i>
                                        <span
                                            class="d-none d-lg-inline"><?= htmlspecialchars($currentUser['nombre'] ?? '') ?></span>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="<?= BASE_URL ?>usuarios/profile">
                                                <i class="bi bi-person me-2"></i>Mi Perfil
                                            </a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item" href="<?= BASE_URL ?>logout"
                                                onclick="return confirm('¿Estás seguro que deseas cerrar sesión?')">
                                                <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
                                            </a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </nav>

                    <!-- Mensajes Flash -->
                    <?php
                    $flashMessages = $_SESSION['flash_messages'] ?? [];
                    unset($_SESSION['flash_messages']);
                    ?>
                    <?php foreach ($flashMessages as $message): ?>
                        <div
                            class="alert alert-<?= $message['type'] === 'error' ? 'danger' : $message['type'] ?> alert-dismissible fade show">
                            <?= htmlspecialchars($message['message']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endforeach; ?>

                    <!-- Contenido de la página -->
                    <div class="content-wrapper">
                        <?php
                        if (isset($viewFile) && !empty($viewFile)) {
                            $viewPath = ROOT_PATH . 'views/' . $viewFile . '.php';
                            if (file_exists($viewPath)) {
                                include $viewPath;
                            } else {
                                echo '<div class="alert alert-warning">Vista no encontrada: ' . htmlspecialchars($viewFile) . '</div>';
                            }
                        }
                        ?>
                    </div>
                </main>
            </div>
        </div>

        <!-- Scripts -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.9.0/dist/sweetalert2.all.min.js"></script>

        <script>
            // Configuración global
            window.App = {
                baseUrl: '<?= BASE_URL ?>',
                csrfToken: '<?= $csrfToken ?? '' ?>'
            };

            // Toggle sidebar en móviles
            document.getElementById('sidebarToggle')?.addEventListener('click', function () {
                document.getElementById('sidebar').classList.toggle('show');
            });

            // Configurar AJAX
            if (typeof $ !== 'undefined') {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': window.App.csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
            }
        </script>

        <?php if (isset($pageScripts)): ?>
            <?= $pageScripts ?>
        <?php endif; ?>
    </body>

</html>
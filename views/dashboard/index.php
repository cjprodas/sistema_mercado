<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">
                <i class="bi bi-speedometer2 me-2"></i>
                Dashboard Principal
            </h1>
            <div class="text-muted">
                <i class="bi bi-calendar-event me-1"></i>
                <?= date('d/m/Y H:i') ?>
            </div>
        </div>
    </div>
</div>

<!-- Tarjetas de Estadísticas -->
<div class="row mb-4">
    <!-- Inquilinos Activos -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start border-primary border-4 shadow h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                            Inquilinos Activos
                        </div>
                        <div class="h5 mb-0 fw-bold text-gray-800">
                            <?= $totalInquilinos ?? 0 ?>
                        </div>
                        <div class="text-muted small">
                            <i class="bi bi-arrow-up text-success"></i>
                            Total registrados
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-people text-primary" style="font-size: 2.5rem; opacity: 0.8;"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent">
                <a href="<?= BASE_URL ?>inquilinos" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-eye me-1"></i>Ver Todos
                </a>
            </div>
        </div>
    </div>

    <!-- Usuarios del Sistema -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start border-success border-4 shadow h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-success text-uppercase mb-1">
                            Usuarios del Sistema
                        </div>
                        <div class="h5 mb-0 fw-bold text-gray-800">
                            <?= $totalUsuarios ?? 1 ?>
                        </div>
                        <div class="text-muted small">
                            <i class="bi bi-shield-check text-success"></i>
                            Con acceso
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-person-gear text-success" style="font-size: 2.5rem; opacity: 0.8;"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent">
                <?php if (isset($permisos['usuarios']) && $permisos['usuarios'] === 'all'): ?>
                    <a href="<?= BASE_URL ?>usuarios" class="btn btn-sm btn-outline-success">
                        <i class="bi bi-gear me-1"></i>Administrar
                    </a>
                <?php else: ?>
                    <span class="text-muted small">Sin permisos</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Pagos del Mes -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start border-info border-4 shadow h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-info text-uppercase mb-1">
                            Pagos del Mes
                        </div>
                        <div class="h5 mb-0 fw-bold text-gray-800">
                            Q. 0.00
                        </div>
                        <div class="text-muted small">
                            <i class="bi bi-calendar-month text-info"></i>
                            <?= date('F Y') ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-cash-coin text-info" style="font-size: 2.5rem; opacity: 0.8;"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent">
                <a href="<?= BASE_URL ?>pagos" class="btn btn-sm btn-outline-info">
                    <i class="bi bi-plus-circle me-1"></i>Registrar Pago
                </a>
            </div>
        </div>
    </div>

    <!-- Estados de Cuenta -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start border-warning border-4 shadow h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-warning text-uppercase mb-1">
                            Estados Pendientes
                        </div>
                        <div class="h5 mb-0 fw-bold text-gray-800">
                            0
                        </div>
                        <div class="text-muted small">
                            <i class="bi bi-exclamation-triangle text-warning"></i>
                            Por cobrar
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-file-earmark-text text-warning" style="font-size: 2.5rem; opacity: 0.8;"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent">
                <a href="<?= BASE_URL ?>estados-cuenta" class="btn btn-sm btn-outline-warning">
                    <i class="bi bi-file-earmark-plus me-1"></i>Generar Estados
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Sección de Acciones Rápidas -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-lightning-charge me-2"></i>
                    Acciones Rápidas
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="d-grid">
                            <a href="<?= BASE_URL ?>inquilinos/create" class="btn btn-outline-primary">
                                <i class="bi bi-person-plus-fill mb-2 d-block" style="font-size: 1.5rem;"></i>
                                Nuevo Inquilino
                            </a>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="d-grid">
                            <a href="<?= BASE_URL ?>pagos/create" class="btn btn-outline-success">
                                <i class="bi bi-cash-stack mb-2 d-block" style="font-size: 1.5rem;"></i>
                                Registrar Pago
                            </a>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="d-grid">
                            <a href="<?= BASE_URL ?>reportes" class="btn btn-outline-info">
                                <i class="bi bi-graph-up-arrow mb-2 d-block" style="font-size: 1.5rem;"></i>
                                Ver Reportes
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Información del Sistema
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Mercado:</strong><br>
                    <span class="text-muted"><?= $mercadoNombre ?? 'Sistema de Mercado' ?></span>
                </div>
                <div class="mb-3">
                    <strong>Usuario Actual:</strong><br>
                    <span class="text-muted"><?= htmlspecialchars($currentUser['nombre'] ?? '') ?></span><br>
                    <small class="text-primary"><?= htmlspecialchars($currentUser['rol_nombre'] ?? '') ?></small>
                </div>
                <div class="mb-3">
                    <strong>Último Acceso:</strong><br>
                    <span class="text-muted"><?= date('d/m/Y H:i') ?></span>
                </div>
                <div>
                    <strong>Versión:</strong><br>
                    <span class="text-muted"><?= $appVersion ?? '1.0.0' ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sección de Actividad Reciente -->
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header bg-secondary text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-clock-history me-2"></i>
                    Actividad Reciente
                </h5>
            </div>
            <div class="card-body">
                <div class="text-center py-4">
                    <i class="bi bi-journal-check text-muted" style="font-size: 3rem;"></i>
                    <h6 class="text-muted mt-3">¡Bienvenido al Sistema!</h6>
                    <p class="text-muted">
                        Comenzá registrando inquilinos y generando estados de cuenta para ver la actividad aquí.
                    </p>
                    <div class="mt-3">
                        <a href="<?= BASE_URL ?>inquilinos/create" class="btn btn-primary me-2">
                            <i class="bi bi-plus-circle me-1"></i>Registrar Primer Inquilino
                        </a>
                        <a href="<?= BASE_URL ?>configuracion" class="btn btn-outline-secondary">
                            <i class="bi bi-gear me-1"></i>Configurar Sistema
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts específicos del dashboard -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Actualizar hora cada minuto
        setInterval(function () {
            const now = new Date();
            const timeString = now.toLocaleDateString('es-GT') + ' ' +
                now.toLocaleTimeString('es-GT', { hour12: false });
            document.querySelector('.text-muted .bi-calendar-event').parentNode.innerHTML =
                '<i class="bi bi-calendar-event me-1"></i>' + timeString;
        }, 60000);

        // Animar las tarjetas al cargar
        const cards = document.querySelectorAll('.card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';

            setTimeout(() => {
                card.style.transition = 'all 0.5s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
    });
</script>

<style>
    .border-4 {
        border-width: 4px !important;
    }

    .card {
        transition: transform 0.2s ease-in-out;
    }

    .card:hover {
        transform: translateY(-2px);
    }

    .text-xs {
        font-size: 0.7rem;
    }

    .text-gray-800 {
        color: #343a40 !important;
    }

    @media (max-width: 768px) {
        .h5 {
            font-size: 1.1rem;
        }

        .card-body {
            padding: 1rem;
        }
    }
</style>
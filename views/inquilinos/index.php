<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">
                <i class="bi bi-people me-2"></i>
                Gestión de Inquilinos
            </h1>
            <?php if ($currentUser && json_decode($currentUser['rol_permisos'], true)['inquilinos'] !== 'read'): ?>
                <a href="<?= BASE_URL ?>inquilinos/create" class="btn btn-primary">
                    <i class="bi bi-person-plus me-2"></i>
                    Nuevo Inquilino
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Filtros y Búsqueda -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-body">
                <form method="GET" id="searchForm" class="row g-3">
                    <div class="col-md-8">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" id="searchInput"
                                placeholder="Buscar por nombre, DPI, teléfono o código comercial..."
                                value="<?= e($search ?? '') ?>">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="estado" id="filtroEstado">
                            <option value="">Todos los estados</option>
                            <option value="activo">Activos</option>
                            <option value="inactivo">Inactivos</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="d-grid">
                            <button type="button" class="btn btn-outline-warning" onclick="limpiarFiltros()">
                                <i class="bi bi-arrow-clockwise me-1"></i>Limpiar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de Inquilinos -->
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-list-ul me-2"></i>
                    Lista de Inquilinos
                    <small class="ms-2">(<?= count($inquilinos ?? []) ?> registros)</small>
                </h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($inquilinos)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-person-x text-muted" style="font-size: 4rem;"></i>
                        <h5 class="text-muted mt-3">No hay inquilinos registrados</h5>
                        <p class="text-muted">
                            <?php if (isset($search) && !empty($search)): ?>
                                No se encontraron resultados para "<?= e($search) ?>"
                            <?php else: ?>
                                Comenzá registrando el primer inquilino
                            <?php endif; ?>
                        </p>
                        <?php if ($currentUser && json_decode($currentUser['rol_permisos'], true)['inquilinos'] !== 'read'): ?>
                            <a href="<?= BASE_URL ?>inquilinos/create" class="btn btn-primary mt-3">
                                <i class="bi bi-person-plus me-2"></i>
                                Registrar Primer Inquilino
                            </a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="inquilinosTable">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Foto</th>
                                    <th>Información Personal</th>
                                    <th>Datos Comerciales</th>
                                    <th>Servicios</th>
                                    <th>Estado</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($inquilinos as $index => $inquilino): ?>
                                    <tr>
                                        <td class="align-middle">
                                            <span class="badge bg-secondary"><?= $inquilino['id'] ?></span>
                                        </td>
                                        <td class="align-middle">
                                            <?php if (!empty($inquilino['foto_propietario'])): ?>
                                                <img src="<?= BASE_URL ?>storage/uploads/inquilinos/<?= $inquilino['foto_propietario'] ?>"
                                                    class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;"
                                                    alt="Foto">
                                            <?php else: ?>
                                                <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center"
                                                    style="width: 50px; height: 50px;">
                                                    <i class="bi bi-person text-white"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="align-middle">
                                            <div>
                                                <strong><?= e($inquilino['nombre_completo']) ?></strong>
                                                <?php if ($inquilino['sexo'] === 'M'): ?>
                                                    <i class="bi bi-gender-male text-info ms-1"></i>
                                                <?php else: ?>
                                                    <i class="bi bi-gender-female text-danger ms-1"></i>
                                                <?php endif; ?>
                                            </div>
                                            <small class="text-muted">
                                                <i class="bi bi-credit-card me-1"></i>DPI: <?= e($inquilino['dpi']) ?>
                                            </small><br>
                                            <small class="text-muted">
                                                <i class="bi bi-telephone me-1"></i><?= e($inquilino['telefono']) ?>
                                            </small>
                                            <?php if (!empty($inquilino['email'])): ?>
                                                <br><small class="text-muted">
                                                    <i class="bi bi-envelope me-1"></i><?= e($inquilino['email']) ?>
                                                </small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="align-middle">
                                            <?php if (!empty($inquilino['codigo_comercial'])): ?>
                                                <div>
                                                    <span
                                                        class="badge bg-primary mb-1"><?= e($inquilino['codigo_comercial'] ?? 'N/A') ?></span>
                                                </div>
                                                <small class="text-muted">
                                                    <i class="bi bi-building me-1"></i><?= e($inquilino['nivel'] ?? 'N/A') ?>
                                                </small><br>
                                                <small class="text-muted">
                                                    <i class="bi bi-shop me-1"></i><?= e($inquilino['tipo_venta'] ?? 'N/A') ?>
                                                </small><br>
                                                <?php if (!empty($inquilino['metros_cuadrados'])): ?>
                                                    <small class="text-muted">
                                                        <i
                                                            class="bi bi-aspect-ratio me-1"></i><?= number_format($inquilino['metros_cuadrados'], 2) ?>
                                                        m²
                                                    </small>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted">Sin datos comerciales</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="align-middle">
                                            <?php if (!empty($inquilino['total_mensual']) && $inquilino['total_mensual'] > 0): ?>
                                                <div class="small">
                                                    <?php if (!empty($inquilino['renta_mensual']) && $inquilino['renta_mensual'] > 0): ?>
                                                        <div class="text-muted">
                                                            <i class="bi bi-house me-1"></i>Renta:
                                                            <?= money($inquilino['renta_mensual']) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    <?php if (!empty($inquilino['energia_mensual']) && $inquilino['energia_mensual'] > 0): ?>
                                                        <div class="text-muted">
                                                            <i class="bi bi-lightning me-1"></i>Energía:
                                                            <?= money($inquilino['energia_mensual']) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    <?php if (!empty($inquilino['agua_mensual']) && $inquilino['agua_mensual'] > 0): ?>
                                                        <div class="text-muted">
                                                            <i class="bi bi-droplet me-1"></i>Agua:
                                                            <?= money($inquilino['agua_mensual']) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    <?php if (!empty($inquilino['basura_mensual']) && $inquilino['basura_mensual'] > 0): ?>
                                                        <div class="text-muted">
                                                            <i class="bi bi-trash me-1"></i>Basura:
                                                            <?= money($inquilino['basura_mensual']) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    <hr class="my-1">
                                                    <strong class="text-primary">
                                                        Total: <?= money($inquilino['total_mensual']) ?>
                                                    </strong>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted">Sin servicios</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="align-middle">
                                            <?php if ($inquilino['estado'] === 'activo'): ?>
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle me-1"></i>Activo
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">
                                                    <i class="bi bi-x-circle me-1"></i>Inactivo
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="align-middle text-center">
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle"
                                                    data-bs-toggle="dropdown">
                                                    <i class="bi bi-gear"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="<?= BASE_URL ?>inquilinos/view/<?= $inquilino['id'] ?>">
                                                            <i class="bi bi-eye me-2"></i>Ver Detalles
                                                        </a>
                                                    </li>
                                                    <?php if ($currentUser && json_decode($currentUser['rol_permisos'], true)['inquilinos'] !== 'read'): ?>
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="<?= BASE_URL ?>inquilinos/edit/<?= $inquilino['id'] ?>">
                                                                <i class="bi bi-pencil me-2"></i>Editar
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="<?= BASE_URL ?>estados-cuenta?inquilino=<?= $inquilino['id'] ?>">
                                                                <i class="bi bi-file-earmark-text me-2"></i>Estado de Cuenta
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="<?= BASE_URL ?>pagos/create?inquilino=<?= $inquilino['id'] ?>">
                                                                <i class="bi bi-cash me-2"></i>Registrar Pago
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item text-danger" href="#"
                                                                onclick="confirmarEliminacion(<?= $inquilino['id'] ?>, '<?= e($inquilino['nombre_completo']) ?>')">
                                                                <i class="bi bi-trash me-2"></i>Desactivar
                                                            </a>
                                                        </li>
                                                    <?php endif; ?>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <?php if (isset($pagination) && $pagination['total_pages'] > 1): ?>
                        <div class="card-footer">
                            <nav aria-label="Paginación de inquilinos">
                                <ul class="pagination justify-content-center mb-0">
                                    <?php if ($pagination['has_previous']): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?= $pagination['previous_page'] ?>">
                                                <i class="bi bi-chevron-left"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                                        <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                        </li>
                                    <?php endfor; ?>

                                    <?php if ($pagination['has_next']): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?= $pagination['next_page'] ?>">
                                                <i class="bi bi-chevron-right"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>

                            <div class="text-center mt-2">
                                <small class="text-muted">
                                    Página <?= $pagination['current_page'] ?> de <?= $pagination['total_pages'] ?>
                                    (<?= $pagination['total_records'] ?> registros total)
                                </small>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Auto-búsqueda mientras escribe
        let searchTimeout;
        document.getElementById('searchInput').addEventListener('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function () {
                if (document.getElementById('searchInput').value.length >= 2 ||
                    document.getElementById('searchInput').value.length === 0) {
                    document.getElementById('searchForm').submit();
                }
            }, 500);
        });

        // Inicializar DataTables si hay datos
        <?php if (!empty($inquilinos) && count($inquilinos) > 10): ?>
            $('#inquilinosTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                },
                pageLength: 20,
                order: [[2, 'asc']], // Ordenar por nombre
                columnDefs: [
                    { orderable: false, targets: [1, 6] } // Foto y acciones no ordenables
                ]
            });
        <?php endif; ?>
    });

    function limpiarFiltros() {
        document.getElementById('searchInput').value = '';
        document.getElementById('filtroEstado').value = '';
        window.location.href = '<?= BASE_URL ?>inquilinos';
    }

    function confirmarEliminacion(id, nombre) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: `¿Deseas desactivar al inquilino "${nombre}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, desactivar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                eliminarInquilino(id);
            }
        });
    }

    function eliminarInquilino(id) {
        const formData = new FormData();
        formData.append('id', id);
        formData.append('csrf_token', window.App.csrfToken);

        fetch(window.App.baseUrl + 'inquilinos/delete', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error de conexión'
                });
            });
    }
</script>
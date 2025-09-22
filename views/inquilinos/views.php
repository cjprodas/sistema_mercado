<?php
$inquilino = $inquilino ?? [];
$servicios = $servicios ?? [];
$estadoCuenta = $estadoCuenta ?? null;
$historialPagos = $historialPagos ?? [];
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">
                <i class="bi bi-person-circle me-2"></i>
                Detalles del Inquilino
            </h1>
            <div>
                <a href="<?= BASE_URL ?>inquilinos" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
                <?php if (hasPermission('inquilinos_edit')): ?>
                    <a href="<?= BASE_URL ?>inquilinos/edit/<?= $inquilino['id'] ?>" class="btn btn-primary">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Información Personal -->
    <div class="col-xl-4 col-lg-5 col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-person me-2"></i>Información Personal</h5>
            </div>
            <div class="card-body">
                <!-- Foto del Propietario -->
                <div class="text-center mb-3">
                    <?php if (!empty($inquilino['foto_propietario'])): ?>
                        <img src="<?= BASE_URL ?>storage/uploads/inquilinos/<?= $inquilino['foto_propietario'] ?>"
                            class="img-fluid rounded-circle mb-2"
                            style="width: 120px; height: 120px; object-fit: cover; cursor: pointer;" data-bs-toggle="modal"
                            data-bs-target="#modalFotoPropietario">
                    <?php else: ?>
                        <div class="bg-light border rounded-circle d-flex align-items-center justify-content-center mb-2"
                            style="width: 120px; height: 120px; margin: 0 auto;">
                            <i class="bi bi-person fs-1 text-muted"></i>
                        </div>
                    <?php endif; ?>
                    <h5 class="mb-1"><?= htmlspecialchars($inquilino['nombre_completo'] ?? '') ?></h5>
                    <p class="text-muted mb-0">
                        <span class="badge bg-<?= ($inquilino['estado'] ?? '') == 'activo' ? 'success' : 'danger' ?>">
                            <?= ucfirst($inquilino['estado'] ?? 'Inactivo') ?>
                        </span>
                    </p>
                </div>

                <table class="table table-sm">
                    <tr>
                        <td><strong>DPI:</strong></td>
                        <td><?= htmlspecialchars($inquilino['dpi'] ?? '') ?></td>
                    </tr>
                    <tr>
                        <td><strong>Sexo:</strong></td>
                        <td><?= htmlspecialchars($inquilino['sexo'] ?? '') ?></td>
                    </tr>
                    <tr>
                        <td><strong>Teléfono:</strong></td>
                        <td>
                            <?php if (!empty($inquilino['telefono'])): ?>
                                <a href="tel:<?= $inquilino['telefono'] ?>" class="text-decoration-none">
                                    <?= htmlspecialchars($inquilino['telefono']) ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">No registrado</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Email:</strong></td>
                        <td>
                            <?php if (!empty($inquilino['email'])): ?>
                                <a href="mailto:<?= $inquilino['email'] ?>" class="text-decoration-none">
                                    <?= htmlspecialchars($inquilino['email']) ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">No registrado</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Domicilio:</strong></td>
                        <td><?= htmlspecialchars($inquilino['domicilio'] ?? '') ?></td>
                    </tr>
                    <tr>
                        <td><strong>Fecha Nac.:</strong></td>
                        <td>
                            <?php if (!empty($inquilino['fecha_nacimiento'])): ?>
                                <?= date('d/m/Y', strtotime($inquilino['fecha_nacimiento'])) ?>
                            <?php else: ?>
                                <span class="text-muted">No registrada</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Registro:</strong></td>
                        <td><?= date('d/m/Y H:i', strtotime($inquilino['created_at'] ?? 'now')) ?></td>
                    </tr>
                </table>

                <!-- DPI Imagen -->
                <div class="mt-3">
                    <h6 class="mb-2"><i class="bi bi-card-text me-2"></i>Documento de Identidad</h6>
                    <?php if (!empty($inquilino['foto_dpi'])): ?>
                        <img src="<?= BASE_URL ?>storage/uploads/inquilinos/<?= $inquilino['foto_dpi'] ?>"
                            class="img-fluid rounded border" style="max-height: 200px; cursor: pointer;"
                            data-bs-toggle="modal" data-bs-target="#modalFotoDPI">
                    <?php else: ?>
                        <div class="bg-light border rounded d-flex align-items-center justify-content-center"
                            style="height: 120px;">
                            <div class="text-center text-muted">
                                <i class="bi bi-card-image fs-2"></i>
                                <p class="mb-0 small">Sin foto del DPI</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Información Comercial -->
    <div class="col-xl-8 col-lg-7 col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-shop me-2"></i>Información Comercial</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Código Comercial:</strong></td>
                                <td>
                                    <span class="badge bg-primary fs-6">
                                        <?= htmlspecialchars($inquilino['codigo_comercial'] ?? '') ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Número de Local:</strong></td>
                                <td><?= htmlspecialchars($inquilino['numero_local'] ?? '') ?></td>
                            </tr>
                            <tr>
                                <td><strong>Categoría:</strong></td>
                                <td><?= htmlspecialchars($inquilino['categoria'] ?? '') ?></td>
                            </tr>
                            <tr>
                                <td><strong>Tipo de Venta:</strong></td>
                                <td><?= htmlspecialchars($inquilino['tipo_venta'] ?? '') ?></td>
                            </tr>
                            <tr>
                                <td><strong>Ubicación:</strong></td>
                                <td><?= htmlspecialchars($inquilino['ubicacion'] ?? '') ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Metros Cuadrados:</strong></td>
                                <td><?= number_format($inquilino['metros_cuadrados'] ?? 0, 2) ?> m²</td>
                            </tr>
                            <tr>
                                <td><strong>Tarifa Metro:</strong></td>
                                <td>Q. <?= number_format($inquilino['tarifa_metro'] ?? 0, 2) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Total Mensual:</strong></td>
                                <td>
                                    <strong class="text-success">
                                        Q.
                                        <?= number_format(($inquilino['metros_cuadrados'] ?? 0) * ($inquilino['tarifa_metro'] ?? 0), 2) ?>
                                    </strong>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Estado Comercial:</strong></td>
                                <td>
                                    <span
                                        class="badge bg-<?= ($inquilino['estado_comercial'] ?? '') == 'operando' ? 'success' : 'warning' ?>">
                                        <?= ucfirst($inquilino['estado_comercial'] ?? 'Sin definir') ?>
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <?php if (!empty($inquilino['observaciones'])): ?>
                    <div class="mt-3">
                        <h6><i class="bi bi-chat-text me-2"></i>Observaciones</h6>
                        <p class="text-muted"><?= nl2br(htmlspecialchars($inquilino['observaciones'])) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Servicios Contratados -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-gear me-2"></i>Servicios Contratados</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($servicios)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Servicio</th>
                                    <th>Descripción</th>
                                    <th class="text-end">Tarifa Mensual</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $totalServicios = 0;
                                foreach ($servicios as $servicio):
                                    $totalServicios += $servicio['tarifa'];
                                    ?>
                                    <tr>
                                        <td>
                                            <i class="bi bi-<?= getServiceIcon($servicio['nombre']) ?> me-2"></i>
                                            <?= htmlspecialchars($servicio['nombre']) ?>
                                        </td>
                                        <td><?= htmlspecialchars($servicio['descripcion'] ?? '') ?></td>
                                        <td class="text-end">Q. <?= number_format($servicio['tarifa'], 2) ?></td>
                                        <td>
                                            <span
                                                class="badge bg-<?= $servicio['estado'] == 'activo' ? 'success' : 'danger' ?>">
                                                <?= ucfirst($servicio['estado']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="table-info">
                                    <th colspan="2">Total Servicios:</th>
                                    <th class="text-end">Q. <?= number_format($totalServicios, 2) ?></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-gear fs-1"></i>
                        <p class="mb-0">No hay servicios contratados</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Estado de Cuenta Actual -->
        <?php if ($estadoCuenta): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Estado de Cuenta Actual</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h6 class="text-muted">Total a Pagar</h6>
                                <h4 class="text-primary mb-0">Q. <?= number_format($estadoCuenta['total'], 2) ?></h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h6 class="text-muted">Pagado</h6>
                                <h4 class="text-success mb-0">Q. <?= number_format($estadoCuenta['pagado'], 2) ?></h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h6 class="text-muted">Saldo Pendiente</h6>
                                <h4 class="text-<?= $estadoCuenta['saldo'] > 0 ? 'danger' : 'success' ?> mb-0">
                                    Q. <?= number_format($estadoCuenta['saldo'], 2) ?>
                                </h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h6 class="text-muted">Estado</h6>
                                <span
                                    class="badge bg-<?= $estadoCuenta['estado'] == 'al_dia' ? 'success' : 'danger' ?> fs-6">
                                    <?= $estadoCuenta['estado'] == 'al_dia' ? 'Al Día' : 'Moroso' ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <?php if (hasPermission('pagos_create')): ?>
                        <div class="text-center mt-3">
                            <a href="<?= BASE_URL ?>pagos/crear/<?= $inquilino['id'] ?>" class="btn btn-success">
                                <i class="bi bi-cash"></i> Registrar Pago
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Historial de Pagos -->
        <?php if (!empty($historialPagos)): ?>
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Historial de Pagos (Últimos 10)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Concepto</th>
                                    <th class="text-end">Monto</th>
                                    <th>Método</th>
                                    <th>Recibo #</th>
                                    <th>Usuario</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($historialPagos as $pago): ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($pago['fecha_pago'])) ?></td>
                                        <td><?= htmlspecialchars($pago['concepto']) ?></td>
                                        <td class="text-end text-success">Q. <?= number_format($pago['monto'], 2) ?></td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?= htmlspecialchars($pago['metodo_pago']) ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($pago['numero_recibo']) ?></td>
                                        <td><?= htmlspecialchars($pago['usuario_nombre']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="text-center mt-3">
                        <a href="<?= BASE_URL ?>pagos/historial/<?= $inquilino['id'] ?>" class="btn btn-outline-primary">
                            Ver Historial Completo
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Foto Propietario -->
<div class="modal fade" id="modalFotoPropietario" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Foto del Propietario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img src="<?= BASE_URL ?>storage/uploads/inquilinos/<?= $inquilino['foto_propietario'] ?? '' ?>"
                    class="img-fluid">
            </div>
        </div>
    </div>
</div>

<!-- Modal Foto DPI -->
<div class="modal fade" id="modalFotoDPI" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Documento de Identidad Personal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img src="<?= BASE_URL ?>storage/uploads/inquilinos/<?= $inquilino['foto_dpi'] ?? '' ?>"
                    class="img-fluid">
            </div>
        </div>
    </div>
</div>

<?php
// Función helper para iconos de servicios
function getServiceIcon($servicio)
{
    $iconos = [
        'agua' => 'droplet',
        'electricidad' => 'lightning',
        'basura' => 'trash',
        'seguridad' => 'shield-check',
        'limpieza' => 'brush',
        'mantenimiento' => 'tools'
    ];

    $servicioLower = strtolower($servicio);
    foreach ($iconos as $key => $icon) {
        if (strpos($servicioLower, $key) !== false) {
            return $icon;
        }
    }
    return 'gear';
}
?>
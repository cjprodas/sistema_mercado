<?php
$isEdit = isset($isEdit) && $isEdit;
$inquilino = $inquilino ?? [];
$servicios = $servicios ?? [];
?>

<form id="inquilinoForm" enctype="multipart/form-data" novalidate>
    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
    <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= $inquilino['id'] ?>">
    <?php endif; ?>
    
    <div class="row">
        <!-- Columna Principal -->
        <div class="col-lg-8">
            
            <!-- SECCIÓN: DATOS PERSONALES -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person me-2"></i>
                        Datos Personales
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="nombre_completo" class="form-label">
                                <i class="bi bi-person-badge me-1"></i>
                                Nombre Completo <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="nombre_completo" 
                                   name="nombre_completo"
                                   value="<?= e($inquilino['nombre_completo'] ?? '') ?>"
                                   placeholder="Ingrese el nombre completo"
                                   required>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="sexo" class="form-label">
                                <i class="bi bi-gender-ambiguous me-1"></i>
                                Sexo <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="sexo" name="sexo" required>
                                <option value="">Seleccionar...</option>
                                <option value="M" <?= ($inquilino['sexo'] ?? '') === 'M' ? 'selected' : '' ?>>Masculino</option>
                                <option value="F" <?= ($inquilino['sexo'] ?? '') === 'F' ? 'selected' : '' ?>>Femenino</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="dpi" class="form-label">
                                <i class="bi bi-credit-card me-1"></i>
                                DPI <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="dpi" 
                                   name="dpi"
                                   value="<?= e($inquilino['dpi'] ?? '') ?>"
                                   placeholder="1234567890123"
                                   maxlength="13"
                                   required>
                            <div class="form-text">13 dígitos numéricos</div>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="telefono" class="form-label">
                                <i class="bi bi-telephone me-1"></i>
                                Teléfono <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="telefono" 
                                   name="telefono"
                                   value="<?= e($inquilino['telefono'] ?? '') ?>"
                                   placeholder="55551234"
                                   maxlength="8"
                                   required>
                            <div class="form-text">8 dígitos numéricos</div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">
                                <i class="bi bi-envelope me-1"></i>
                                Email (Opcional)
                            </label>
                            <input type="email" 
                                   class="form-control" 
                                   id="email" 
                                   name="email"
                                   value="<?= e($inquilino['email'] ?? '') ?>"
                                   placeholder="correo@ejemplo.com">
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="fecha_nacimiento" class="form-label">
                                <i class="bi bi-calendar me-1"></i>
                                Fecha de Nacimiento (Opcional)
                            </label>
                            <input type="date" 
                                   class="form-control" 
                                   id="fecha_nacimiento" 
                                   name="fecha_nacimiento"
                                   value="<?= $inquilino['fecha_nacimiento'] ?? '' ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="estado_civil" class="form-label">
                                <i class="bi bi-heart me-1"></i>
                                Estado Civil (Opcional)
                            </label>
                            <select class="form-select" id="estado_civil" name="estado_civil">
                                <option value="">Seleccionar...</option>
                                <option value="soltero" <?= ($inquilino['estado_civil'] ?? '') === 'soltero' ? 'selected' : '' ?>>Soltero/a</option>
                                <option value="casado" <?= ($inquilino['estado_civil'] ?? '') === 'casado' ? 'selected' : '' ?>>Casado/a</option>
                                <option value="viudo" <?= ($inquilino['estado_civil'] ?? '') === 'viudo' ? 'selected' : '' ?>>Viudo/a</option>
                                <option value="divorciado" <?= ($inquilino['estado_civil'] ?? '') === 'divorciado' ? 'selected' : '' ?>>Divorciado/a</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="domicilio" class="form-label">
                                <i class="bi bi-house me-1"></i>
                                Domicilio <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" 
                                      id="domicilio" 
                                      name="domicilio" 
                                      rows="2"
                                      placeholder="Dirección completa del domicilio"
                                      required><?= e($inquilino['domicilio'] ?? '') ?></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- SECCIÓN: DATOS COMERCIALES -->
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-shop me-2"></i>
                        Datos Comerciales
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Código y Nivel -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nivel" class="form-label">
                                <i class="bi bi-building me-1"></i>
                                Nivel <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="nivel" name="nivel" required>
                                <option value="">Seleccionar nivel...</option>
                                <option value="N1" <?= ($inquilino['nivel'] ?? '') === 'N1' ? 'selected' : '' ?>>N1 - Planta Baja</option>
                                <option value="N2" <?= ($inquilino['nivel'] ?? '') === 'N2' ? 'selected' : '' ?>>N2 - Segundo Piso</option>
                                <option value="N3" <?= ($inquilino['nivel'] ?? '') === 'N3' ? 'selected' : '' ?>>N3 - Tercer Piso</option>
                                <option value="N4" <?= ($inquilino['nivel'] ?? '') === 'N4' ? 'selected' : '' ?>>N4 - Cuarto Piso</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="codigo_comercial" class="form-label">
                                <i class="bi bi-qr-code me-1"></i>
                                Código Comercial
                            </label>
                            <div class="input-group">
                                <input type="text" 
                                       class="form-control" 
                                       id="codigo_comercial"
                                       value="<?= e($inquilino['codigo_comercial'] ?? '') ?>"
                                       readonly>
                                <button type="button" class="btn btn-outline-secondary" onclick="generarCodigo()">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </button>
                            </div>
                            <div class="form-text">Se genera automáticamente según el nivel</div>
                        </div>
                    </div>
                    
                    <!-- Medidas -->
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="frente" class="form-label">
                                <i class="bi bi-arrows-vertical me-1"></i>
                                Frente (metros) <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   class="form-control" 
                                   id="frente" 
                                   name="frente"
                                   value="<?= $inquilino['frente'] ?? '' ?>"
                                   step="0.01"
                                   min="0.1"
                                   placeholder="0.00"
                                   required>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="fondo" class="form-label">
                                <i class="bi bi-arrows-horizontal me-1"></i>
                                Fondo (metros) <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   class="form-control" 
                                   id="fondo" 
                                   name="fondo"
                                   value="<?= $inquilino['fondo'] ?? '' ?>"
                                   step="0.01"
                                   min="0.1"
                                   placeholder="0.00"
                                   required>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="metros_cuadrados" class="form-label">
                                <i class="bi bi-aspect-ratio me-1"></i>
                                Metros Cuadrados
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="metros_cuadrados"
                                   readonly
                                   placeholder="Cálculo automático">
                            <div class="form-text">Frente × Fondo</div>
                        </div>
                    </div>
                    
                    <!-- Días de Ventas -->
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label">
                                <i class="bi bi-calendar-week me-1"></i>
                                Días de Ventas <span class="text-danger">*</span>
                            </label>
                            <div class="row">
                                <?php 
                                $diasVentas = json_decode($inquilino['dias_ventas'] ?? '[]', true) ?: [];
                                $diasSemana = $diasSemana ?? [
                                    'lunes' => 'Lunes', 'martes' => 'Martes', 'miercoles' => 'Miércoles',
                                    'jueves' => 'Jueves', 'viernes' => 'Viernes', 'sabado' => 'Sábado', 'domingo' => 'Domingo'
                                ];
                                ?>
                                <?php foreach ($diasSemana as $key => $dia): ?>
                                <div class="col-lg-3 col-md-4 col-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="dia_<?= $key ?>" 
                                               name="dias_ventas[]" 
                                               value="<?= $key ?>"
                                               <?= in_array($key, $diasVentas) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="dia_<?= $key ?>">
                                            <?= $dia ?>
                                        </label>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tipo de Venta -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tipo_venta" class="form-label">
                                <i class="bi bi-tag me-1"></i>
                                Tipo de Venta <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="tipo_venta" name="tipo_venta" required>
                                <option value="">Seleccionar tipo...</option>
                                <?php if (isset($tiposVentaPorCategoria)): ?>
                                    <?php foreach ($tiposVentaPorCategoria as $categoria => $tipos): ?>
                                        <optgroup label="<?= e($categoria) ?>">
                                            <?php foreach ($tipos as $tipo): ?>
                                                <option value="<?= e($tipo) ?>" 
                                                        <?= ($inquilino['tipo_venta'] ?? '') === $tipo ? 'selected' : '' ?>>
                                                    <?= e($tipo) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="estructura" class="form-label">
                                <i class="bi bi-building-gear me-1"></i>
                                Estructura <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="estructura" name="estructura" required>
                                <option value="">Seleccionar...</option>
                                <option value="MESA" <?= ($inquilino['estructura'] ?? '') === 'MESA' ? 'selected' : '' ?>>Mesa</option>
                                <option value="LOCAL" <?= ($inquilino['estructura'] ?? '') === 'LOCAL' ? 'selected' : '' ?>>Local</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    
                    <!-- Grupo y Clasificación -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="grupo_comercial" class="form-label">
                                <i class="bi bi-people-fill me-1"></i>
                                Grupo Comercial <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="grupo_comercial" name="grupo_comercial" required>
                                <option value="">Seleccionar...</option>
                                <option value="Asociación de comerciantes" <?= ($inquilino['grupo_comercial'] ?? '') === 'Asociación de comerciantes' ? 'selected' : '' ?>>Asociación de comerciantes</option>
                                <option value="Piso Plaza" <?= ($inquilino['grupo_comercial'] ?? '') === 'Piso Plaza' ? 'selected' : '' ?>>Piso Plaza</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="clasificacion" class="form-label">
                                <i class="bi bi-clipboard-check me-1"></i>
                                Clasificación <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="clasificacion" name="clasificacion" required>
                                <option value="">Seleccionar...</option>
                                <option value="Contrato" <?= ($inquilino['clasificacion'] ?? '') === 'Contrato' ? 'selected' : '' ?>>Con Contrato</option>
                                <option value="Sin contrato" <?= ($inquilino['clasificacion'] ?? '') === 'Sin contrato' ? 'selected' : '' ?>>Sin Contrato</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- SECCIÓN: SERVICIOS Y TARIFAS -->
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-gear me-2"></i>
                        Servicios y Tarifas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Renta -->
                        <div class="col-md-6 mb-3">
                            <label for="renta" class="form-label">
                                <i class="bi bi-house me-1"></i>
                                Renta Mensual <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="renta" name="renta" required>
                                <option value="">Seleccionar tarifa...</option>
                                <?php if (isset($tarifas['renta'])): ?>
                                    <?php foreach ($tarifas['renta'] as $tarifa): ?>
                                        <option value="<?= $tarifa['monto'] ?>" 
                                                <?= ($servicios['renta'] ?? 0) == $tarifa['monto'] ? 'selected' : '' ?>>
                                            Q. <?= number_format($tarifa['monto'], 2) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <!-- Energía -->
                        <div class="col-md-6 mb-3">
                            <label for="energia" class="form-label">
                                <i class="bi bi-lightning me-1"></i>
                                Energía Eléctrica
                            </label>
                            <select class="form-select" id="energia" name="energia">
                                <option value="0">Sin servicio</option>
                                <?php if (isset($tarifas['energia'])): ?>
                                    <?php foreach ($tarifas['energia'] as $tarifa): ?>
                                        <option value="<?= $tarifa['monto'] ?>" 
                                                <?= ($servicios['energia'] ?? 0) == $tarifa['monto'] ? 'selected' : '' ?>>
                                            Q. <?= number_format($tarifa['monto'], 2) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Agua -->
                        <div class="col-md-6 mb-3">
                            <label for="agua" class="form-label">
                                <i class="bi bi-droplet me-1"></i>
                                Agua Potable
                            </label>
                            <select class="form-select" id="agua" name="agua">
                                <option value="0">Sin servicio</option>
                                <?php if (isset($tarifas['agua'])): ?>
                                    <?php foreach ($tarifas['agua'] as $tarifa): ?>
                                        <option value="<?= $tarifa['monto'] ?>" 
                                                <?= ($servicios['agua'] ?? 0) == $tarifa['monto'] ? 'selected' : '' ?>>
                                            Q. <?= number_format($tarifa['monto'], 2) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        
                        <!-- Basura -->
                        <div class="col-md-6 mb-3">
                            <label for="basura" class="form-label">
                                <i class="bi bi-trash me-1"></i>
                                Recolección de Basura
                            </label>
                            <select class="form-select" id="basura" name="basura">
                                <option value="0">Sin servicio</option>
                                <?php if (isset($tarifas['basura'])): ?>
                                    <?php foreach ($tarifas['basura'] as $tarifa): ?>
                                        <option value="<?= $tarifa['monto'] ?>" 
                                                <?= ($servicios['basura'] ?? 0) == $tarifa['monto'] ? 'selected' : '' ?>>
                                            Q. <?= number_format($tarifa['monto'], 2) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Total Estimado -->
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-info" id="totalEstimado">
                                <i class="bi bi-calculator me-2"></i>
                                <strong>Total Mensual Estimado: Q. 0.00</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Observaciones -->
            <div class="card shadow mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-chat-text me-2"></i>
                        Observaciones
                    </h5>
                </div>
                <div class="card-body">
                    <textarea class="form-control" 
                              id="observaciones" 
                              name="observaciones" 
                              rows="3"
                              placeholder="Información adicional sobre el inquilino (opcional)"><?= e($inquilino['observaciones'] ?? '') ?></textarea>
                </div>
            </div>
        </div>
        
        <!-- Columna Lateral -->
        <div class="col-lg-4">
            
            <!-- SECCIÓN: FOTOGRAFÍAS -->
            <div class="card shadow mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-camera me-2"></i>
                        Fotografías
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Foto del Propietario -->
                    <div class="mb-4">
                        <label class="form-label">
                            <i class="bi bi-person-badge me-1"></i>
                            Foto del Propietario
                        </label>
                        <div class="text-center mb-3">
                            <div id="preview_propietario" class="mb-2">
                                <?php if (!empty($inquilino['foto_propietario'])): ?>
                                    <img src="<?= BASE_URL ?>storage/uploads/inquilinos/<?= $inquilino['foto_propietario'] ?>" 
                                         class="img-thumbnail" 
                                         style="max-width: 200px; max-height: 200px;">
                                <?php else: ?>
                                    <div class="bg-light border rounded d-flex align-items-center justify-content-center" 
                                         style="width: 200px; height: 200px; margin: 0 auto;">
                                        <i class="bi bi-person text-muted" style="font-size: 4rem;"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <input type="file" 
                                   class="form-control" 
                                   id="foto_propietario" 
                                   name="foto_propietario" 
                                   accept="image/*"
                                   onchange="previewImage(this, 'preview_propietario')">
                            <div class="form-text">JPG, PNG - Máximo 2MB</div>
                        </div>
                    </div>
                    
                    <!-- Foto del DPI -->
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="bi bi-credit-card me-1"></i>
                            Foto del DPI
                        </label>
                        <div class="text-center mb-3">
                            <div id="preview_dpi" class="mb-2">
                                <?php if (!empty($inquilino['foto_dpi'])): ?>
                                    <img src="<?= BASE_URL ?>storage/uploads/inquilinos/<?= $inquilino['foto_dpi'] ?>" 
                                         class="img-thumbnail" 
                                         style="max-width: 200px; max-height: 200px;">
                                <?php else: ?>
                                    <div class="bg-light border rounded d-flex align-items-center justify-content-center" 
                                         style="width: 200px; height: 120px; margin: 0 auto;">
                                        <i class="bi bi-credit-card text-muted" style="font-size: 3rem;"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <input type="file" 
                                   class="form-control" 
                                   id="foto_dpi" 
                                   name="foto_dpi" 
                                   accept="image/*"
                                   onchange="previewImage(this, 'preview_dpi')">
                            <div class="form-text">JPG, PNG - Máximo 2MB</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- SECCIÓN: ACCIONES -->
            <div class="card shadow">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                            <i class="bi bi-save me-2"></i>
                            <?= $isEdit ? 'Actualizar Inquilino' : 'Guardar Inquilino' ?>
                        </button>
                        
                        <a href="<?= BASE_URL ?>inquilinos" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>
                            Volver a la Lista
                        </a>
                        
                        <?php if ($isEdit): ?>
                        <hr>
                        <a href="<?= BASE_URL ?>inquilinos/view/<?= $inquilino['id'] ?>" class="btn btn-outline-info">
                            <i class="bi bi-eye me-2"></i>
                            Ver Detalles
                        </a>
                        <?php endif; ?>
                    </div>
                    
                    <hr>
                    
                    <div class="small text-muted">
                        <strong>Campos requeridos:</strong> <span class="text-danger">*</span><br>
                        Los campos marcados son obligatorios para el registro.
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Calcular metros cuadrados automáticamente
    calcularMetrosCuadrados();
    
    // Escuchar cambios en frente y fondo
    document.getElementById('frente').addEventListener('input', calcularMetrosCuadrados);
    document.getElementById('fondo').addEventListener('input', calcularMetrosCuadrados);
    
    // Calcular total de servicios
    calcularTotalServicios();
    
    // Escuchar cambios en servicios
    ['renta', 'energia', 'agua', 'basura'].forEach(function(servicio) {
        document.getElementById(servicio).addEventListener('change', calcularTotalServicios);
    });
    
    // Generar código al cambiar nivel
    document.getElementById('nivel').addEventListener('change', function() {
        if (this.value) {
            generarCodigo();
        }
    });
    
    // Validación de DPI en tiempo real
    document.getElementById('dpi').addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '').substring(0, 13);
        validarDPI();
    });
    
    // Validación de teléfono en tiempo real
    document.getElementById('telefono').addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '').substring(0, 8);
    });
    
    // Manejar envío del formulario
    document.getElementById('inquilinoForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!validarFormulario()) {
            return;
        }
        
        enviarFormulario();
    });
    
    // Generar código inicial si es nuevo
    <?php if (!$isEdit): ?>
    if (document.getElementById('nivel').value) {
        generarCodigo();
    }
    <?php endif; ?>
});

function calcularMetrosCuadrados() {
    const frente = parseFloat(document.getElementById('frente').value) || 0;
    const fondo = parseFloat(document.getElementById('fondo').value) || 0;
    const total = (frente * fondo).toFixed(2);
    
    document.getElementById('metros_cuadrados').value = total > 0 ? total + ' m²' : '';
}

function calcularTotalServicios() {
    const renta = parseFloat(document.getElementById('renta').value) || 0;
    const energia = parseFloat(document.getElementById('energia').value) || 0;
    const agua = parseFloat(document.getElementById('agua').value) || 0;
    const basura = parseFloat(document.getElementById('basura').value) || 0;
    
    const total = renta + energia + agua + basura;
    
    document.getElementById('totalEstimado').innerHTML = 
        '<i class="bi bi-calculator me-2"></i>' +
        '<strong>Total Mensual Estimado: Q. ' + total.toFixed(2) + '</strong>';
}

function generarCodigo() {
    const nivel = document.getElementById('nivel').value;
    
    if (!nivel) return;
    
    fetch(`${window.App.baseUrl}inquilinos/generar-codigo?nivel=${nivel}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('codigo_comercial').value = data.codigo;
            }
        })
        .catch(error => {
            console.error('Error al generar código:', error);
        });
}

function validarDPI() {
    const dpi = document.getElementById('dpi').value;
    const input = document.getElementById('dpi');
    
    if (dpi.length === 13) {
        // Aquí podrías agregar validación adicional del algoritmo de DPI guatemalteco
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
    } else if (dpi.length > 0) {
        input.classList.remove('is-valid');
        input.classList.add('is-invalid');
    } else {
        input.classList.remove('is-valid', 'is-invalid');
    }
}

function validarFormulario() {
    let isValid = true;
    const form = document.getElementById('inquilinoForm');
    
    // Limpiar validaciones anteriores
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    
    // Validar campos requeridos
    const requiredFields = [
        'nombre_completo', 'dpi', 'sexo', 'telefono', 'domicilio',
        'nivel', 'frente', 'fondo', 'tipo_venta', 'estructura', 
        'grupo_comercial', 'clasificacion', 'renta'
    ];
    
    requiredFields.forEach(fieldName => {
        const field = document.getElementById(fieldName);
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        }
    });
    
    // Validar DPI
    const dpi = document.getElementById('dpi').value;
    if (dpi.length !== 13) {
        document.getElementById('dpi').classList.add('is-invalid');
        isValid = false;
    }
    
    // Validar teléfono
    const telefono = document.getElementById('telefono').value;
    if (telefono.length !== 8) {
        document.getElementById('telefono').classList.add('is-invalid');
        isValid = false;
    }
    
    // Validar que tenga al menos un día de ventas
    const diasSeleccionados = form.querySelectorAll('input[name="dias_ventas[]"]:checked');
    if (diasSeleccionados.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Días de Ventas',
            text: 'Debe seleccionar al menos un día de ventas'
        });
        isValid = false;
    }
    
    // Validar medidas
    const frente = parseFloat(document.getElementById('frente').value);
    const fondo = parseFloat(document.getElementById('fondo').value);
    
    if (frente <= 0) {
        document.getElementById('frente').classList.add('is-invalid');
        isValid = false;
    }
    
    if (fondo <= 0) {
        document.getElementById('fondo').classList.add('is-invalid');
        isValid = false;
    }
    
    return isValid;
}

function enviarFormulario() {
    const form = document.getElementById('inquilinoForm');
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;
    
    // Mostrar estado de carga
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Guardando...';
    
    const formData = new FormData(form);
    const isEdit = formData.has('id');
    const url = isEdit ? 
        `${window.App.baseUrl}inquilinos/update` : 
        `${window.App.baseUrl}inquilinos/store`;
    
    fetch(url, {
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
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    window.location.href = `${window.App.baseUrl}inquilinos`;
                }
            });
        } else {
            // Mostrar errores específicos
            if (data.errors) {
                Object.keys(data.errors).forEach(field => {
                    const fieldElement = document.getElementById(field);
                    if (fieldElement) {
                        fieldElement.classList.add('is-invalid');
                        const feedback = fieldElement.parentNode.querySelector('.invalid-feedback');
                        if (feedback) {
                            feedback.textContent = data.errors[field];
                        }
                    }
                });
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error de Conexión',
            text: 'No se pudo conectar con el servidor'
        });
    })
    .finally(() => {
        // Restaurar botón
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.innerHTML = `
                <img src="${e.target.result}" 
                     class="img-thumbnail" 
                     style="max-width: 200px; max-height: 200px;">
            `;
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Validación en tiempo real para todos los campos
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('inquilinoForm');
    
    // Agregar validación en tiempo real a todos los inputs
    form.querySelectorAll('input, select, textarea').forEach(field => {
        field.addEventListener('blur', function() {
            validateField(this);
        });
        
        field.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                validateField(this);
            }
        });
    });
});

function validateField(field) {
    const value = field.value.trim();
    let isValid = true;
    let message = '';
    
    // Validar según el tipo de campo
    switch (field.name) {
        case 'nombre_completo':
            if (!value) {
                isValid = false;
                message = 'El nombre completo es requerido';
            } else if (value.length < 3) {
                isValid = false;
                message = 'El nombre debe tener al menos 3 caracteres';
            }
            break;
            
        case 'dpi':
            if (!value) {
                isValid = false;
                message = 'El DPI es requerido';
            } else if (!/^\d{13}$/.test(value)) {
                isValid = false;
                message = 'El DPI debe tener exactamente 13 dígitos';
            }
            break;
            
        case 'telefono':
            if (!value) {
                isValid = false;
                message = 'El teléfono es requerido';
            } else if (!/^\d{8}$/.test(value)) {
                isValid = false;
                message = 'El teléfono debe tener exactamente 8 dígitos';
            }
            break;
            
        case 'email':
            if (value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                isValid = false;
                message = 'El email no tiene un formato válido';
            }
            break;
            
        case 'frente':
        case 'fondo':
            if (!value || parseFloat(value) <= 0) {
                isValid = false;
                message = 'Debe ser un número mayor a 0';
            }
            break;
    }
    
    // Aplicar clases de validación
    if (field.hasAttribute('required') && !value) {
        isValid = false;
        message = message || 'Este campo es requerido';
    }
    
    if (isValid) {
        field.classList.remove('is-invalid');
        field.classList.add('is-valid');
    } else {
        field.classList.remove('is-valid');
        field.classList.add('is-invalid');
        
        const feedback = field.parentNode.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.textContent = message;
        }
    }
}
</script>

<style>
.form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.card-header {
    font-weight: 600;
}

.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.img-thumbnail {
    transition: transform 0.2s;
}

.img-thumbnail:hover {
    transform: scale(1.05);
}

.alert-info {
    background-color: #e7f3ff;
    border-color: #b3d7ff;
    color: #004085;
}

@media (max-width: 768px) {
    .col-lg-3 {
        margin-bottom: 0.5rem;
    }
    
    .card-body {
        padding: 1rem;
    }
    
    .btn-lg {
        padding: 0.5rem 1rem;
        font-size: 1rem;
    }
}
</style>
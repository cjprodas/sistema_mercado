<?php
/**
 * Controlador de Inquilinos
 */

class InquilinosController extends BaseController
{

    private $inquilinoModel;

    public function __construct()
    {
        parent::__construct();
        $this->inquilinoModel = new Inquilino();
        $this->requirePermission('inquilinos', 'read');
    }

    /**
     * Lista de inquilinos
     */
    public function index()
    {
        $this->viewData['pageTitle'] = 'Gestión de Inquilinos';
        $this->viewData['breadcrumbs'] = [
            ['title' => 'Inquilinos']
        ];

        try {
            // Obtener lista de inquilinos con paginación
            $page = $_GET['page'] ?? 1;
            $search = $_GET['search'] ?? '';

            if (!empty($search)) {
                $inquilinos = $this->inquilinoModel->search($search);
                $this->viewData['inquilinos'] = $inquilinos;
                $this->viewData['search'] = $search;
            } else {
                $result = $this->inquilinoModel->paginate($page, 20, [], 'nombre_completo ASC');
                $this->viewData['inquilinos'] = $result['data'];
                $this->viewData['pagination'] = $result['pagination'];
            }

        } catch (Exception $e) {
            $this->setFlashMessage('error', 'Error al cargar inquilinos');
            $this->viewData['inquilinos'] = [];
        }

        $this->viewData['viewFile'] = 'inquilinos/index';
        $this->view('inquilinos/index');
    }

    /**
     * Mostrar formulario de crear inquilino
     */
    public function create()
    {
        $this->requirePermission('inquilinos', 'create');

        $this->viewData['pageTitle'] = 'Nuevo Inquilino';
        $this->viewData['breadcrumbs'] = [
            ['title' => 'Inquilinos', 'url' => url('inquilinos')],
            ['title' => 'Nuevo Inquilino']
        ];

        // Cargar datos para formulario
        $this->loadFormData();

        $this->viewData['viewFile'] = 'inquilinos/form';
        $this->view('inquilinos/form');
    }

    /**
     * Guardar nuevo inquilino
     */
    public function store()
    {
        $this->requireMethod('POST');
        $this->validateCSRF();
        $this->requirePermission('inquilinos', 'create');

        try {
            // Validar datos
            $errors = $this->validateInquilinoData($_POST);

            if (!empty($errors)) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Datos inválidos',
                    'errors' => $errors
                ]);
            }

            // Preparar datos del inquilino
            $inquilinoData = [
                'nombre_completo' => trim($_POST['nombre_completo']),
                'dpi' => trim($_POST['dpi']),
                'sexo' => $_POST['sexo'],
                'telefono' => trim($_POST['telefono']),
                'email' => trim($_POST['email']) ?: null,
                'domicilio' => trim($_POST['domicilio']),
                'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?: null,
                'estado_civil' => $_POST['estado_civil'] ?: null,
                'observaciones' => trim($_POST['observaciones']) ?: null,
                'estado' => 'activo',
                'created_by' => $this->currentUser['id']
            ];

            // Preparar datos comerciales
            $comercialData = [
                'nivel' => $_POST['nivel'],
                'frente' => floatval($_POST['frente']),
                'fondo' => floatval($_POST['fondo']),
                'dias_ventas' => $_POST['dias_ventas'] ?? [],
                'tipo_venta' => $_POST['tipo_venta'],
                'estructura' => $_POST['estructura'],
                'grupo_comercial' => $_POST['grupo_comercial'],
                'clasificacion' => $_POST['clasificacion']
            ];

            // Preparar datos de servicios
            $serviciosData = [
                'renta' => floatval($_POST['renta'] ?? 0),
                'energia' => floatval($_POST['energia'] ?? 0),
                'agua' => floatval($_POST['agua'] ?? 0),
                'basura' => floatval($_POST['basura'] ?? 0)
            ];

            // Crear inquilino
            $inquilinoId = $this->inquilinoModel->createWithCommercialData(
                $inquilinoData,
                $comercialData,
                $serviciosData
            );

            // Manejar archivos subidos
            $this->handleFileUploads($inquilinoId);

            // Log de actividad
            $this->logActivity('Inquilino creado', [
                'inquilino_id' => $inquilinoId,
                'nombre' => $inquilinoData['nombre_completo'],
                'dpi' => $inquilinoData['dpi']
            ]);

            $this->jsonResponse([
                'success' => true,
                'message' => 'Inquilino registrado exitosamente',
                'redirect' => url('inquilinos')
            ]);

        } catch (Exception $e) {
            logMessage('ERROR', 'Error al crear inquilino', [
                'error' => $e->getMessage(),
                'user_id' => $this->currentUser['id']
            ]);

            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al registrar inquilino: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar formulario de editar inquilino
     */
    public function edit($id)
    {
        $this->requirePermission('inquilinos', 'update');

        try {
            $inquilino = $this->inquilinoModel->getCompleteData($id);

            if (!$inquilino) {
                $this->setFlashMessage('error', 'Inquilino no encontrado');
                $this->redirect('inquilinos');
            }

            $this->viewData['pageTitle'] = 'Editar Inquilino';
            $this->viewData['breadcrumbs'] = [
                ['title' => 'Inquilinos', 'url' => url('inquilinos')],
                ['title' => 'Editar: ' . $inquilino['nombre_completo']]
            ];

            // Cargar datos para formulario
            $this->loadFormData();

            // Cargar servicios del inquilino
            $servicios = $this->inquilinoModel->getServices($id);
            $this->viewData['servicios'] = [];
            foreach ($servicios as $servicio) {
                $this->viewData['servicios'][$servicio['concepto']] = $servicio['monto'];
            }

            $this->viewData['inquilino'] = $inquilino;
            $this->viewData['isEdit'] = true;
            $this->viewData['viewFile'] = 'inquilinos/form';
            $this->view('inquilinos/form');

        } catch (Exception $e) {
            $this->setFlashMessage('error', 'Error al cargar inquilino');
            $this->redirect('inquilinos');
        }
    }

    /**
     * Actualizar inquilino
     */
    public function update()
    {
        $this->requireMethod('POST');
        $this->validateCSRF();
        $this->requirePermission('inquilinos', 'update');

        $id = $_POST['id'] ?? null;

        if (!$id) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'ID de inquilino requerido'
            ]);
        }

        try {
            // Validar datos
            $errors = $this->validateInquilinoData($_POST, $id);

            if (!empty($errors)) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Datos inválidos',
                    'errors' => $errors
                ]);
            }

            // Preparar datos (similar a store pero sin created_by)
            $inquilinoData = [
                'nombre_completo' => trim($_POST['nombre_completo']),
                'dpi' => trim($_POST['dpi']),
                'sexo' => $_POST['sexo'],
                'telefono' => trim($_POST['telefono']),
                'email' => trim($_POST['email']) ?: null,
                'domicilio' => trim($_POST['domicilio']),
                'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?: null,
                'estado_civil' => $_POST['estado_civil'] ?: null,
                'observaciones' => trim($_POST['observaciones']) ?: null
            ];

            $comercialData = [
                'nivel' => $_POST['nivel'],
                'frente' => floatval($_POST['frente']),
                'fondo' => floatval($_POST['fondo']),
                'dias_ventas' => $_POST['dias_ventas'] ?? [],
                'tipo_venta' => $_POST['tipo_venta'],
                'estructura' => $_POST['estructura'],
                'grupo_comercial' => $_POST['grupo_comercial'],
                'clasificacion' => $_POST['clasificacion']
            ];

            $serviciosData = [
                'renta' => floatval($_POST['renta'] ?? 0),
                'energia' => floatval($_POST['energia'] ?? 0),
                'agua' => floatval($_POST['agua'] ?? 0),
                'basura' => floatval($_POST['basura'] ?? 0)
            ];

            // Actualizar inquilino
            $this->inquilinoModel->updateWithCommercialData(
                $id,
                $inquilinoData,
                $comercialData,
                $serviciosData
            );

            // Manejar archivos subidos
            $this->handleFileUploads($id);

            // Log de actividad
            $this->logActivity('Inquilino actualizado', [
                'inquilino_id' => $id,
                'nombre' => $inquilinoData['nombre_completo']
            ]);

            $this->jsonResponse([
                'success' => true,
                'message' => 'Inquilino actualizado exitosamente',
                'redirect' => url('inquilinos')
            ]);

        } catch (Exception $e) {
            logMessage('ERROR', 'Error al actualizar inquilino', [
                'error' => $e->getMessage(),
                'inquilino_id' => $id,
                'user_id' => $this->currentUser['id']
            ]);

            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al actualizar inquilino: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ver detalles del inquilino
     */
    public function view($id)
    {
        try {
            $inquilino = $this->inquilinoModel->getCompleteData($id);

            if (!$inquilino) {
                $this->setFlashMessage('error', 'Inquilino no encontrado');
                $this->redirect('inquilinos');
            }

            $this->viewData['pageTitle'] = 'Detalles: ' . $inquilino['nombre_completo'];
            $this->viewData['breadcrumbs'] = [
                ['title' => 'Inquilinos', 'url' => url('inquilinos')],
                ['title' => $inquilino['nombre_completo']]
            ];

            // Cargar datos adicionales
            $this->viewData['inquilino'] = $inquilino;
            $this->viewData['servicios'] = $this->inquilinoModel->getServices($id);
            $this->viewData['estadoCuenta'] = $this->inquilinoModel->getCurrentAccount($id);
            $this->viewData['historialPagos'] = $this->inquilinoModel->getPaymentHistory($id, 5);

            $this->viewData['viewFile'] = 'inquilinos/view';
            $this->view('inquilinos/view');

        } catch (Exception $e) {
            $this->setFlashMessage('error', 'Error al cargar inquilino');
            $this->redirect('inquilinos');
        }
    }

    /**
     * Eliminar/desactivar inquilino
     */
    public function delete()
    {
        $this->requireMethod('POST');
        $this->validateCSRF();
        $this->requirePermission('inquilinos', 'delete');

        $id = $_POST['id'] ?? null;

        if (!$id) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'ID de inquilino requerido'
            ]);
        }

        try {
            // Soft delete
            $this->inquilinoModel->update($id, ['estado' => 'inactivo']);

            // Liberar local
            $this->db->query(
                "UPDATE datos_comerciales SET estado_local = 'disponible' WHERE inquilino_id = ?",
                [$id]
            );

            $this->logActivity('Inquilino desactivado', ['inquilino_id' => $id]);

            $this->jsonResponse([
                'success' => true,
                'message' => 'Inquilino desactivado exitosamente'
            ]);

        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al desactivar inquilino'
            ], 500);
        }
    }

    /**
     * Búsqueda AJAX de inquilinos
     */
    public function search()
    {
        $term = $_GET['term'] ?? '';

        if (strlen($term) < 2) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Término de búsqueda muy corto'
            ]);
        }

        try {
            $inquilinos = $this->inquilinoModel->getForSelect($term);

            $this->jsonResponse([
                'success' => true,
                'data' => $inquilinos
            ]);

        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error en búsqueda'
            ], 500);
        }
    }

    /**
     * Obtener tarifas disponibles para AJAX
     */
    public function getTarifas()
    {
        $concepto = $_GET['concepto'] ?? '';

        if (empty($concepto)) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Concepto requerido'
            ]);
        }

        try {
            $tarifas = $this->db->fetchAll(
                "SELECT monto, descripcion FROM tarifas WHERE concepto = ? AND activo = TRUE ORDER BY monto",
                [$concepto]
            );

            $this->jsonResponse([
                'success' => true,
                'data' => $tarifas
            ]);

        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al obtener tarifas'
            ], 500);
        }
    }

    /**
     * Generar código comercial automático
     */
    public function generarCodigo()
    {
        $nivel = $_GET['nivel'] ?? '';

        if (empty($nivel)) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Nivel requerido'
            ]);
        }

        try {
            $codigo = $this->inquilinoModel->generateCommercialCode($nivel);

            $this->jsonResponse([
                'success' => true,
                'codigo' => $codigo
            ]);

        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al generar código'
            ], 500);
        }
    }

    /**
     * Subir foto del inquilino
     */
    public function uploadPhoto()
    {
        $this->requireMethod('POST');
        $this->validateCSRF();
        $this->requirePermission('inquilinos', 'update');

        $id = $_POST['inquilino_id'] ?? null;
        $tipo = $_POST['tipo'] ?? 'propietario'; // propietario o dpi

        if (!$id) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'ID de inquilino requerido'
            ]);
        }

        try {
            $uploadResult = $this->handleSingleFileUpload('foto', $id, $tipo);

            if ($uploadResult['success']) {
                // Actualizar BD
                $field = $tipo === 'dpi' ? 'foto_dpi' : 'foto_propietario';
                $this->inquilinoModel->update($id, [$field => $uploadResult['filename']]);

                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Foto subida exitosamente',
                    'filename' => $uploadResult['filename'],
                    'url' => url('storage/uploads/inquilinos/' . $uploadResult['filename'])
                ]);
            } else {
                $this->jsonResponse([
                    'success' => false,
                    'message' => $uploadResult['message']
                ]);
            }

        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al subir foto'
            ], 500);
        }
    }

    /**
     * Cargar datos para formularios
     */
    private function loadFormData()
    {
        // Tipos de venta por categoría
        $tiposVenta = $this->inquilinoModel->getTiposVenta();
        $this->viewData['tiposVentaPorCategoria'] = [];
        foreach ($tiposVenta as $tipo) {
            $this->viewData['tiposVentaPorCategoria'][$tipo['categoria']][] = $tipo['nombre'];
        }

        // Tarifas disponibles
        $this->viewData['tarifas'] = [
            'renta' => $this->db->fetchAll("SELECT monto FROM tarifas WHERE concepto = 'renta' AND activo = TRUE ORDER BY monto"),
            'energia' => $this->db->fetchAll("SELECT monto FROM tarifas WHERE concepto = 'energia' AND activo = TRUE ORDER BY monto"),
            'agua' => $this->db->fetchAll("SELECT monto FROM tarifas WHERE concepto = 'agua' AND activo = TRUE ORDER BY monto"),
            'basura' => $this->db->fetchAll("SELECT monto FROM tarifas WHERE concepto = 'basura' AND activo = TRUE ORDER BY monto")
        ];

        // Días de la semana
        $this->viewData['diasSemana'] = [
            'lunes' => 'Lunes',
            'martes' => 'Martes',
            'miercoles' => 'Miércoles',
            'jueves' => 'Jueves',
            'viernes' => 'Viernes',
            'sabado' => 'Sábado',
            'domingo' => 'Domingo'
        ];
    }

    /**
     * Validar datos del inquilino
     */
    private function validateInquilinoData($data, $id = null)
    {
        $errors = [];

        // Validar nombre
        if (empty($data['nombre_completo'])) {
            $errors['nombre_completo'] = 'El nombre completo es requerido';
        } elseif (strlen($data['nombre_completo']) < 3) {
            $errors['nombre_completo'] = 'El nombre debe tener al menos 3 caracteres';
        }

        // Validar DPI
        if (empty($data['dpi'])) {
            $errors['dpi'] = 'El DPI es requerido';
        } elseif (!preg_match('/^\d{13}$/', $data['dpi'])) {
            $errors['dpi'] = 'El DPI debe tener exactamente 13 dígitos';
        } else {
            // Verificar que no exista (excepto si es edición)
            $existing = $this->inquilinoModel->first('dpi', $data['dpi']);
            if ($existing && (!$id || $existing['id'] != $id)) {
                $errors['dpi'] = 'Este DPI ya está registrado';
            }
        }

        // Validar teléfono
        if (empty($data['telefono'])) {
            $errors['telefono'] = 'El teléfono es requerido';
        } elseif (!preg_match('/^\d{8}$/', $data['telefono'])) {
            $errors['telefono'] = 'El teléfono debe tener exactamente 8 dígitos';
        }

        // Validar email si se proporciona
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'El email no tiene un formato válido';
        }

        // Validar domicilio
        if (empty($data['domicilio'])) {
            $errors['domicilio'] = 'El domicilio es requerido';
        }

        // Validar datos comerciales
        if (empty($data['nivel'])) {
            $errors['nivel'] = 'El nivel es requerido';
        }

        if (empty($data['frente']) || !is_numeric($data['frente']) || $data['frente'] <= 0) {
            $errors['frente'] = 'El frente debe ser un número mayor a 0';
        }

        if (empty($data['fondo']) || !is_numeric($data['fondo']) || $data['fondo'] <= 0) {
            $errors['fondo'] = 'El fondo debe ser un número mayor a 0';
        }

        if (empty($data['tipo_venta'])) {
            $errors['tipo_venta'] = 'El tipo de venta es requerido';
        }

        if (empty($data['estructura'])) {
            $errors['estructura'] = 'La estructura es requerida';
        }

        if (empty($data['grupo_comercial'])) {
            $errors['grupo_comercial'] = 'El grupo comercial es requerido';
        }

        if (empty($data['clasificacion'])) {
            $errors['clasificacion'] = 'La clasificación es requerida';
        }

        // Validar que al menos un servicio tenga tarifa
        $servicios = ['renta', 'energia', 'agua', 'basura'];
        $tieneServicios = false;
        foreach ($servicios as $servicio) {
            if (!empty($data[$servicio]) && $data[$servicio] > 0) {
                $tieneServicios = true;
                break;
            }
        }

        if (!$tieneServicios) {
            $errors['servicios'] = 'Debe asignar al menos un servicio con tarifa';
        }

        return $errors;
    }

    /**
     * Manejar subida de archivos
     */
    private function handleFileUploads($inquilinoId)
    {
        $uploadDir = UPLOAD_PATH . 'inquilinos/';
        ensureDirectoryExists($uploadDir);

        // Manejar foto del propietario
        if (isset($_FILES['foto_propietario']) && $_FILES['foto_propietario']['error'] === UPLOAD_ERR_OK) {
            $result = $this->uploadFile($_FILES['foto_propietario'], $uploadDir, 'propietario_' . $inquilinoId);
            if ($result['success']) {
                $this->inquilinoModel->update($inquilinoId, ['foto_propietario' => $result['filename']]);
            }
        }

        // Manejar foto del DPI
        if (isset($_FILES['foto_dpi']) && $_FILES['foto_dpi']['error'] === UPLOAD_ERR_OK) {
            $result = $this->uploadFile($_FILES['foto_dpi'], $uploadDir, 'dpi_' . $inquilinoId);
            if ($result['success']) {
                $this->inquilinoModel->update($inquilinoId, ['foto_dpi' => $result['filename']]);
            }
        }
    }

    /**
     * Subir archivo individual
     */
    private function uploadFile($file, $uploadDir, $prefix)
    {
        // Validar tipo de archivo
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'message' => 'Tipo de archivo no permitido'];
        }

        // Validar tamaño (2MB máximo)
        if ($file['size'] > MAX_FILE_SIZE) {
            return ['success' => false, 'message' => 'Archivo muy grande (máximo 2MB)'];
        }

        // Generar nombre único
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $prefix . '_' . time() . '.' . $extension;
        $filepath = $uploadDir . $filename;

        // Mover archivo
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            // Redimensionar imagen si es necesario
            $this->resizeImage($filepath, 800, 600);

            return ['success' => true, 'filename' => $filename];
        } else {
            return ['success' => false, 'message' => 'Error al subir archivo'];
        }
    }

    /**
     * Redimensionar imagen
     */
    private function resizeImage($filepath, $maxWidth, $maxHeight)
    {
        $imageInfo = getimagesize($filepath);
        if (!$imageInfo)
            return;

        $width = $imageInfo[0];
        $height = $imageInfo[1];
        $type = $imageInfo[2];

        // Si ya es menor, no redimensionar
        if ($width <= $maxWidth && $height <= $maxHeight)
            return;

        // Calcular nuevas dimensiones
        $ratio = min($maxWidth / $width, $maxHeight / $height);
        $newWidth = round($width * $ratio);
        $newHeight = round($height * $ratio);

        // Crear imagen desde archivo
        switch ($type) {
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($filepath);
                break;
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($filepath);
                break;
            default:
                return;
        }

        // Crear nueva imagen
        $destination = imagecreatetruecolor($newWidth, $newHeight);

        // Para PNG, preservar transparencia
        if ($type === IMAGETYPE_PNG) {
            imagealphablending($destination, false);
            imagesavealpha($destination, true);
        }

        // Redimensionar
        imagecopyresampled($destination, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Guardar
        switch ($type) {
            case IMAGETYPE_JPEG:
                imagejpeg($destination, $filepath, 85);
                break;
            case IMAGETYPE_PNG:
                imagepng($destination, $filepath);
                break;
        }

        // Limpiar memoria
        imagedestroy($source);
        imagedestroy($destination);
    }

    /**
     * Subida individual de foto para AJAX
     */
    private function handleSingleFileUpload($fieldName, $inquilinoId, $tipo)
    {
        if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'No se recibió archivo válido'];
        }

        $uploadDir = UPLOAD_PATH . 'inquilinos/';
        ensureDirectoryExists($uploadDir);

        return $this->uploadFile($_FILES[$fieldName], $uploadDir, $tipo . '_' . $inquilinoId);
    }
}
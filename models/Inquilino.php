<?php
/**
 * Controlador de Inquilinos
 */

class InquilinosController extends BaseController {
    
    private $inquilinoModel;
    
    public function __construct() {
        parent::__construct();
        $this->inquilinoModel = new Inquilino();
        $this->requirePermission('inquilinos', 'read');
    }
    
    /**
     * Lista de inquilinos
     */
    public function index() {
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
    public function create() {
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
    public function store() {
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
    public function edit($id) {
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
    public function update() {
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
    public function view($id) {
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
    public function delete() {
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
    public function search() {
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
    public function getTarifas() {
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
    public function generarCodigo() {
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
    public function uploadPhoto() {
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
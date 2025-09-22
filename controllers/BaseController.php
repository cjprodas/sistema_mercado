<?php
/**
 * Controlador Base del Sistema
 * Contiene funcionalidades comunes para todos los controladores
 */

class BaseController
{
    protected $db;
    protected $currentUser;
    protected $viewData = [];

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->checkAuthentication();
        $this->loadCurrentUser();
        $this->setGlobalViewData();
    }

    /**
     * Verificar autenticación del usuario
     */
    protected function checkAuthentication()
    {
        // Rutas que no requieren autenticación
        $publicRoutes = ['login', 'logout'];
        $currentRoute = $this->getCurrentRoute();

        if (!in_array($currentRoute, $publicRoutes) && !$this->isAuthenticated()) {
            $this->redirect('login');
        }
    }

    /**
     * Verificar si el usuario está autenticado
     */
    protected function isAuthenticated()
    {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * Cargar información del usuario actual
     */
    protected function loadCurrentUser()
    {
        if ($this->isAuthenticated()) {
            $userId = $_SESSION['user_id'];
            $this->currentUser = $this->db->fetch(
                "SELECT u.*, r.nombre as rol_nombre, r.permisos as rol_permisos 
                 FROM usuarios u 
                 JOIN roles r ON u.rol_id = r.id 
                 WHERE u.id = ? AND u.estado = 'activo'",
                [$userId]
            );

            if (!$this->currentUser) {
                $this->logout();
            }

            // Actualizar último login
            $this->db->query(
                "UPDATE usuarios SET ultimo_login = NOW() WHERE id = ?",
                [$userId]
            );
        }
    }

    /**
     * Obtener la ruta actual
     */
    protected function getCurrentRoute()
    {
        $uri = $_SERVER['REQUEST_URI'];
        $path = parse_url($uri, PHP_URL_PATH);
        $path = str_replace(str_replace($_SERVER['DOCUMENT_ROOT'], '', ROOT_PATH . 'public'), '', $path);
        return trim($path, '/');
    }

    /**
     * Configurar datos globales para las vistas
     */
    protected function setGlobalViewData()
    {
        $this->viewData['currentUser'] = $this->currentUser;
        $this->viewData['appName'] = APP_NAME;
        $this->viewData['appVersion'] = APP_VERSION;
        $this->viewData['baseUrl'] = BASE_URL;
        $this->viewData['csrfToken'] = generateCSRFToken();

        // Cargar configuraciones del sistema
        $this->viewData['mercadoNombre'] = getConfig('mercado_nombre', 'Mercado Municipal');
        $this->viewData['mercadoDireccion'] = getConfig('mercado_direccion', '');

        // Cargar notificaciones no leídas
        if ($this->currentUser) {
            $this->viewData['notificaciones'] = $this->getNotificacionesNoLeidas();
            $this->viewData['countNotificaciones'] = count($this->viewData['notificaciones']);
        }
    }

    /**
     * Verificar permisos del usuario
     */
    protected function hasPermission($module, $action = 'read')
    {
        if (!$this->currentUser) {
            return false;
        }

        $permisos = json_decode($this->currentUser['rol_permisos'], true);

        if (!isset($permisos[$module])) {
            return false;
        }

        $modulePerms = $permisos[$module];

        // Si tiene permiso 'all', puede hacer todo
        if ($modulePerms === 'all') {
            return true;
        }

        // Si es 'none', no puede hacer nada
        if ($modulePerms === 'none') {
            return false;
        }

        // Verificar permisos específicos
        $allowedActions = explode(',', $modulePerms);
        return in_array($action, $allowedActions);
    }

    /**
     * Verificar permisos y redirigir si no los tiene
     */
    protected function requirePermission($module, $action = 'read')
    {
        if (!$this->hasPermission($module, $action)) {
            $this->setFlashMessage('error', 'No tienes permisos para realizar esta acción');
            $this->redirect('dashboard');
        }
    }

    /**
     * Cargar notificaciones no leídas
     */
    protected function getNotificacionesNoLeidas()
    {
        if (!$this->currentUser) {
            return [];
        }

        return $this->db->fetchAll(
            "SELECT * FROM notificaciones 
             WHERE (usuario_id = ? OR usuario_id IS NULL) 
             AND leida = FALSE 
             ORDER BY fecha_envio DESC 
             LIMIT 10",
            [$this->currentUser['id']]
        );
    }

    /**
     * Renderizar vista
     */
    protected function view($viewFile, $data = [])
    {
        // Combinar datos específicos de la vista con datos globales
        $viewData = array_merge($this->viewData, $data);

        // Extraer variables para la vista
        extract($viewData);

        // Cargar layout principal
        include ROOT_PATH . 'views/layouts/main.php';
    }

    /**
     * Renderizar vista sin layout (para AJAX)
     */
    protected function viewPartial($viewFile, $data = [])
    {
        $viewData = array_merge($this->viewData, $data);
        extract($viewData);

        include ROOT_PATH . 'views/' . $viewFile . '.php';
    }

    /**
     * Respuesta JSON
     */
    protected function jsonResponse($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Redirección
     */
    protected function redirect($url = '')
    {
        if (empty($url)) {
            $url = BASE_URL;
        } elseif (!filter_var($url, FILTER_VALIDATE_URL)) {
            $url = BASE_URL . ltrim($url, '/');
        }

        header("Location: $url");
        exit;
    }

    /**
     * Mensajes flash
     */
    protected function setFlashMessage($type, $message)
    {
        $_SESSION['flash_messages'][] = [
            'type' => $type,
            'message' => $message
        ];
    }

    /**
     * Obtener mensajes flash
     */
    protected function getFlashMessages()
    {
        $messages = $_SESSION['flash_messages'] ?? [];
        unset($_SESSION['flash_messages']);
        return $messages;
    }

    /**
     * Validar token CSRF
     */
    protected function validateCSRF()
    {
        $token = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? '';

        if (!validateCSRFToken($token)) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Token de seguridad inválido'
            ], 403);
        }
    }

    /**
     * Validar método HTTP
     */
    protected function requireMethod($method)
    {
        if ($_SERVER['REQUEST_METHOD'] !== strtoupper($method)) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Método no permitido'
            ], 405);
        }
    }

    /**
     * Logout del usuario
     */
    protected function logout()
    {
        // Registrar logout en logs
        if ($this->currentUser) {
            logMessage('INFO', 'Usuario cerró sesión', [
                'user_id' => $this->currentUser['id'],
                'email' => $this->currentUser['email']
            ]);
        }

        session_unset();
        session_destroy();
        $this->redirect('login');
    }

    /**
     * Sanitizar datos de entrada
     */
    protected function sanitizeInput($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'sanitizeInput'], $data);
        }

        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validar datos requeridos
     */
    protected function validateRequired($data, $required_fields)
    {
        $errors = [];

        foreach ($required_fields as $field) {
            if (empty($data[$field])) {
                $errors[$field] = "El campo $field es requerido";
            }
        }

        return $errors;
    }

    /**
     * Registrar actividad del usuario
     */
    protected function logActivity($action, $details = [])
    {
        if ($this->currentUser) {
            $context = array_merge([
                'user_id' => $this->currentUser['id'],
                'user_email' => $this->currentUser['email'],
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ], $details);

            logMessage('INFO', $action, $context);

            // También guardar en base de datos para reportes
            $this->db->query(
                "INSERT INTO logs_sistema (nivel, mensaje, contexto, usuario_id, ip_address, url, metodo) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)",
                [
                    'INFO',
                    $action,
                    json_encode($context),
                    $this->currentUser['id'],
                    $_SERVER['REMOTE_ADDR'] ?? null,
                    $_SERVER['REQUEST_URI'] ?? null,
                    $_SERVER['REQUEST_METHOD'] ?? null
                ]
            );
        }
    }

    /**
     * Crear notificación
     */
    protected function createNotification($userId, $tipo, $titulo, $mensaje, $enlace = null)
    {
        $this->db->query(
            "INSERT INTO notificaciones (usuario_id, tipo, titulo, mensaje, enlace) VALUES (?, ?, ?, ?, ?)",
            [$userId, $tipo, $titulo, $mensaje, $enlace]
        );
    }

    /**
     * Paginación
     */
    protected function paginate($sql, $params = [], $page = 1, $perPage = RECORDS_PER_PAGE)
    {
        // Contar total de registros
        $countSql = "SELECT COUNT(*) FROM ($sql) as count_query";
        $totalRecords = $this->db->count($countSql, $params);

        // Calcular paginación
        $totalPages = ceil($totalRecords / $perPage);
        $offset = ($page - 1) * $perPage;

        // Obtener registros de la página actual
        $dataSql = "$sql LIMIT $perPage OFFSET $offset";
        $records = $this->db->fetchAll($dataSql, $params);

        return [
            'data' => $records,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total_records' => $totalRecords,
                'total_pages' => $totalPages,
                'has_previous' => $page > 1,
                'has_next' => $page < $totalPages,
                'previous_page' => $page > 1 ? $page - 1 : null,
                'next_page' => $page < $totalPages ? $page + 1 : null
            ]
        ];
    }
}
<?php
/**
 * Sistema de Rutas del Sistema
 * Maneja el enrutamiento de todas las peticiones
 */

class Router
{
    private $routes = [];
    private $currentRoute;
    private $params = [];

    public function __construct()
    {
        $this->defineRoutes();
        $this->parseCurrentRoute();
    }

    /**
     * Definir todas las rutas del sistema
     */
    private function defineRoutes()
    {
        // Rutas de autenticación
        $this->routes = [
            // AUTH ROUTES
            '' => ['controller' => 'AuthController', 'method' => 'login'],
            'login' => ['controller' => 'AuthController', 'method' => 'login'],
            'process-login' => ['controller' => 'AuthController', 'method' => 'processLogin'],
            'logout' => ['controller' => 'AuthController', 'method' => 'logout'],
            'change-password' => ['controller' => 'AuthController', 'method' => 'changePassword'],
            'check-session' => ['controller' => 'AuthController', 'method' => 'checkSession'],
            'extend-session' => ['controller' => 'AuthController', 'method' => 'extendSession'],

            // DASHBOARD ROUTES
            'dashboard' => ['controller' => 'DashboardController', 'method' => 'index'],
            'dashboard/stats' => ['controller' => 'DashboardController', 'method' => 'getStats'],
            'dashboard/notifications' => ['controller' => 'DashboardController', 'method' => 'getNotifications'],
            'dashboard/mark-notification-read' => ['controller' => 'DashboardController', 'method' => 'markNotificationRead'],

            // USUARIOS ROUTES
            'usuarios' => ['controller' => 'UsuariosController', 'method' => 'index'],
            'usuarios/create' => ['controller' => 'UsuariosController', 'method' => 'create'],
            'usuarios/store' => ['controller' => 'UsuariosController', 'method' => 'store'],
            'usuarios/edit/{id}' => ['controller' => 'UsuariosController', 'method' => 'edit'],
            'usuarios/update' => ['controller' => 'UsuariosController', 'method' => 'update'],
            'usuarios/delete' => ['controller' => 'UsuariosController', 'method' => 'delete'],
            'usuarios/profile' => ['controller' => 'UsuariosController', 'method' => 'profile'],
            'usuarios/update-profile' => ['controller' => 'UsuariosController', 'method' => 'updateProfile'],
            'usuarios/upload-photo' => ['controller' => 'UsuariosController', 'method' => 'uploadPhoto'],

            // INQUILINOS ROUTES
            'inquilinos' => ['controller' => 'InquilinosController', 'method' => 'index'],
            'inquilinos/create' => ['controller' => 'InquilinosController', 'method' => 'create'],
            'inquilinos/store' => ['controller' => 'InquilinosController', 'method' => 'store'],
            'inquilinos/edit/{id}' => ['controller' => 'InquilinosController', 'method' => 'edit'],
            'inquilinos/update' => ['controller' => 'InquilinosController', 'method' => 'update'],
            'inquilinos/delete' => ['controller' => 'InquilinosController', 'method' => 'delete'],
            'inquilinos/view/{id}' => ['controller' => 'InquilinosController', 'method' => 'view'],
            'inquilinos/search' => ['controller' => 'InquilinosController', 'method' => 'search'],
            'inquilinos/get-tarifas' => ['controller' => 'InquilinosController', 'method' => 'getTarifas'],
            'inquilinos/generar-codigo' => ['controller' => 'InquilinosController', 'method' => 'generarCodigo'],
            'inquilinos/upload-photo' => ['controller' => 'InquilinosController', 'method' => 'uploadPhoto'],
            'inquilinos/estado-cuenta/{id}' => ['controller' => 'InquilinosController', 'method' => 'estadoCuenta'],

            // PAGOS ROUTES
            'pagos' => ['controller' => 'PagosController', 'method' => 'index'],
            'pagos/create' => ['controller' => 'PagosController', 'method' => 'create'],
            'pagos/store' => ['controller' => 'PagosController', 'method' => 'store'],
            'pagos/view/{id}' => ['controller' => 'PagosController', 'method' => 'view'],
            'pagos/search-inquilino' => ['controller' => 'PagosController', 'method' => 'searchInquilino'],
            'pagos/get-estado-cuenta' => ['controller' => 'PagosController', 'method' => 'getEstadoCuenta'],
            'pagos/comprobante/{id}' => ['controller' => 'PagosController', 'method' => 'comprobante'],
            'pagos/historial/{inquilino_id}' => ['controller' => 'PagosController', 'method' => 'historial'],

            // ESTADOS DE CUENTA ROUTES
            'estados-cuenta' => ['controller' => 'EstadosCuentaController', 'method' => 'index'],
            'estados-cuenta/generar' => ['controller' => 'EstadosCuentaController', 'method' => 'generar'],
            'estados-cuenta/generar-masivo' => ['controller' => 'EstadosCuentaController', 'method' => 'generarMasivo'],
            'estados-cuenta/view/{id}' => ['controller' => 'EstadosCuentaController', 'method' => 'view'],
            'estados-cuenta/pdf/{id}' => ['controller' => 'EstadosCuentaController', 'method' => 'generarPDF'],
            'estados-cuenta/aplicar-mora' => ['controller' => 'EstadosCuentaController', 'method' => 'aplicarMora'],

            // CONTRATOS ROUTES
            'contratos' => ['controller' => 'ContratosController', 'method' => 'index'],
            'contratos/create' => ['controller' => 'ContratosController', 'method' => 'create'],
            'contratos/store' => ['controller' => 'ContratosController', 'method' => 'store'],
            'contratos/edit/{id}' => ['controller' => 'ContratosController', 'method' => 'edit'],
            'contratos/update' => ['controller' => 'ContratosController', 'method' => 'update'],
            'contratos/view/{id}' => ['controller' => 'ContratosController', 'method' => 'view'],
            'contratos/renovar/{id}' => ['controller' => 'ContratosController', 'method' => 'renovar'],
            'contratos/upload-documento' => ['controller' => 'ContratosController', 'method' => 'uploadDocumento'],
            'contratos/por-vencer' => ['controller' => 'ContratosController', 'method' => 'porVencer'],

            // REPORTES ROUTES
            'reportes' => ['controller' => 'ReportesController', 'method' => 'index'],
            'reportes/morosos' => ['controller' => 'ReportesController', 'method' => 'morosos'],
            'reportes/al-dia' => ['controller' => 'ReportesController', 'method' => 'alDia'],
            'reportes/locales' => ['controller' => 'ReportesController', 'method' => 'locales'],
            'reportes/piso-plaza' => ['controller' => 'ReportesController', 'method' => 'pisoPlaza'],
            'reportes/pagos-diarios' => ['controller' => 'ReportesController', 'method' => 'pagosDiarios'],
            'reportes/pagos-mensuales' => ['controller' => 'ReportesController', 'method' => 'pagosMensuales'],
            'reportes/energia' => ['controller' => 'ReportesController', 'method' => 'energia'],
            'reportes/basura' => ['controller' => 'ReportesController', 'method' => 'basura'],
            'reportes/financiero' => ['controller' => 'ReportesController', 'method' => 'financiero'],
            'reportes/ocupacion' => ['controller' => 'ReportesController', 'method' => 'ocupacion'],

            // CONFIGURACIÓN ROUTES
            'configuracion' => ['controller' => 'ConfiguracionController', 'method' => 'index'],
            'configuracion/general' => ['controller' => 'ConfiguracionController', 'method' => 'general'],
            'configuracion/tarifas' => ['controller' => 'ConfiguracionController', 'method' => 'tarifas'],
            'configuracion/update-general' => ['controller' => 'ConfiguracionController', 'method' => 'updateGeneral'],
            'configuracion/update-tarifas' => ['controller' => 'ConfiguracionController', 'method' => 'updateTarifas'],
            'configuracion/backup' => ['controller' => 'ConfiguracionController', 'method' => 'backup'],
            'configuracion/restore' => ['controller' => 'ConfiguracionController', 'method' => 'restore'],
            'configuracion/logs' => ['controller' => 'ConfiguracionController', 'method' => 'logs'],
            'configuracion/tipos-venta' => ['controller' => 'ConfiguracionController', 'method' => 'tiposVenta'],

            // API ROUTES (para AJAX)
            'api/get-inquilino/{id}' => ['controller' => 'ApiController', 'method' => 'getInquilino'],
            'api/search-inquilinos' => ['controller' => 'ApiController', 'method' => 'searchInquilinos'],
            'api/get-tarifas/{concepto}' => ['controller' => 'ApiController', 'method' => 'getTarifas'],
            'api/dashboard-stats' => ['controller' => 'ApiController', 'method' => 'dashboardStats'],
            'api/validate-dpi' => ['controller' => 'ApiController', 'method' => 'validateDPI'],
            'api/generar-codigo-comercial' => ['controller' => 'ApiController', 'method' => 'generarCodigoComercial'],

            // AJAX ROUTES
            'ajax/upload-file' => ['controller' => 'AjaxController', 'method' => 'uploadFile'],
            'ajax/delete-file' => ['controller' => 'AjaxController', 'method' => 'deleteFile'],
            'ajax/get-notifications' => ['controller' => 'AjaxController', 'method' => 'getNotifications'],
            'ajax/mark-notification-read' => ['controller' => 'AjaxController', 'method' => 'markNotificationRead']
        ];
    }

    /**
     * Analizar la ruta actual
     */
    private function parseCurrentRoute()
    {
        $uri = $_SERVER['REQUEST_URI'];
        $path = parse_url($uri, PHP_URL_PATH);

        // Remover el path base del proyecto
        $basePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', dirname($_SERVER['SCRIPT_NAME']));
        $path = str_replace($basePath, '', $path);
        $path = trim($path, '/');

        // Si está vacía, es la ruta raíz
        if (empty($path)) {
            $path = '';
        }

        $this->currentRoute = $path;
    }

    /**
     * Manejar la ruta actual
     */
    public function handleRequest()
    {
        $route = $this->findRoute($this->currentRoute);

        if (!$route) {
            $this->handle404();
            return;
        }

        $controllerName = $route['controller'];
        $methodName = $route['method'];

        // Verificar que el controlador existe
        if (!class_exists($controllerName)) {
            $this->handle404();
            return;
        }

        // Instanciar controlador
        $controller = new $controllerName();

        // Verificar que el método existe
        if (!method_exists($controller, $methodName)) {
            $this->handle404();
            return;
        }

        // Ejecutar método del controlador
        try {
            call_user_func_array([$controller, $methodName], $this->params);
        } catch (Exception $e) {
            $this->handleError($e);
        }
    }

    /**
     * Buscar ruta que coincida
     */
    private function findRoute($currentRoute)
    {
        // Buscar coincidencia exacta primero
        if (isset($this->routes[$currentRoute])) {
            return $this->routes[$currentRoute];
        }

        // Buscar rutas con parámetros
        foreach ($this->routes as $pattern => $route) {
            if (strpos($pattern, '{') !== false) {
                $regex = $this->convertPatternToRegex($pattern);
                if (preg_match($regex, $currentRoute, $matches)) {
                    // Extraer parámetros
                    array_shift($matches); // Remover la coincidencia completa
                    $this->params = $matches;
                    return $route;
                }
            }
        }

        return null;
    }

    /**
     * Convertir patrón de ruta a expresión regular
     */
    private function convertPatternToRegex($pattern)
    {
        $pattern = str_replace('/', '\/', $pattern);
        $pattern = preg_replace('/\{([^}]+)\}/', '([^\/]+)', $pattern);
        return '/^' . $pattern . '$/';
    }

    /**
     * Manejar error 404
     */
    private function handle404()
    {
        http_response_code(404);

        // Si es una petición AJAX, devolver JSON
        if ($this->isAjaxRequest()) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Ruta no encontrada'
            ]);
            exit;
        }

        // Mostrar página 404
        include ROOT_PATH . 'views/errors/404.php';
        exit;
    }

    /**
     * Manejar errores del sistema
     */
    private function handleError($exception)
    {
        // Log del error
        error_log("Error en aplicación: " . $exception->getMessage());
        logMessage('ERROR', 'Error en aplicación', [
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ]);

        http_response_code(500);

        // Si es una petición AJAX, devolver JSON
        if ($this->isAjaxRequest()) {
            header('Content-Type: application/json');
            $response = [
                'success' => false,
                'message' => 'Error interno del servidor'
            ];

            // En desarrollo, mostrar detalles del error
            if (ENVIRONMENT === 'development') {
                $response['debug'] = [
                    'message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine()
                ];
            }

            echo json_encode($response);
            exit;
        }

        // Mostrar página de error
        $errorMessage = $exception->getMessage();
        include ROOT_PATH . 'views/errors/500.php';
        exit;
    }

    /**
     * Verificar si es una petición AJAX
     */
    private function isAjaxRequest()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Generar URL desde nombre de ruta
     */
    public static function url($route, $params = [])
    {
        $url = BASE_URL . $route;

        // Reemplazar parámetros en la URL
        foreach ($params as $key => $value) {
            $url = str_replace('{' . $key . '}', $value, $url);
        }

        return $url;
    }

    /**
     * Redirigir a una ruta
     */
    public static function redirect($route, $params = [])
    {
        $url = self::url($route, $params);
        header("Location: $url");
        exit;
    }

    /**
     * Verificar si la ruta actual coincide
     */
    public function isCurrentRoute($route)
    {
        return $this->currentRoute === $route;
    }

    /**
     * Obtener ruta actual
     */
    public function getCurrentRoute()
    {
        return $this->currentRoute;
    }

    /**
     * Obtener parámetros de la ruta actual
     */
    public function getParams()
    {
        return $this->params;
    }
}
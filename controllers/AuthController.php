<?php
/**
 * Controlador de Autenticación - CORREGIDO
 * Maneja login, logout y funciones de seguridad
 */

class AuthController extends BaseController
{

    public function __construct()
    {
        // No llamar parent::__construct() para evitar verificación de autenticación
        $this->db = Database::getInstance();
        $this->viewData = [];
        $this->setBasicViewData();
    }

    /**
     * Configurar datos básicos para vistas de autenticación
     */
    private function setBasicViewData()
    {
        $this->viewData['appName'] = APP_NAME;
        $this->viewData['appVersion'] = APP_VERSION;
        $this->viewData['baseUrl'] = BASE_URL;
        $this->viewData['csrfToken'] = generateCSRFToken();
    }

    /**
     * Mostrar formulario de login
     */
    public function login()
    {
        // Si ya está autenticado, redirigir al dashboard
        if ($this->isUserAuthenticated()) {
            $this->redirect('dashboard');
        }

        $this->view('auth/login');
    }

    /**
     * Procesar login
     */
    public function processLogin()
    {
        $this->requireMethod('POST');

        // Validar CSRF
        $token = $_POST['csrf_token'] ?? '';
        if (!validateCSRFToken($token)) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Token de seguridad inválido'
            ], 403);
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);

        // Validación básica
        if (empty($email) || empty($password)) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Email y contraseña son requeridos'
            ]);
        }

        // Verificar si la IP está bloqueada
        if ($this->isIpBlocked()) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'IP bloqueada temporalmente por intentos fallidos'
            ]);
        }

        // Buscar usuario
        $user = $this->db->fetch(
            "SELECT u.*, r.nombre as rol_nombre, r.permisos as rol_permisos 
             FROM usuarios u 
             JOIN roles r ON u.rol_id = r.id 
             WHERE u.email = ? AND u.estado = 'activo'",
            [$email]
        );

        // Verificar usuario y contraseña
        if (!$user || !password_verify($password, $user['password_hash'])) {
            $this->handleFailedLogin($email);
            $this->jsonResponse([
                'success' => false,
                'message' => 'Credenciales incorrectas'
            ]);
        }

        // Verificar si el usuario está bloqueado
        if ($this->isUserBlocked($user)) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Usuario bloqueado temporalmente'
            ]);
        }

        // Login exitoso
        $this->handleSuccessfulLogin($user, $remember);

        $this->jsonResponse([
            'success' => true,
            'message' => 'Login exitoso',
            'redirect' => url('dashboard')
        ]);
    }

    /**
     * Logout del usuario
     */
    public function logout()
    {
        // Registrar logout
        if (isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
            logMessage('INFO', 'Usuario cerró sesión', [
                'user_id' => $userId,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
        }

        // Destruir sesión
        session_unset();
        session_destroy();

        // Eliminar cookie si existe
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/');
        }

        $this->redirect('login');
    }

    /**
     * Verificar si el usuario está autenticado
     */
    private function isUserAuthenticated()
    {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * Verificar si la IP está bloqueada
     */
    private function isIpBlocked()
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $blockTime = time() - LOCKOUT_TIME;

        $attempts = $this->db->count(
            "SELECT COUNT(*) FROM logs_sistema 
             WHERE nivel = 'WARNING' 
             AND mensaje = 'Intento de login fallido' 
             AND ip_address = ? 
             AND created_at > FROM_UNIXTIME(?)",
            [$ip, $blockTime]
        );

        return $attempts >= MAX_LOGIN_ATTEMPTS;
    }

    /**
     * Verificar si el usuario está bloqueado
     */
    private function isUserBlocked($user)
    {
        if (empty($user['bloqueado_hasta'])) {
            return false;
        }

        $blockedUntil = new DateTime($user['bloqueado_hasta']);
        $now = new DateTime();

        return $now < $blockedUntil;
    }

    /**
     * Manejar login fallido
     */
    private function handleFailedLogin($email)
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        // Registrar intento fallido
        logMessage('WARNING', 'Intento de login fallido', [
            'email' => $email,
            'ip_address' => $ip,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ]);

        // Incrementar contador de intentos si el usuario existe
        $this->db->query(
            "UPDATE usuarios 
             SET intentos_login = intentos_login + 1,
                 bloqueado_hasta = CASE 
                     WHEN intentos_login + 1 >= ? THEN DATE_ADD(NOW(), INTERVAL ? SECOND)
                     ELSE bloqueado_hasta 
                 END
             WHERE email = ?",
            [MAX_LOGIN_ATTEMPTS, LOCKOUT_TIME, $email]
        );
    }

    /**
     * Manejar login exitoso
     */
    private function handleSuccessfulLogin($user, $remember = false)
    {
        // Resetear intentos de login
        $this->db->query(
            "UPDATE usuarios 
             SET intentos_login = 0, 
                 bloqueado_hasta = NULL, 
                 ultimo_login = NOW() 
             WHERE id = ?",
            [$user['id']]
        );

        // Iniciar sesión
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['nombre'];
        $_SESSION['user_role'] = $user['rol_nombre'];
        $_SESSION['login_time'] = time();
        $_SESSION['last_activity'] = time();

        // Manejar "recordarme"
        if ($remember) {
            $this->setRememberCookie($user['id']);
        }

        // Registrar login exitoso
        logMessage('INFO', 'Login exitoso', [
            'user_id' => $user['id'],
            'email' => $user['email'],
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ]);

        // Crear notificación de bienvenida si es primer login del día
        $this->createWelcomeNotification($user['id']);
    }

    /**
     * Configurar cookie de "recordarme"
     */
    private function setRememberCookie($userId)
    {
        $token = bin2hex(random_bytes(32));
        $expires = time() + (30 * 24 * 60 * 60); // 30 días

        // Guardar token en base de datos (aquí podrías crear una tabla de tokens)
        // Por simplicidad, lo guardamos en la sesión
        setcookie('remember_token', $token, $expires, '/', '', true, true);

        // En un sistema completo, guardarías el token hasheado en la BD
        $_SESSION['remember_token'] = password_hash($token, PASSWORD_DEFAULT);
    }

    /**
     * Crear notificación de bienvenida
     */
    private function createWelcomeNotification($userId)
    {
        $today = date('Y-m-d');

        // Verificar si ya existe notificación de hoy
        $exists = $this->db->count(
            "SELECT COUNT(*) FROM notificaciones 
             WHERE usuario_id = ? 
             AND tipo = 'info' 
             AND titulo LIKE 'Bienvenido%' 
             AND DATE(fecha_envio) = ?",
            [$userId, $today]
        );

        if (!$exists) {
            $mensaje = "¡Bienvenido al sistema! Tienes acceso a todas las funcionalidades según tu rol.";

            $this->db->query(
                "INSERT INTO notificaciones (usuario_id, tipo, titulo, mensaje) 
                 VALUES (?, 'info', 'Bienvenido al Sistema', ?)",
                [$userId, $mensaje]
            );
        }
    }

    /**
     * Cambiar contraseña
     */
    public function changePassword()
    {
        $this->requireMethod('POST');

        // Verificar autenticación
        if (!$this->isUserAuthenticated()) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'No autenticado'
            ], 401);
        }

        $this->validateCSRF();

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validaciones
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Todos los campos son requeridos'
            ]);
        }

        if ($newPassword !== $confirmPassword) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'La nueva contraseña y confirmación no coinciden'
            ]);
        }

        if (strlen($newPassword) < 6) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'La contraseña debe tener al menos 6 caracteres'
            ]);
        }

        // Obtener usuario actual
        $userId = $_SESSION['user_id'];
        $user = $this->db->fetch(
            "SELECT password_hash FROM usuarios WHERE id = ?",
            [$userId]
        );

        // Verificar contraseña actual
        if (!password_verify($currentPassword, $user['password_hash'])) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'La contraseña actual es incorrecta'
            ]);
        }

        // Actualizar contraseña
        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

        $this->db->query(
            "UPDATE usuarios SET password_hash = ?, updated_at = NOW() WHERE id = ?",
            [$newPasswordHash, $userId]
        );

        // Registrar cambio
        logMessage('INFO', 'Contraseña cambiada', [
            'user_id' => $userId,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);

        $this->jsonResponse([
            'success' => true,
            'message' => 'Contraseña actualizada correctamente'
        ]);
    }

    /**
     * Verificar sesión (para AJAX)
     */
    public function checkSession()
    {
        $this->jsonResponse([
            'authenticated' => $this->isUserAuthenticated(),
            'time_remaining' => $this->getSessionTimeRemaining()
        ]);
    }

    /**
     * Obtener tiempo restante de sesión
     */
    private function getSessionTimeRemaining()
    {
        if (!isset($_SESSION['last_activity'])) {
            return 0;
        }

        $elapsed = time() - $_SESSION['last_activity'];
        $remaining = SESSION_TIMEOUT - $elapsed;

        return max(0, $remaining);
    }

    /**
     * Extender sesión
     */
    public function extendSession()
    {
        if ($this->isUserAuthenticated()) {
            $_SESSION['last_activity'] = time();

            $this->jsonResponse([
                'success' => true,
                'time_remaining' => SESSION_TIMEOUT
            ]);
        } else {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Sesión expirada'
            ], 401);
        }
    }

    /**
     * Renderizar vista de autenticación
     */
    protected function view($viewFile, $data = [])
    {
        $viewData = array_merge($this->viewData, $data);
        extract($viewData);

        // Cargar layout de autenticación
        include ROOT_PATH . 'views/layouts/auth.php';
    }

    /**
     * Validar CSRF para métodos de autenticación - CORREGIDO: protected
     */
    protected function validateCSRF()
    {
        $token = $_POST['csrf_token'] ?? '';

        if (!validateCSRFToken($token)) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Token de seguridad inválido'
            ], 403);
        }
    }

    /**
     * Requerir método HTTP específico - CORREGIDO: protected
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
     * Respuesta JSON - CORREGIDO: protected (igual que BaseController)
     */
    protected function jsonResponse($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Redirección - CORREGIDO: protected
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
}
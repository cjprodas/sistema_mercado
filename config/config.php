<?php
/**
 * Configuración Principal del Sistema - CORREGIDO
 * Sistema de Administración de Mercado
 */

// Configuración del entorno
define('ENVIRONMENT', 'development'); // development, production

// Configuración de rutas - SOLO DEFINIR SI NO EXISTE
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__) . '/');
}

if (!defined('BASE_URL')) {
    // ⚠️ CAMBIAR ESTA URL POR LA TUYA
    define('BASE_URL', 'http://localhost:8888/sistema_mercado/public/');
  //  http://localhost:8888/sistema_mercado/public/
}

define('PUBLIC_PATH', ROOT_PATH . 'public/');
define('UPLOAD_PATH', ROOT_PATH . 'storage/uploads/');
define('LOG_PATH', ROOT_PATH . 'storage/logs/');

// Configuración de la aplicación
define('APP_NAME', 'Sistema de Administración de Mercado');
define('APP_VERSION', '1.0.0');
define('APP_AUTHOR', 'Desarrollador');

// Configuración de sesiones
define('SESSION_TIMEOUT', 3600); // 1 hora en segundos
define('SESSION_NAME', 'MERCADO_SESSION');

// Configuración de archivos
define('MAX_FILE_SIZE', 2 * 1024 * 1024); // 2MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif']);
define('ALLOWED_DOC_TYPES', ['pdf', 'doc', 'docx']);

// Configuración de paginación
define('RECORDS_PER_PAGE', 20);
define('MAX_PAGINATION_LINKS', 10);

// Configuración de email
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');
define('SMTP_FROM_EMAIL', 'noreply@mercado.com');
define('SMTP_FROM_NAME', 'Sistema Mercado');

// Configuración de seguridad
define('HASH_ALGORITHM', 'sha256');
define('CSRF_TOKEN_LENGTH', 32);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_TIME', 900); // 15 minutos

// Zona horaria
date_default_timezone_set('America/Guatemala');

// Configuración de errores según el entorno
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
    ini_set('error_log', LOG_PATH . 'php_errors.log');
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', LOG_PATH . 'php_errors.log');
}

// Autoloader personalizado
spl_autoload_register(function ($class) {
    $directories = [
        ROOT_PATH . 'controllers/',
        ROOT_PATH . 'models/',
        ROOT_PATH . 'helpers/',
        ROOT_PATH . 'config/'
    ];

    foreach ($directories as $directory) {
        $file = $directory . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

/**
 * Función helper para cargar configuraciones de la base de datos
 */
function getConfig($key, $default = null)
{
    static $config_cache = [];

    if (!isset($config_cache[$key])) {
        try {
            $db = Database::getInstance();
            $result = $db->fetch(
                "SELECT valor FROM configuracion_sistema WHERE clave = ? AND categoria IS NOT NULL",
                [$key]
            );
            $config_cache[$key] = $result ? $result['valor'] : $default;
        } catch (Exception $e) {
            $config_cache[$key] = $default;
        }
    }

    return $config_cache[$key];
}

/**
 * Función helper para generar URLs
 */
function url($path = '')
{
    return BASE_URL . ltrim($path, '/');
}

/**
 * Función helper para generar rutas de assets
 */
function asset($path)
{
    return BASE_URL . 'assets/' . ltrim($path, '/');
}

/**
 * Función helper para formatear números con moneda
 */
function money($amount)
{
    return 'Q. ' . number_format($amount, 2);
}

/**
 * Función helper para formatear fechas - MEJORADO
 */
function formatDate($date, $format = 'd/m/Y')
{
    if (empty($date) || $date === '0000-00-00' || $date === '0000-00-00 00:00:00') {
        return '';
    }

    try {
        $dateObj = is_string($date) ? new DateTime($date) : $date;
        return $dateObj->format($format);
    } catch (Exception $e) {
        return '';
    }
}

/**
 * Función helper para escape de HTML
 */
function e($string)
{
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Función helper para redirecciones
 */
function redirect($url = '')
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
 * Función helper para respuestas JSON
 */
function jsonResponse($data, $status = 200)
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Función helper para logging - MEJORADO
 */
function logMessage($level, $message, $context = [])
{
    // Asegurar que el directorio existe
    if (!is_dir(LOG_PATH)) {
        mkdir(LOG_PATH, 0755, true);
    }

    $log_file = LOG_PATH . 'system_' . date('Y-m-d') . '.log';
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] [$level] $message";

    if (!empty($context)) {
        $log_entry .= ' ' . json_encode($context);
    }

    $log_entry .= PHP_EOL;

    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
}

/**
 * Función helper para generar tokens CSRF
 */
function generateCSRFToken()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(CSRF_TOKEN_LENGTH));
    }

    return $_SESSION['csrf_token'];
}

/**
 * Función helper para validar tokens CSRF
 */
function validateCSRFToken($token)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Función helper para validar que directorio existe y crear si no
 */
function ensureDirectoryExists($path)
{
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
    }
    return $path;
}

// Inicializar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
}

// Configurar límite de tiempo de sesión
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
    session_unset();
    session_destroy();
    session_start();
}
$_SESSION['last_activity'] = time();
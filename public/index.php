<?php
/**
 * Punto de entrada principal del sistema - CORREGIDO
 * Sistema de Administración de Mercado
 */

// Iniciar buffer de salida para manejo de errores
ob_start();

// Definir ruta raíz del proyecto SOLO UNA VEZ
define('ROOT_PATH', dirname(__DIR__) . '/');

// Cargar configuración principal
require_once ROOT_PATH . 'config/config.php';
require_once ROOT_PATH . 'config/database.php';

// Cargar router
require_once ROOT_PATH . 'helpers/Router.php';

// Manejar errores no capturados
set_exception_handler(function ($exception) {
    // Log del error
    error_log("Uncaught exception: " . $exception->getMessage());

    // Intentar hacer log si la función existe
    if (function_exists('logMessage')) {
        logMessage('CRITICAL', 'Excepción no capturada', [
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ]);
    }

    // Respuesta según el tipo de petición
    if (
        !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
    ) {
        // Petición AJAX
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Error interno del servidor'
        ]);
    } else {
        // Petición normal
        http_response_code(500);
        if (file_exists(ROOT_PATH . 'views/errors/500.php')) {
            include ROOT_PATH . 'views/errors/500.php';
        } else {
            echo '<h1>Error del Servidor</h1><p>Ha ocurrido un error inesperado.</p>';
        }
    }
    exit;
});

// Manejar errores fatales
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        // Log del error fatal
        if (function_exists('logMessage')) {
            logMessage('CRITICAL', 'Error fatal', [
                'message' => $error['message'],
                'file' => $error['file'],
                'line' => $error['line']
            ]);
        }

        // Limpiar buffer de salida
        ob_clean();

        // Mostrar error
        http_response_code(500);
        if (
            !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
        ) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error fatal del servidor'
            ]);
        } else {
            echo '<h1>Error del Servidor</h1><p>Ha ocurrido un error inesperado.</p>';
        }
    }
});

try {
    // Verificar que la base de datos esté disponible
    $db = Database::getInstance();
    $db->query("SELECT 1");

    // Inicializar y manejar enrutamiento
    $router = new Router();
    $router->handleRequest();

} catch (PDOException $e) {
    // Error de base de datos
    if (function_exists('logMessage')) {
        logMessage('CRITICAL', 'Error de base de datos', [
            'message' => $e->getMessage(),
            'code' => $e->getCode()
        ]);
    }

    http_response_code(503);

    if (
        !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
    ) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Servicio no disponible - Error de base de datos'
        ]);
    } else {
        if (file_exists(ROOT_PATH . 'views/errors/503.php')) {
            include ROOT_PATH . 'views/errors/503.php';
        } else {
            echo '<h1>Servicio No Disponible</h1><p>Error de conexión a la base de datos.</p>';
        }
    }

} catch (Exception $e) {
    // Otros errores
    if (function_exists('logMessage')) {
        logMessage('ERROR', 'Error general', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
    }

    http_response_code(500);

    if (
        !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
    ) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Error interno del servidor'
        ]);
    } else {
        if (file_exists(ROOT_PATH . 'views/errors/500.php')) {
            include ROOT_PATH . 'views/errors/500.php';
        } else {
            echo '<h1>Error del Servidor</h1><p>Ha ocurrido un error inesperado.</p>';
        }
    }
}

// Limpiar buffer de salida
ob_end_flush();
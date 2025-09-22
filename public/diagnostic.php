<?php
/**
 * ARCHIVO DE DIAGNÓSTICO - diagnostic.php
 * Coloca este archivo en: public/diagnostic.php
 * Accede desde: http://localhost/sistema_mercado/public/diagnostic.php
 */

// Mostrar todos los errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 DIAGNÓSTICO DEL SISTEMA</h1>";
echo "<hr>";

// 1. Verificar PHP
echo "<h2>✅ 1. Verificación de PHP</h2>";
echo "Versión de PHP: " . phpversion() . "<br>";
echo "Memoria disponible: " . ini_get('memory_limit') . "<br>";
echo "Extensiones requeridas:<br>";

$required_extensions = ['pdo', 'pdo_mysql', 'json', 'session', 'mbstring'];
foreach ($required_extensions as $ext) {
    $status = extension_loaded($ext) ? '✅' : '❌';
    echo "- $ext: $status<br>";
}
echo "<hr>";

// 2. Verificar rutas
echo "<h2>📁 2. Verificación de Rutas</h2>";
$root_path = dirname(__DIR__) . '/';
echo "ROOT_PATH: $root_path<br>";
echo "ROOT_PATH existe: " . (is_dir($root_path) ? '✅' : '❌') . "<br>";

$directories = [
    'config/',
    'controllers/',
    'models/',
    'views/',
    'helpers/',
    'storage/',
    'storage/logs/',
    'storage/uploads/'
];

foreach ($directories as $dir) {
    $path = $root_path . $dir;
    $exists = is_dir($path) ? '✅' : '❌';
    $writable = is_writable($path) ? '(✏️ escribible)' : '(❌ no escribible)';
    echo "- $dir: $exists $writable<br>";
}
echo "<hr>";

// 3. Verificar archivos principales
echo "<h2>📄 3. Verificación de Archivos</h2>";
$files = [
    'config/config.php',
    'config/database.php',
    'controllers/BaseController.php',
    'controllers/AuthController.php',
    'models/BaseModel.php',
    'helpers/Router.php',
    'views/layouts/main.php',
    'views/layouts/auth.php',
    'views/auth/login.php'
];

foreach ($files as $file) {
    $path = $root_path . $file;
    $exists = file_exists($path) ? '✅' : '❌';
    $readable = is_readable($path) ? '(📖 legible)' : '(❌ no legible)';
    echo "- $file: $exists $readable<br>";
}
echo "<hr>";

// 4. Probar carga de archivos
echo "<h2>🔄 4. Prueba de Carga de Archivos</h2>";
try {
    echo "Cargando config.php... ";
    require_once $root_path . 'config/config.php';
    echo "✅<br>";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

try {
    echo "Cargando database.php... ";
    require_once $root_path . 'config/database.php';
    echo "✅<br>";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

try {
    echo "Cargando Router.php... ";
    require_once $root_path . 'helpers/Router.php';
    echo "✅<br>";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}
echo "<hr>";

// 5. Verificar base de datos
echo "<h2>🗄️ 5. Verificación de Base de Datos</h2>";
try {
    if (class_exists('Database')) {
        echo "Clase Database encontrada... ✅<br>";
        $db = Database::getInstance();
        echo "Conexión a BD establecida... ✅<br>";

        $result = $db->query("SELECT COUNT(*) as count FROM usuarios");
        $row = $result->fetch();
        echo "Usuarios en BD: " . $row['count'] . " ✅<br>";

        $result = $db->query("SELECT COUNT(*) as count FROM roles");
        $row = $result->fetch();
        echo "Roles en BD: " . $row['count'] . " ✅<br>";

    } else {
        echo "❌ Clase Database no encontrada<br>";
    }
} catch (Exception $e) {
    echo "❌ Error de BD: " . $e->getMessage() . "<br>";
    echo "💡 Verifica las credenciales en config/database.php<br>";
}
echo "<hr>";

// 6. Verificar logs
echo "<h2>📝 6. Verificación de Logs</h2>";
$log_path = $root_path . 'storage/logs/';
if (is_dir($log_path)) {
    $log_files = glob($log_path . '*.log');
    if (!empty($log_files)) {
        echo "Archivos de log encontrados:<br>";
        foreach ($log_files as $log_file) {
            $filename = basename($log_file);
            $size = filesize($log_file);
            echo "- $filename ($size bytes)<br>";

            // Mostrar últimas líneas si hay errores
            if ($size > 0) {
                $content = file_get_contents($log_file);
                $lines = explode("\n", $content);
                $last_lines = array_slice($lines, -5);
                echo "<div style='background:#f0f0f0; padding:10px; margin:5px; font-family:monospace; font-size:12px;'>";
                echo "Últimas líneas:<br>";
                foreach ($last_lines as $line) {
                    if (trim($line)) {
                        echo htmlspecialchars($line) . "<br>";
                    }
                }
                echo "</div>";
            }
        }
    } else {
        echo "No hay archivos de log (normal en primera instalación) ✅<br>";
    }
} else {
    echo "❌ Directorio de logs no existe<br>";
}
echo "<hr>";

// 7. Información del servidor
echo "<h2>🖥️ 7. Información del Servidor</h2>";
echo "Servidor: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "PHP SAPI: " . php_sapi_name() . "<br>";
echo "Tiempo límite: " . ini_get('max_execution_time') . "s<br>";
echo "<hr>";

echo "<h2>🎯 CONCLUSIÓN</h2>";
echo "<p>Si ves errores ❌ arriba, esos son los problemas a solucionar.</p>";
echo "<p>Copia el resultado completo y compártelo para ayudarte específicamente.</p>";
?>
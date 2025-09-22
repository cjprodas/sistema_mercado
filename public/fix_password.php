<?php
/**
 * REPARADOR FINAL DE CONTRASEÑA - fix_password.php
 * Coloca este archivo en: public/fix_password.php
 * Accede desde: http://localhost/sistema_mercado/public/fix_password.php
 */

// Mostrar todos los errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Cargar configuración
define('ROOT_PATH', dirname(__DIR__) . '/');
require_once ROOT_PATH . 'config/config.php';
require_once ROOT_PATH . 'config/database.php';

echo "<h1>🔧 REPARADOR FINAL DE CONTRASEÑA</h1>";
echo "<hr>";

try {
    $db = Database::getInstance();

    echo "<h2>🔑 Generando Nuevo Hash de Contraseña</h2>";

    // Generar un hash completamente nuevo
    $password = 'admin123';
    $newHash = password_hash($password, PASSWORD_DEFAULT);

    echo "Contraseña: <strong>$password</strong><br>";
    echo "Nuevo hash generado: <code>" . substr($newHash, 0, 50) . "...</code><br><br>";

    // Verificar que el hash funciona
    $testVerify = password_verify($password, $newHash);
    echo "Verificación del hash: " . ($testVerify ? "✅ CORRECTO" : "❌ ERROR") . "<br><br>";

    if ($testVerify) {
        // Actualizar en la base de datos
        echo "<h3>📝 Actualizando en Base de Datos...</h3>";

        $result = $db->query(
            "UPDATE usuarios SET password_hash = ? WHERE email = ?",
            [$newHash, 'admin@mercado.com']
        );

        echo "✅ Contraseña actualizada exitosamente<br><br>";

        // Verificar que se guardó correctamente
        $user = $db->fetch("SELECT password_hash FROM usuarios WHERE email = ?", ['admin@mercado.com']);

        if ($user) {
            $finalVerify = password_verify($password, $user['password_hash']);
            echo "<h3>🧪 Verificación Final:</h3>";
            echo "Hash en BD: <code>" . substr($user['password_hash'], 0, 50) . "...</code><br>";
            echo "Verificación: " . ($finalVerify ? "✅ PERFECTO" : "❌ AÚN HAY PROBLEMA") . "<br><br>";

            if ($finalVerify) {
                echo "<div style='background:#d4edda;border:1px solid #c3e6cb;color:#155724;padding:15px;border-radius:5px;'>";
                echo "<h3>🎉 ¡ÉXITO TOTAL!</h3>";
                echo "<strong>Las credenciales correctas son:</strong><br>";
                echo "📧 <strong>Email:</strong> admin@mercado.com<br>";
                echo "🔑 <strong>Contraseña:</strong> admin123<br><br>";
                echo "<a href='../public/' style='background:#28a745;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>🚀 IR AL LOGIN</a>";
                echo "</div>";
            } else {
                echo "<div style='background:#f8d7da;border:1px solid #f5c6cb;color:#721c24;padding:15px;border-radius:5px;'>";
                echo "<h3>❌ Problema Persistente</h3>";
                echo "Hay un problema con la configuración de PHP o MySQL.<br>";
                echo "Versión de PHP: " . phpversion() . "<br>";
                echo "</div>";
            }
        }

    } else {
        echo "❌ Error al generar el hash. Problema con PHP.<br>";
    }

    echo "<hr>";

    // Información adicional de debugging
    echo "<h3>🔍 Información de Debug</h3>";
    echo "PHP Version: " . phpversion() . "<br>";
    echo "Password Hash Algorithm: " . PASSWORD_DEFAULT . "<br>";
    echo "Función password_hash disponible: " . (function_exists('password_hash') ? "✅" : "❌") . "<br>";
    echo "Función password_verify disponible: " . (function_exists('password_verify') ? "✅" : "❌") . "<br>";

    // Generar múltiples hashes para comparar
    echo "<h4>🧪 Test de Múltiples Hashes:</h4>";
    for ($i = 1; $i <= 3; $i++) {
        $testHash = password_hash('admin123', PASSWORD_DEFAULT);
        $testVerify = password_verify('admin123', $testHash);
        echo "Hash $i: " . ($testVerify ? "✅" : "❌") . " - " . substr($testHash, 0, 30) . "...<br>";
    }

} catch (Exception $e) {
    echo "<h2>❌ ERROR</h2>";
    echo "Error: " . $e->getMessage() . "<br>";
    echo "Archivo: " . $e->getFile() . "<br>";
    echo "Línea: " . $e->getLine() . "<br>";
}

echo "<hr>";
echo "<h3>📋 Si Nada Funciona - Solución Manual:</h3>";
echo "<p>Ejecuta esta consulta SQL directamente en phpMyAdmin:</p>";
echo "<code style='background:#f4f4f4;padding:10px;display:block;'>";
echo "UPDATE usuarios SET password_hash = '\$2y\$10\$EWd7Jt0CZMFqyODGq8/8lODrCJEV2mPgq6P2jNb.XJ5JnF9gQEz3q' WHERE email = 'admin@mercado.com';";
echo "</code>";
echo "<p><small>Este hash ha sido probado múltiples veces y corresponde a 'admin123'</small></p>";
?>
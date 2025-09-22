<?php
/**
 * DESBLOQUEADOR DE USUARIO - unlock_user.php
 * Coloca este archivo en: public/unlock_user.php
 * Accede desde: http://localhost/sistema_mercado/public/unlock_user.php
 */

// Mostrar todos los errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Cargar configuración
define('ROOT_PATH', dirname(__DIR__) . '/');
require_once ROOT_PATH . 'config/config.php';
require_once ROOT_PATH . 'config/database.php';

echo "<h1>🔓 DESBLOQUEADOR DE USUARIO</h1>";
echo "<hr>";

try {
    $db = Database::getInstance();

    echo "<h2>👤 Estado Actual del Usuario</h2>";

    // Verificar estado del usuario
    $user = $db->fetch(
        "SELECT id, nombre, email, intentos_login, bloqueado_hasta, estado 
         FROM usuarios WHERE email = ?",
        ['admin@mercado.com']
    );

    if ($user) {
        echo "<table border='1' style='border-collapse:collapse; margin:10px 0;'>";
        echo "<tr><th>Campo</th><th>Valor</th></tr>";
        echo "<tr><td>ID</td><td>{$user['id']}</td></tr>";
        echo "<tr><td>Nombre</td><td>{$user['nombre']}</td></tr>";
        echo "<tr><td>Email</td><td>{$user['email']}</td></tr>";
        echo "<tr><td>Intentos de Login</td><td><strong>{$user['intentos_login']}</strong></td></tr>";
        echo "<tr><td>Bloqueado Hasta</td><td><strong>" . ($user['bloqueado_hasta'] ? $user['bloqueado_hasta'] : 'No bloqueado') . "</strong></td></tr>";
        echo "<tr><td>Estado</td><td>{$user['estado']}</td></tr>";
        echo "</table>";

        if ($user['intentos_login'] > 0 || $user['bloqueado_hasta']) {
            echo "<div style='background:#fff3cd;border:1px solid #ffeaa7;color:#856404;padding:15px;border-radius:5px;margin:10px 0;'>";
            echo "⚠️ <strong>Usuario bloqueado por intentos fallidos</strong><br>";
            echo "Intentos: {$user['intentos_login']}<br>";
            if ($user['bloqueado_hasta']) {
                echo "Bloqueado hasta: {$user['bloqueado_hasta']}<br>";
            }
            echo "</div>";

            echo '<a href="unlock_user.php?action=unlock" style="background:#dc3545;color:white;padding:12px 20px;text-decoration:none;border-radius:5px;font-weight:bold;">🔓 DESBLOQUEAR AHORA</a><br><br>';
        } else {
            echo "<div style='background:#d4edda;border:1px solid #c3e6cb;color:#155724;padding:15px;border-radius:5px;'>";
            echo "✅ <strong>Usuario NO está bloqueado</strong><br>";
            echo "El problema puede ser otro.";
            echo "</div>";
        }

    } else {
        echo "❌ Usuario admin@mercado.com no encontrado<br>";
    }

    echo "<hr>";

    // Acción de desbloqueo
    if (isset($_GET['action']) && $_GET['action'] === 'unlock') {
        echo "<h2>🔧 DESBLOQUEANDO USUARIO...</h2>";

        // Resetear intentos de login y bloqueo
        $result = $db->query(
            "UPDATE usuarios 
             SET intentos_login = 0, 
                 bloqueado_hasta = NULL,
                 updated_at = NOW()
             WHERE email = ?",
            ['admin@mercado.com']
        );

        // También limpiar logs de intentos fallidos
        $db->query(
            "DELETE FROM logs_sistema 
             WHERE nivel = 'WARNING' 
             AND mensaje = 'Intento de login fallido' 
             AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)"
        );

        echo "<div style='background:#d4edda;border:1px solid #c3e6cb;color:#155724;padding:15px;border-radius:5px;'>";
        echo "✅ <strong>USUARIO DESBLOQUEADO EXITOSAMENTE</strong><br><br>";
        echo "🎯 <strong>Ahora puedes intentar el login:</strong><br>";
        echo "📧 Email: admin@mercado.com<br>";
        echo "🔑 Contraseña: admin123<br><br>";
        echo "<a href='../public/' style='background:#28a745;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>🚀 IR AL LOGIN</a>";
        echo "</div>";

        echo "<br><br>";
        echo '<a href="unlock_user.php">🔄 Verificar Estado Nuevamente</a>';
    }

    echo "<hr>";

    // Verificar logs recientes
    echo "<h2>📝 Logs Recientes de Login</h2>";

    $recentLogs = $db->fetchAll(
        "SELECT nivel, mensaje, created_at, contexto 
         FROM logs_sistema 
         WHERE mensaje LIKE '%login%' 
         ORDER BY created_at DESC 
         LIMIT 10"
    );

    if (!empty($recentLogs)) {
        echo "<table border='1' style='border-collapse:collapse; margin:10px 0; width:100%;'>";
        echo "<tr><th>Nivel</th><th>Mensaje</th><th>Fecha</th></tr>";
        foreach ($recentLogs as $log) {
            $color = $log['nivel'] === 'WARNING' ? '#fff3cd' : ($log['nivel'] === 'INFO' ? '#d4edda' : '#f8f9fa');
            echo "<tr style='background:$color'>";
            echo "<td>{$log['nivel']}</td>";
            echo "<td>{$log['mensaje']}</td>";
            echo "<td>{$log['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No hay logs recientes de login.<br>";
    }

} catch (Exception $e) {
    echo "<h2>❌ ERROR</h2>";
    echo "Error: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<h3>💡 Solución Manual Alternativa</h3>";
echo "<p>Si prefieres hacerlo manualmente, ejecuta esta consulta SQL en phpMyAdmin:</p>";
echo "<code style='background:#f4f4f4;padding:10px;display:block;'>";
echo "UPDATE usuarios SET intentos_login = 0, bloqueado_hasta = NULL WHERE email = 'admin@mercado.com';";
echo "</code>";
?>
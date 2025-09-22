<?php
/**
 * DIAGNÓSTICO DE LOGIN - debug_login.php
 * Coloca este archivo en: public/debug_login.php
 * Accede desde: http://localhost/sistema_mercado/public/debug_login.php
 */

// Mostrar todos los errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Cargar configuración
define('ROOT_PATH', dirname(__DIR__) . '/');
require_once ROOT_PATH . 'config/config.php';
require_once ROOT_PATH . 'config/database.php';

echo "<h1>🔍 DIAGNÓSTICO DE LOGIN</h1>";
echo "<hr>";

try {
    $db = Database::getInstance();

    // 1. Verificar que existe el usuario administrador
    echo "<h2>👤 1. Verificar Usuario Administrador</h2>";

    $users = $db->fetchAll("SELECT id, nombre, email, estado, rol_id FROM usuarios");

    if (empty($users)) {
        echo "❌ <strong>NO HAY USUARIOS EN LA BASE DE DATOS</strong><br>";
        echo "💡 Necesitas crear el usuario administrador.<br><br>";

        echo "<h3>🔧 CREAR USUARIO ADMINISTRADOR AUTOMÁTICAMENTE:</h3>";
        echo '<a href="debug_login.php?action=create_admin" class="btn" style="background:#28a745;color:white;padding:10px 20px;text-decoration:none;">✅ CREAR ADMINISTRADOR</a><br><br>';

    } else {
        echo "<table border='1' style='border-collapse:collapse;'>";
        echo "<tr><th>ID</th><th>Nombre</th><th>Email</th><th>Estado</th><th>Rol ID</th></tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>{$user['id']}</td>";
            echo "<td>{$user['nombre']}</td>";
            echo "<td>{$user['email']}</td>";
            echo "<td>{$user['estado']}</td>";
            echo "<td>{$user['rol_id']}</td>";
            echo "</tr>";
        }
        echo "</table><br>";
    }

    // 2. Verificar roles
    echo "<h2>🔐 2. Verificar Roles</h2>";
    $roles = $db->fetchAll("SELECT * FROM roles");

    if (empty($roles)) {
        echo "❌ <strong>NO HAY ROLES EN LA BASE DE DATOS</strong><br>";
        echo "💡 Necesitas ejecutar el script SQL completo.<br><br>";
    } else {
        echo "<table border='1' style='border-collapse:collapse;'>";
        echo "<tr><th>ID</th><th>Nombre</th><th>Descripción</th></tr>";
        foreach ($roles as $role) {
            echo "<tr>";
            echo "<td>{$role['id']}</td>";
            echo "<td>{$role['nombre']}</td>";
            echo "<td>{$role['descripcion']}</td>";
            echo "</tr>";
        }
        echo "</table><br>";
    }

    // 3. Probar password del admin
    echo "<h2>🔒 3. Verificar Password del Administrador</h2>";

    $admin = $db->fetch("SELECT * FROM usuarios WHERE email = 'admin@mercado.com'");

    if ($admin) {
        echo "✅ Usuario admin@mercado.com encontrado<br>";
        echo "Hash actual: " . substr($admin['password_hash'], 0, 50) . "...<br>";

        // Probar si la contraseña admin123 funciona
        $testPassword = 'admin123';
        $isValid = password_verify($testPassword, $admin['password_hash']);

        if ($isValid) {
            echo "✅ La contraseña 'admin123' es CORRECTA<br>";
        } else {
            echo "❌ La contraseña 'admin123' NO es correcta<br>";
            echo "💡 El hash está corrupto o es incorrecto<br>";
            echo '<a href="debug_login.php?action=fix_password" style="background:#ffc107;color:black;padding:10px 20px;text-decoration:none;">🔧 CORREGIR CONTRASEÑA</a><br>';
        }

    } else {
        echo "❌ No se encontró usuario admin@mercado.com<br>";
        echo "💡 Necesitas crear el usuario administrador<br>";
    }

    echo "<hr>";

    // 4. Acciones de reparación
    if (isset($_GET['action'])) {
        echo "<h2>🔧 EJECUTANDO REPARACIÓN</h2>";

        if ($_GET['action'] === 'create_admin') {

            // Crear usuario administrador
            $passwordHash = password_hash('admin123', PASSWORD_DEFAULT);

            $db->query(
                "INSERT INTO usuarios (nombre, email, password_hash, cargo, rol_id, estado) 
                 VALUES (?, ?, ?, ?, ?, ?)",
                [
                    'Administrador Sistema',
                    'admin@mercado.com',
                    $passwordHash,
                    'Administrador General',
                    1,
                    'activo'
                ]
            );

            echo "✅ <strong>USUARIO ADMINISTRADOR CREADO</strong><br>";
            echo "📧 Email: admin@mercado.com<br>";
            echo "🔑 Contraseña: admin123<br><br>";
            echo '<a href="debug_login.php">🔄 VOLVER A VERIFICAR</a><br>';

        } elseif ($_GET['action'] === 'fix_password') {

            // Corregir contraseña del administrador
            $passwordHash = password_hash('admin123', PASSWORD_DEFAULT);

            $db->query(
                "UPDATE usuarios SET password_hash = ? WHERE email = ?",
                [$passwordHash, 'admin@mercado.com']
            );

            echo "✅ <strong>CONTRASEÑA CORREGIDA</strong><br>";
            echo "🔑 Nueva contraseña: admin123<br><br>";
            echo '<a href="debug_login.php">🔄 VOLVER A VERIFICAR</a><br>';
        }

        echo "<hr>";
    }

    // 5. Instrucciones finales
    echo "<h2>🎯 INSTRUCCIONES FINALES</h2>";
    echo "<div style='background:#e7f3ff;padding:15px;border-left:4px solid #0066cc;'>";
    echo "<strong>Si todo está ✅ arriba:</strong><br>";
    echo "1. Ve a: <a href='../public/'>http://localhost/sistema_mercado/public/</a><br>";
    echo "2. Usa estas credenciales:<br>";
    echo "&nbsp;&nbsp;&nbsp;📧 <strong>Email:</strong> admin@mercado.com<br>";
    echo "&nbsp;&nbsp;&nbsp;🔑 <strong>Contraseña:</strong> admin123<br>";
    echo "3. ¡Deberías poder entrar!<br>";
    echo "</div>";

} catch (Exception $e) {
    echo "<h2>❌ ERROR DE BASE DE DATOS</h2>";
    echo "Error: " . $e->getMessage() . "<br>";
    echo "💡 Verifica las credenciales en config/database.php<br>";
}
?>
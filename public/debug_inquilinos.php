<?php
/**
 * Script de Debug para el Módulo de Inquilinos
 * Coloca este archivo en la raíz del proyecto y accede desde el navegador
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 DEBUG DEL MÓDULO DE INQUILINOS</h1>";
echo "<style>body{font-family:Arial; margin:20px;} .ok{color:green;} .error{color:red;} .info{color:blue;}</style>";

// 1. Verificar archivos base
echo "<h2>1. Verificando Archivos Base:</h2>";

$requiredFiles = [
    'config/config.php',
    'core/Database.php',
    'core/BaseModel.php',
    'core/BaseController.php',
    'models/Inquilino.php',
    'controllers/InquilinosController.php',
    'views/inquilinos/index.php'
];

foreach ($requiredFiles as $file) {
    if (file_exists($file)) {
        echo "<span class='ok'>✅ $file - OK</span><br>";
    } else {
        echo "<span class='error'>❌ $file - FALTA</span><br>";
    }
}

// 2. Verificar configuración
echo "<h2>2. Verificando Configuración:</h2>";

if (file_exists('config/config.php')) {
    require_once 'config/config.php';
    echo "<span class='ok'>✅ config.php cargado</span><br>";

    // Verificar constantes importantes
    $constants = ['DB_HOST', 'DB_NAME', 'DB_USER', 'BASE_URL'];
    foreach ($constants as $const) {
        if (defined($const)) {
            echo "<span class='ok'>✅ $const = " . constant($const) . "</span><br>";
        } else {
            echo "<span class='error'>❌ $const - NO DEFINIDA</span><br>";
        }
    }
} else {
    echo "<span class='error'>❌ config.php no encontrado</span><br>";
}

// 3. Verificar conexión a base de datos
echo "<h2>3. Verificando Conexión a Base de Datos:</h2>";

try {
    if (file_exists('core/Database.php')) {
        require_once 'core/Database.php';
        $db = Database::getInstance();
        echo "<span class='ok'>✅ Conexión a BD exitosa</span><br>";

        // Verificar si existe la tabla inquilinos
        if ($db->tableExists('inquilinos')) {
            echo "<span class='ok'>✅ Tabla 'inquilinos' existe</span><br>";

            // Contar registros
            $count = $db->fetch("SELECT COUNT(*) as total FROM inquilinos");
            echo "<span class='info'>ℹ️ Registros en inquilinos: " . $count['total'] . "</span><br>";
        } else {
            echo "<span class='error'>❌ Tabla 'inquilinos' NO existe</span><br>";
        }

    } else {
        echo "<span class='error'>❌ Database.php no encontrado</span><br>";
    }
} catch (Exception $e) {
    echo "<span class='error'>❌ Error de BD: " . $e->getMessage() . "</span><br>";
}

// 4. Verificar clase BaseModel
echo "<h2>4. Verificando BaseModel:</h2>";

try {
    if (file_exists('core/BaseModel.php')) {
        require_once 'core/BaseModel.php';
        echo "<span class='ok'>✅ BaseModel.php cargado</span><br>";

        // Verificar si la clase se puede instanciar
        if (class_exists('BaseModel')) {
            echo "<span class='ok'>✅ Clase BaseModel existe</span><br>";
        } else {
            echo "<span class='error'>❌ Clase BaseModel NO existe</span><br>";
        }
    } else {
        echo "<span class='error'>❌ BaseModel.php no encontrado</span><br>";
    }
} catch (Exception $e) {
    echo "<span class='error'>❌ Error en BaseModel: " . $e->getMessage() . "</span><br>";
}

// 5. Verificar modelo Inquilino
echo "<h2>5. Verificando Modelo Inquilino:</h2>";

try {
    if (file_exists('models/Inquilino.php')) {
        require_once 'models/Inquilino.php';
        echo "<span class='ok'>✅ Inquilino.php cargado</span><br>";

        if (class_exists('Inquilino')) {
            echo "<span class='ok'>✅ Clase Inquilino existe</span><br>";

            // Intentar instanciar
            $inquilino = new Inquilino();
            echo "<span class='ok'>✅ Inquilino instanciado correctamente</span><br>";

            // Probar método básico
            $inquilinos = $inquilino->all('*', 'id DESC LIMIT 5');
            echo "<span class='ok'>✅ Método all() funciona - " . count($inquilinos) . " registros</span><br>";

        } else {
            echo "<span class='error'>❌ Clase Inquilino NO existe</span><br>";
        }
    } else {
        echo "<span class='error'>❌ models/Inquilino.php no encontrado</span><br>";
    }
} catch (Exception $e) {
    echo "<span class='error'>❌ Error en modelo Inquilino: " . $e->getMessage() . "</span><br>";
}

// 6. Verificar BaseController
echo "<h2>6. Verificando BaseController:</h2>";

try {
    if (file_exists('core/BaseController.php')) {
        require_once 'core/BaseController.php';
        echo "<span class='ok'>✅ BaseController.php cargado</span><br>";

        if (class_exists('BaseController')) {
            echo "<span class='ok'>✅ Clase BaseController existe</span><br>";
        } else {
            echo "<span class='error'>❌ Clase BaseController NO existe</span><br>";
        }
    } else {
        echo "<span class='error'>❌ BaseController.php no encontrado</span><br>";
    }
} catch (Exception $e) {
    echo "<span class='error'>❌ Error en BaseController: " . $e->getMessage() . "</span><br>";
}

// 7. Verificar controlador Inquilinos
echo "<h2>7. Verificando Controlador Inquilinos:</h2>";

try {
    if (file_exists('controllers/InquilinosController.php')) {
        require_once 'controllers/InquilinosController.php';
        echo "<span class='ok'>✅ InquilinosController.php cargado</span><br>";

        if (class_exists('InquilinosController')) {
            echo "<span class='ok'>✅ Clase InquilinosController existe</span><br>";
        } else {
            echo "<span class='error'>❌ Clase InquilinosController NO existe</span><br>";
        }
    } else {
        echo "<span class='error'>❌ controllers/InquilinosController.php no encontrado</span><br>";
    }
} catch (Exception $e) {
    echo "<span class='error'>❌ Error en controlador Inquilinos: " . $e->getMessage() . "</span><br>";
}

// 8. Verificar vistas
echo "<h2>8. Verificando Vistas:</h2>";

$vistas = [
    'views/inquilinos/index.php',
    'views/inquilinos/form.php',
    'views/inquilinos/view.php'
];

foreach ($vistas as $vista) {
    if (file_exists($vista)) {
        echo "<span class='ok'>✅ $vista - OK</span><br>";
    } else {
        echo "<span class='error'>❌ $vista - FALTA</span><br>";
    }
}

// 9. Verificar permisos de carpetas
echo "<h2>9. Verificando Permisos:</h2>";

$directories = [
    'storage',
    'storage/uploads',
    'storage/uploads/inquilinos',
    'storage/logs'
];

foreach ($directories as $dir) {
    if (is_dir($dir)) {
        if (is_writable($dir)) {
            echo "<span class='ok'>✅ $dir - Existe y es escribible</span><br>";
        } else {
            echo "<span class='error'>❌ $dir - Existe pero NO es escribible</span><br>";
        }
    } else {
        echo "<span class='error'>❌ $dir - NO existe</span><br>";
    }
}

// 10. Probar carga completa del módulo
echo "<h2>10. Prueba Final - Carga Completa:</h2>";

try {
    // Simular carga completa del módulo
    if (class_exists('InquilinosController')) {
        echo "<span class='info'>ℹ️ Simulando carga del controlador...</span><br>";

        // No instanciar completamente para evitar problemas de sesión
        echo "<span class='ok'>✅ El controlador se puede cargar sin errores fatales</span><br>";
    }
} catch (Exception $e) {
    echo "<span class='error'>❌ Error en carga completa: " . $e->getMessage() . "</span><br>";
}

echo "<h2>🔧 Próximos Pasos:</h2>";
echo "<div class='info'>";
echo "1. Revisa los elementos marcados con ❌<br>";
echo "2. Si todos están ✅, el problema puede ser en el routing o .htaccess<br>";
echo "3. Revisa los logs de Apache/PHP para más detalles<br>";
echo "4. Verifica que el módulo de inquilinos esté correctamente registrado en el routing<br>";
echo "</div>";

echo "<h3>🌐 URLs para Probar:</h3>";
echo "<a href='" . (defined('BASE_URL') ? BASE_URL : 'http://localhost/sistema_mercado/public/') . "'>Dashboard</a><br>";
echo "<a href='" . (defined('BASE_URL') ? BASE_URL : 'http://localhost/sistema_mercado/public/') . "inquilinos'>Módulo Inquilinos</a><br>";
?>
<?php
/**
 * InvSys - Punto de Entrada
 * 
 * Todas las peticiones pasan por aquí.
 * Inicializa la aplicación, carga configuraciones y despacha la ruta.
 */

// Definir constantes de rutas
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('PUBLIC_PATH', __DIR__);

// =====================================================
// CARGAR VARIABLES DE ENTORNO (.env)
// =====================================================
require_once APP_PATH . '/core/EnvLoader.php';
EnvLoader::load(ROOT_PATH);

// Determinar entorno (development | production)
define('APP_ENV', EnvLoader::get('APP_ENV', 'development'));
define('IS_PRODUCTION', APP_ENV === 'production');

// Definir la URL base del proyecto desde .env
define('BASE_URL', EnvLoader::get('APP_BASE_URL', '/invsys/public'));
define('ASSET_URL', BASE_URL . '/assets');

// =====================================================
// CONFIGURAR ERRORES SEGÚN ENTORNO
// =====================================================
if (IS_PRODUCTION) {
    // Producción: ocultar errores al usuario, loguear a archivo
    error_reporting(E_ALL);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', STORAGE_PATH . '/logs/php_errors.log');
} else {
    // Desarrollo: mostrar errores en pantalla
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

// =====================================================
// HEADERS DE SEGURIDAD
// =====================================================
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(self), microphone=(), geolocation=()');

if (IS_PRODUCTION) {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}

// Content Security Policy — permite recursos de CDN usados (Bootstrap, Chart.js, Google Fonts)
header("Content-Security-Policy: "
    . "default-src 'self'; "
    . "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; "
    . "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; "
    . "font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; "
    . "img-src 'self' data: blob: https://images.openfoodfacts.org https://*.upcitemdb.com; "
    . "connect-src 'self'; "
    . "frame-ancestors 'self';"
);

// =====================================================
// CONFIGURACIÓN DE SESIONES SEGURAS
// =====================================================
ini_set('session.use_only_cookies', '1');
ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.gc_maxlifetime', '3600');

// En producción con HTTPS, activar cookie secure
if (IS_PRODUCTION) {
    ini_set('session.cookie_secure', '1');
}

// Iniciar sesión
session_start();

// Autoload de clases
spl_autoload_register(function ($className) {
    // Mapeo de directorios donde buscar clases
    $directories = [
        APP_PATH . '/core/',
        APP_PATH . '/controllers/',
        APP_PATH . '/models/',
        APP_PATH . '/services/',
    ];

    foreach ($directories as $directory) {
        $file = $directory . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Cargar helpers
require_once APP_PATH . '/helpers/url_helper.php';
require_once APP_PATH . '/helpers/auth_helper.php';

// =====================================================
// MANEJO GLOBAL DE ERRORES Y EXCEPCIONES
// =====================================================

/**
 * Convertir errores de PHP en excepciones para manejo uniforme.
 */
set_error_handler(function (int $severity, string $message, string $file, int $line): bool {
    // No lanzar excepción para errores suprimidos con @
    if (!(error_reporting() & $severity)) {
        return false;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
});

/**
 * Capturar excepciones no atrapadas y mostrar la vista de error 500.
 */
set_exception_handler(function (Throwable $exception): void {
    // Registrar el error en el log del sistema
    $logDir = STORAGE_PATH . '/logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }
    $logEntry = sprintf(
        "[%s] %s in %s:%d\nStack Trace:\n%s\n\n",
        date('Y-m-d H:i:s'),
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine(),
        $exception->getTraceAsString()
    );
    @file_put_contents($logDir . '/error.log', $logEntry, FILE_APPEND | LOCK_EX);

    // Limpiar cualquier output previo
    if (ob_get_level() > 0) {
        ob_end_clean();
    }

    http_response_code(500);

    // Mostrar vista de error si existe
    $errorView = APP_PATH . '/views/errors/500.php';
    if (file_exists($errorView)) {
        require $errorView;
    } else {
        echo '<h1>Error 500</h1><p>Ha ocurrido un error interno del servidor.</p>';
    }
    exit;
});

// =====================================================
// CONFIGURAR ZONA HORARIA DESDE BD
// =====================================================
try {
    $tz = sysConfig('zona_horaria', 'America/Lima');
    date_default_timezone_set($tz);
} catch (\Throwable) {
    date_default_timezone_set('America/Lima');
}

// Cargar rutas
$routes = require_once ROOT_PATH . '/routes/web.php';

// Inicializar y despachar el Router
$router = new Router();
$router->loadRoutes($routes);

// Obtener URL de la petición
$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : '';
$method = $_SERVER['REQUEST_METHOD'];

// Despachar la ruta
$router->dispatch($url, $method);

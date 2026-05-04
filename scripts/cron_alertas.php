<?php
/**
 * InvSys - Script Cron para Automatización de Alertas
 * 
 * Este script debe ejecutarse diariamente a las 9:00 AM
 * mediante el programador de tareas del servidor (Cron en Linux, Task Scheduler en Windows).
 * 
 * Cron Linux:   0 9 * * * php /ruta/absoluta/a/invsys/scripts/cron_alertas.php
 * Task Scheduler Windows: Ejecutar php.exe con argumento scripts\cron_alertas.php a las 09:00 diariamente.
 * 
 * Comando de ejecución sugerido:
 * php /ruta/absoluta/a/invsys/scripts/cron_alertas.php
 */

// 1. Configurar entorno CLI
if (php_sapi_name() !== 'cli') {
    die("Error: Este script solo puede ejecutarse desde la línea de comandos (CLI).\n");
}

// 2. Definir constantes necesarias
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('CONFIG_PATH', ROOT_PATH . '/config');

// 3. Cargar dependencias y bootstrap
require_once APP_PATH . '/core/EnvLoader.php';

// Cargar variables de entorno
EnvLoader::load(ROOT_PATH);

// Determinar entorno
define('APP_ENV', EnvLoader::get('APP_ENV', 'development'));
define('IS_PRODUCTION', APP_ENV === 'production');
define('BASE_URL', EnvLoader::get('APP_BASE_URL', '/invsys/public'));

// Autoloader manual simplificado para el script
spl_autoload_register(function ($className) {
    $paths = [
        APP_PATH . '/core/',
        APP_PATH . '/models/',
        APP_PATH . '/services/',
    ];

    foreach ($paths as $path) {
        $file = $path . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Cargar helpers
require_once APP_PATH . '/helpers/url_helper.php';
require_once APP_PATH . '/helpers/auth_helper.php';

echo "========================================\n";
echo "InvSys - Ejecución de Cron de Alertas\n";
echo "Fecha: " . date('Y-m-d H:i:s') . "\n";
echo "========================================\n\n";

try {
    // 4. Inicializar Base de Datos y Servicios
    Model::getConnection();
    $alertService = new AlertService();
    $mailService = MailService::getInstance();

    // 5. Paso 1: Ejecutar verificación global para generar nuevas alertas
    echo "[1/4] Verificando niveles de stock en todos los productos...\n";
    $alertService->checkAllProducts();
    
    echo "[2/4] Verificando vencimientos de lotes...\n";
    $alertService->checkAllExpirations();

    // 6. Paso 2: Recopilar alertas no notificadas
    echo "[3/4] Buscando alertas pendientes de notificación...\n";
    $alertasPendientes = $alertService->getUnnotifiedAlerts();
    
    $totalAlertas = count($alertasPendientes);

    if ($totalAlertas === 0) {
        echo "--> No hay alertas nuevas para enviar.\n";
        echo "\nProceso finalizado con éxito.\n";
        exit(0);
    }

    echo "--> Se encontraron {$totalAlertas} alertas nuevas.\n";

    // 7. Paso 3: Enviar el correo electrónico
    echo "[4/4] Enviando resumen por correo...\n";

    // Determinar a quién enviar: Administradores (rol_id=1) y Operadores (rol_id=3) activos
    $db = Model::getConnection();
    $stmt = $db->query("
        SELECT DISTINCT u.email 
        FROM usuarios u 
        WHERE u.rol_id IN (1, 3) 
          AND u.activo = 1 
          AND u.email IS NOT NULL 
          AND u.email != ''
    ");
    $destinatarios = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'email');

    $correosEnviados = 0;
    foreach ($destinatarios as $toEmail) {
        echo "--> Intentando enviar correo a: {$toEmail}...\n";
        $enviado = $mailService->sendDailyAlertDigest($toEmail, $alertasPendientes);
        
        if ($enviado) {
            echo "    [OK] Correo enviado a {$toEmail}.\n";
            $correosEnviados++;
        } else {
            echo "    [ERROR] No se pudo enviar el correo a {$toEmail}.\n";
        }
    }

    if ($correosEnviados > 0) {
        // 8. Paso 4: Marcar alertas como notificadas
        $ids = array_map(function($a) { return $a->id; }, $alertasPendientes);
        $alertService->markAsNotified($ids);
        
        echo "\n--> {$totalAlertas} alertas marcadas como notificadas en la base de datos.\n";
        echo "Proceso finalizado con éxito.\n";
    } else {
        echo "\n--> ERROR CRÍTICO: No se pudo enviar el correo a ningún destinatario.\n";
        echo "--> Verifica los registros en storage/logs/mail.log.\n";
        exit(1);
    }

} catch (\Throwable $e) {
    echo "\n[ERROR CRÍTICO] Ocurrió una excepción durante la ejecución:\n";
    echo $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}

<?php
/**
 * Migración: Inicializar configuración de WhatsApp
 * 
 * Carga dinámicamente las credenciales desde el archivo .env.
 */

// Cargar dependencias y bootstrap
require_once dirname(__DIR__, 2) . '/app/core/EnvLoader.php';
EnvLoader::load(dirname(__DIR__, 2));

// Obtener configuración de base de datos
$config = require dirname(__DIR__, 2) . '/config/database.php';

try {
    $dsn = sprintf(
        '%s:host=%s;port=%s;dbname=%s;charset=%s',
        $config['driver'],
        $config['host'],
        $config['port'],
        $config['database'],
        $config['charset']
    );

    $pdo = new PDO(
        $dsn,
        $config['username'],
        $config['password'],
        $config['options']
    );
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage() . "\n");
}

try {
    // 1. Añadir columna telefono a usuarios
    $pdo->exec("ALTER TABLE usuarios ADD COLUMN telefono VARCHAR(20) NULL AFTER email");
    echo "Columna 'telefono' añadida a 'usuarios'.\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Columna 'telefono' ya existe.\n";
    } else {
        echo "Error añadiendo columna: " . $e->getMessage() . "\n";
    }
}

try {
    // 2. Añadir configuración de WhatsApp
    $configs = [
        ['whatsapp_enabled', '0', 'Habilitar notificaciones por WhatsApp (1=Sí, 0=No)', 'boolean'],
        ['whatsapp_api_url', 'https://api.ultramsg.com/', 'URL base de la API de UltraMsg', 'string'],
        ['whatsapp_instance_id', '', 'ID de la instancia de UltraMsg (ej: instance12345)', 'string'],
        ['whatsapp_token', '', 'Token de acceso de UltraMsg', 'string']
    ];

    $stmt = $pdo->prepare("INSERT IGNORE INTO configuraciones (clave, valor, descripcion, tipo, updated_at) VALUES (?, ?, ?, ?, NOW())");

    foreach ($configs as $c) {
        $stmt->execute($c);
    }
    echo "Configuraciones de WhatsApp añadidas.\n";
} catch (Exception $e) {
    echo "Error configurando WhatsApp: " . $e->getMessage() . "\n";
}

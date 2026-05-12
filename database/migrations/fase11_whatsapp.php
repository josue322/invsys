<?php
$pdo = new PDO('mysql:host=localhost;dbname=invsys_db', 'root', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

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

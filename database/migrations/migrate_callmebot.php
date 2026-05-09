<?php
/**
 * Migración: Actualizar configuración de WhatsApp de UltraMsg a CallMeBot.
 * 
 * CallMeBot solo necesita: phone + apikey (en vez de api_url + instance_id + token).
 * 
 * Ejecución: php database/migrations/migrate_callmebot.php
 */
$pdo = new PDO('mysql:host=localhost;dbname=invsys_db', 'root', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

try {
    // 1. Eliminar claves obsoletas de UltraMsg
    $pdo->exec("DELETE FROM configuraciones WHERE clave IN ('whatsapp_api_url', 'whatsapp_instance_id', 'whatsapp_token')");
    echo "✓ Claves obsoletas de UltraMsg eliminadas.\n";

    // 2. Insertar nuevas claves para CallMeBot
    $configs = [
        ['whatsapp_phone', '', 'Número de teléfono del administrador con código de país (ej: +50588887777)', 'string'],
        ['whatsapp_apikey', '', 'API Key de CallMeBot (obtenida al registrarse)', 'string'],
    ];

    $stmt = $pdo->prepare("INSERT IGNORE INTO configuraciones (clave, valor, descripcion, tipo, updated_at) VALUES (?, ?, ?, ?, NOW())");

    foreach ($configs as $c) {
        $stmt->execute($c);
    }
    echo "✓ Nuevas claves de CallMeBot insertadas (whatsapp_phone, whatsapp_apikey).\n";
    echo "✓ Migración completada exitosamente.\n";
    echo "\nPróximos pasos:\n";
    echo "  1. Ve a Configuración del sistema.\n";
    echo "  2. Activa WhatsApp y llena tu número de teléfono y API Key.\n";
    echo "  3. Para obtener tu API Key, agrega el número de CallMeBot a tus contactos\n";
    echo "     y envíale: 'I allow callmebot to send me messages'\n";

} catch (Exception $e) {
    echo "✗ Error en migración: " . $e->getMessage() . "\n";
}

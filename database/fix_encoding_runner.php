<?php
/**
 * InvSys - Script para corregir encoding UTF-8 en la BD
 * Ejecutar desde la raíz del proyecto: php database/fix_encoding_runner.php
 */

// Bootstrap mínimo
define('ROOT_PATH', dirname(__DIR__));
define('CONFIG_PATH', ROOT_PATH . '/config');
define('APP_PATH', ROOT_PATH . '/app');

require_once APP_PATH . '/core/EnvLoader.php';
EnvLoader::load(ROOT_PATH . '/.env');

$config = require CONFIG_PATH . '/database.php';

$dsn = sprintf('%s:host=%s;port=%s;dbname=%s;charset=%s',
    $config['driver'], $config['host'], $config['port'],
    $config['database'], $config['charset']
);

$pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);
$pdo->exec("SET NAMES utf8mb4");

$fixes = [
    // categorias
    ["UPDATE categorias SET nombre = ?, descripcion = ? WHERE id = ?", ['Electrónica', 'Dispositivos y componentes electrónicos', 1]],
    ["UPDATE categorias SET descripcion = ? WHERE nombre = 'Herramientas'", ['Herramientas manuales y eléctricas']],
    ["UPDATE categorias SET descripcion = ? WHERE nombre = 'Seguridad'", ['Equipos de protección y seguridad']],

    // roles
    ["UPDATE roles SET descripcion = ? WHERE id = ?", ['Administrador del sistema con acceso total', 1]],
    ["UPDATE roles SET descripcion = ? WHERE id = ?", ['Supervisor con acceso a reportes y gestión', 2]],
    ["UPDATE roles SET descripcion = ? WHERE id = ?", ['Operador con acceso básico a inventario', 3]],

    // proveedores
    ["UPDATE proveedores SET direccion = ? WHERE id = ?", ['Av. Tecnológica 123, Lima', 1]],

    // ubicaciones
    ["UPDATE ubicaciones SET descripcion = ? WHERE id = ?", ['Electrónica de alto valor', 1]],
    ["UPDATE ubicaciones SET nombre = ?, descripcion = ? WHERE id = ?", ['Cuarto Frío 1', 'Productos perecederos y químicos', 4]],

    // productos
    ["UPDATE productos SET descripcion = ? WHERE sku = ?", ['Detergente líquido industrial', 'LIMP-001']],

    // configuraciones
    ["UPDATE configuraciones SET descripcion = ? WHERE clave = ?", ['Permitir registro público de nuevos usuarios', 'permitir_registro']],
    ["UPDATE configuraciones SET descripcion = ? WHERE clave = ?", ['ID del rol asignado a nuevos usuarios registrados públicamente', 'rol_registro_publico']],
    ["UPDATE configuraciones SET descripcion = ? WHERE clave = ?", ['Número máximo de intentos de login fallidos', 'intentos_login_max']],
    ["UPDATE configuraciones SET descripcion = ? WHERE clave = ?", ['Tiempo de bloqueo por intentos fallidos (minutos)', 'tiempo_bloqueo_minutos']],
    ["UPDATE configuraciones SET descripcion = ? WHERE clave = ?", ['Tiempo de vida de la sesión en segundos', 'session_lifetime']],
    ["UPDATE configuraciones SET descripcion = ? WHERE clave = ?", ['Enviar alertas por correo electrónico', 'alertas_email']],
    ["UPDATE configuraciones SET descripcion = ? WHERE clave = ?", ['Símbolo de la moneda', 'moneda_simbolo']],
    ["UPDATE configuraciones SET descripcion = ? WHERE clave = ?", ['Código ISO de la moneda', 'moneda_codigo']],
    ["UPDATE configuraciones SET descripcion = ? WHERE clave = ?", ['Zona horaria del sistema', 'zona_horaria']],
    ["UPDATE configuraciones SET descripcion = ? WHERE clave = ?", ['Formato de visualización de fechas', 'formato_fecha']],
    ["UPDATE configuraciones SET descripcion = ? WHERE clave = ?", ['Registros por página en listados', 'registros_por_pagina']],
    ["UPDATE configuraciones SET descripcion = ? WHERE clave = ?", ['Habilitar animaciones de interfaz', 'animaciones']],
    ["UPDATE configuraciones SET descripcion = ? WHERE clave = ?", ['Días de retención de logs de auditoría', 'retencion_logs']],
    ["UPDATE configuraciones SET descripcion = ? WHERE clave = ?", ['Generar alerta al alcanzar stock mínimo', 'reorden_automatico']],
    ["UPDATE configuraciones SET descripcion = ? WHERE clave = ?", ['Alertas de accesos fallidos y cambios', 'alertas_seguridad']],
    ["UPDATE configuraciones SET descripcion = ? WHERE clave = ?", ['Stock mínimo global por defecto para nuevos productos', 'stock_minimo_global']],
    ["UPDATE configuraciones SET descripcion = ? WHERE clave = ?", ['Permitir stock negativo en salidas', 'permitir_stock_negativo']],
    ["UPDATE configuraciones SET descripcion = ? WHERE clave = ?", ['Modo de densidad compacta', 'densidad_compacta']],
    ["UPDATE configuraciones SET descripcion = ? WHERE clave = ?", ['Permitir contraer el sidebar', 'sidebar_colapsable']],
    ["UPDATE configuraciones SET descripcion = ? WHERE clave = ?", ['Tema por defecto del sistema (light/dark)', 'tema_defecto']],

    // permisos
    ["UPDATE permisos SET descripcion = ? WHERE modulo = ? AND accion = ?", ['Ver categorías', 'categorias', 'ver']],
    ["UPDATE permisos SET descripcion = ? WHERE modulo = ? AND accion = ?", ['Crear categorías', 'categorias', 'crear']],
    ["UPDATE permisos SET descripcion = ? WHERE modulo = ? AND accion = ?", ['Editar categorías', 'categorias', 'editar']],
    ["UPDATE permisos SET descripcion = ? WHERE modulo = ? AND accion = ?", ['Eliminar categorías', 'categorias', 'eliminar']],
    ["UPDATE permisos SET descripcion = ? WHERE modulo = ? AND accion = ?", ['Ver configuración', 'configuracion', 'ver']],
    ["UPDATE permisos SET descripcion = ? WHERE modulo = ? AND accion = ?", ['Editar configuración', 'configuracion', 'editar']],
    ["UPDATE permisos SET descripcion = ? WHERE modulo = ? AND accion = ?", ['Ver logs de seguridad', 'seguridad', 'ver']],
    ["UPDATE permisos SET descripcion = ? WHERE modulo = ? AND accion = ?", ['Ver movimientos de inventario', 'movimientos', 'ver']],
    ["UPDATE permisos SET descripcion = ? WHERE modulo = ? AND accion = ?", ['Ver alertas del sistema', 'alertas', 'ver']],
    ["UPDATE permisos SET descripcion = ? WHERE modulo = ? AND accion = ?", ['Marcar alertas como leídas', 'alertas', 'gestionar']],
];

$count = 0;
foreach ($fixes as [$sql, $params]) {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $affected = $stmt->rowCount();
    $count += $affected;
}

echo "✅ Fix de encoding completado: {$count} registros actualizados.\n";

// Verificación
$checks = [
    ['categorias', "SELECT nombre FROM categorias WHERE id = 1"],
    ['roles',      "SELECT descripcion FROM roles WHERE id = 2"],
    ['ubicaciones',"SELECT nombre FROM ubicaciones WHERE id = 4"],
    ['productos',  "SELECT descripcion FROM productos WHERE sku = 'LIMP-001'"],
];

echo "\n--- Verificación ---\n";
foreach ($checks as [$tabla, $sql]) {
    $result = $pdo->query($sql)->fetch();
    $val = reset((array)$result);
    $hex = bin2hex($val);
    echo "{$tabla}: {$val} [HEX: {$hex}]\n";
}

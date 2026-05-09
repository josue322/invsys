<?php
define('ROOT_PATH', __DIR__);
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');

require_once APP_PATH . '/core/EnvLoader.php';
EnvLoader::load(ROOT_PATH);

require_once APP_PATH . '/core/Model.php';
require_once APP_PATH . '/models/AnalisisInventario.php';

$analisis = new AnalisisInventario();

echo "--- 30 DIAS ---\n";
print_r($analisis->getProductosSinMovimiento(30));

echo "\n--- 0 DIAS (Todos los que no se movieron hoy) ---\n";
print_r($analisis->getProductosSinMovimiento(0));


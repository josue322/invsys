<?php
$pdo = new PDO('mysql:host=localhost;port=3306;dbname=invsys_db;charset=utf8mb4','root','', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
]);
$pdo->exec('SET NAMES utf8mb4');

$checks = [
    "SELECT nombre, HEX(nombre) as h FROM categorias WHERE id=1",
    "SELECT descripcion, HEX(descripcion) as h FROM roles WHERE id=2",
    "SELECT descripcion, HEX(descripcion) as h FROM roles WHERE id=3",
    "SELECT nombre, HEX(nombre) as h FROM ubicaciones WHERE id=4",
    "SELECT descripcion, HEX(descripcion) as h FROM productos WHERE sku='LIMP-001'",
    "SELECT direccion as nombre, HEX(direccion) as h FROM proveedores WHERE id=1",
];

foreach ($checks as $sql) {
    $r = $pdo->query($sql)->fetch();
    $col = isset($r->nombre) ? $r->nombre : $r->descripcion;
    echo $col . ' => ' . $r->h . PHP_EOL;
}

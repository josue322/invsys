<?php
/**
 * InvSys - Configuración de Base de Datos
 * 
 * Configuración centralizada de la conexión PDO a MySQL.
 * Los valores sensibles se leen desde las variables de entorno (.env).
 * Si no existen variables de entorno, se usan los valores por defecto.
 */

return [
    'driver'    => EnvLoader::get('DB_DRIVER', 'mysql'),
    'host'      => EnvLoader::get('DB_HOST', 'localhost'),
    'port'      => EnvLoader::get('DB_PORT', '3306'),
    'database'  => EnvLoader::get('DB_DATABASE', 'invsys_db'),
    'username'  => EnvLoader::get('DB_USERNAME', 'root'),
    'password'  => EnvLoader::get('DB_PASSWORD', ''),
    'charset'   => EnvLoader::get('DB_CHARSET', 'utf8mb4'),
    'collation' => 'utf8mb4_unicode_ci',
    'options'   => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_STRINGIFY_FETCHES  => false,
    ],
];

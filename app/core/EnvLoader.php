<?php
/**
 * InvSys - Env Loader
 * 
 * Carga variables de entorno desde un archivo .env
 * Similar a phpdotenv pero sin dependencias externas.
 * Las variables se almacenan en $_ENV y getenv().
 */

class EnvLoader
{
    /**
     * Cargar variables desde un archivo .env
     *
     * @param string $path Ruta al directorio que contiene .env
     * @param string $filename Nombre del archivo (default: .env)
     * @throws RuntimeException Si el archivo no existe
     */
    public static function load(string $path, string $filename = '.env'): void
    {
        $filePath = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;

        if (!file_exists($filePath)) {
            // En desarrollo, si no hay .env, simplemente no cargar nada
            // Las variables tomarán valores por defecto
            return;
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            // Ignorar comentarios
            $line = trim($line);
            if (empty($line) || str_starts_with($line, '#')) {
                continue;
            }

            // Parsear KEY=VALUE
            if (!str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Remover comillas si las tiene
            if (preg_match('/^"(.*)"$/', $value, $matches)) {
                $value = $matches[1];
            } elseif (preg_match("/^'(.*)'$/", $value, $matches)) {
                $value = $matches[1];
            }

            // Solo establecer si no está ya definida (prioridad: variables del sistema)
            if (!isset($_ENV[$key]) && getenv($key) === false) {
                $_ENV[$key] = $value;
                putenv("{$key}={$value}");
            }
        }
    }

    /**
     * Obtener una variable de entorno con valor por defecto.
     *
     * @param string $key Nombre de la variable
     * @param mixed $default Valor por defecto si no existe
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        // Prioridad: $_ENV > getenv > default
        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }

        $value = getenv($key);
        if ($value !== false) {
            return $value;
        }

        return $default;
    }

    /**
     * Verificar si una variable de entorno existe.
     *
     * @param string $key
     * @return bool
     */
    public static function has(string $key): bool
    {
        return isset($_ENV[$key]) || getenv($key) !== false;
    }
}

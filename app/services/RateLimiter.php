<?php
/**
 * InvSys - RateLimiter
 * 
 * Servicio de rate limiting basado en archivos.
 * Controla la frecuencia de acciones por IP para prevenir
 * ataques de fuerza bruta y abuso de endpoints.
 */

class RateLimiter
{
    /** @var string Directorio donde se almacenan los archivos de rate limit */
    private string $storagePath;

    /** @var self|null Instancia singleton */
    private static ?self $instance = null;

    /**
     * Obtener instancia singleton.
     *
     * @return self
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $this->storagePath = STORAGE_PATH . '/rate_limits';

        if (!is_dir($this->storagePath)) {
            @mkdir($this->storagePath, 0755, true);
        }
    }

    /**
     * Verificar si una IP está rate-limited para una acción.
     *
     * @param string $ip        Dirección IP del cliente
     * @param string $action    Nombre de la acción (ej: 'login')
     * @param int $maxAttempts  Máximo de intentos permitidos en la ventana
     * @param int $windowSecs   Ventana de tiempo en segundos
     * @return array ['limited' => bool, 'remaining' => int, 'retry_after' => int]
     */
    public function check(string $ip, string $action, int $maxAttempts = 20, int $windowSecs = 900): array
    {
        $data = $this->loadData($ip, $action);
        $now = time();

        // Filtrar intentos dentro de la ventana
        $data['attempts'] = array_values(array_filter(
            $data['attempts'] ?? [],
            fn(int $ts) => ($now - $ts) < $windowSecs
        ));

        $count = count($data['attempts']);
        $remaining = max(0, $maxAttempts - $count);

        // Calcular tiempo para retry si está limitado
        $retryAfter = 0;
        if ($count >= $maxAttempts && !empty($data['attempts'])) {
            $oldest = min($data['attempts']);
            $retryAfter = max(0, $windowSecs - ($now - $oldest));
        }

        return [
            'limited'     => $count >= $maxAttempts,
            'remaining'   => $remaining,
            'retry_after' => $retryAfter,
        ];
    }

    /**
     * Registrar un intento para una IP y acción.
     *
     * @param string $ip     Dirección IP del cliente
     * @param string $action Nombre de la acción
     */
    public function hit(string $ip, string $action): void
    {
        $data = $this->loadData($ip, $action);
        $now = time();

        // Agregar intento actual
        $data['attempts'][] = $now;

        // Limpiar intentos viejos (más de 1 hora) para no acumular basura
        $data['attempts'] = array_values(array_filter(
            $data['attempts'],
            fn(int $ts) => ($now - $ts) < 3600
        ));

        $this->saveData($ip, $action, $data);
    }

    /**
     * Limpiar todos los intentos de una IP para una acción.
     * Útil tras un login exitoso.
     *
     * @param string $ip     Dirección IP
     * @param string $action Nombre de la acción
     */
    public function clear(string $ip, string $action): void
    {
        $file = $this->getFilePath($ip, $action);
        if (file_exists($file)) {
            @unlink($file);
        }
    }

    /**
     * Limpiar archivos expirados (más de 1 hora sin actividad).
     * Se llama automáticamente al guardar, con probabilidad 1/50.
     */
    public function cleanup(): void
    {
        $now = time();
        $files = glob($this->storagePath . '/*.json');

        foreach ($files as $file) {
            // Si el archivo tiene más de 1 hora, eliminarlo
            if (($now - filemtime($file)) > 3600) {
                @unlink($file);
            }
        }
    }

    /**
     * Obtener la ruta del archivo para una IP y acción.
     *
     * @param string $ip
     * @param string $action
     * @return string
     */
    private function getFilePath(string $ip, string $action): string
    {
        // Hash la IP para nombres de archivo seguros
        $key = md5($ip . ':' . $action);
        return $this->storagePath . '/' . $key . '.json';
    }

    /**
     * Cargar datos de rate limit desde archivo.
     *
     * @param string $ip
     * @param string $action
     * @return array
     */
    private function loadData(string $ip, string $action): array
    {
        $file = $this->getFilePath($ip, $action);

        if (!file_exists($file)) {
            return ['attempts' => []];
        }

        $content = @file_get_contents($file);
        if ($content === false) {
            return ['attempts' => []];
        }

        $data = json_decode($content, true);
        return is_array($data) ? $data : ['attempts' => []];
    }

    /**
     * Guardar datos de rate limit a archivo.
     *
     * @param string $ip
     * @param string $action
     * @param array $data
     */
    private function saveData(string $ip, string $action, array $data): void
    {
        $file = $this->getFilePath($ip, $action);
        @file_put_contents($file, json_encode($data), LOCK_EX);

        // Limpieza periódica (1 de cada 50 requests)
        if (random_int(1, 50) === 1) {
            $this->cleanup();
        }
    }
}

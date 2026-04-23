<?php
/**
 * InvSys - Modelo Config
 * 
 * Sistema de configuraciones con caché en memoria.
 * Carga todas las configuraciones en un solo query y las sirve desde caché.
 */

class Config extends Model
{
    protected string $table = 'configuraciones';

    /** @var array|null Caché estática de todas las configuraciones */
    private static ?array $cache = null;

    /**
     * Cargar todas las configuraciones en memoria (1 sola query).
     * Se ejecuta solo la primera vez; las siguientes lecturas usan caché.
     * Almacena tanto el valor como el tipo para evitar queries adicionales.
     *
     * @return array Mapa clave => ['valor' => string, 'tipo' => string]
     */
    public static function loadAll(): array
    {
        if (self::$cache === null) {
            try {
                $instance = new self();
                $results = $instance->query("SELECT clave, valor, tipo FROM {$instance->table}")->fetchAll();
                self::$cache = [];
                foreach ($results as $row) {
                    self::$cache[$row->clave] = [
                        'valor' => $row->valor,
                        'tipo'  => $row->tipo ?? 'text',
                    ];
                }
            } catch (\Exception $e) {
                self::$cache = [];
            }
        }
        return self::$cache;
    }

    /**
     * Obtener valor de una configuración por su clave (usa caché).
     */
    public function getValue(string $key): ?string
    {
        self::loadAll();
        return isset(self::$cache[$key]) ? self::$cache[$key]['valor'] : null;
    }

    /**
     * Obtener valor con fallback rápido (método estático).
     *
     * @param string $key Clave de configuración
     * @param mixed $default Valor por defecto
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        self::loadAll();
        return isset(self::$cache[$key]) ? self::$cache[$key]['valor'] : $default;
    }

    /**
     * Obtener el tipo de una configuración desde la caché.
     *
     * @param string $key Clave de configuración
     * @return string|null
     */
    public static function getType(string $key): ?string
    {
        self::loadAll();
        return self::$cache[$key]['tipo'] ?? null;
    }

    /**
     * Establecer valor de una configuración.
     * Actualiza tanto la BD como la caché en memoria.
     */
    public function setValue(string $key, string $value): bool
    {
        $existing = $this->findOneBy('clave', $key);
        if ($existing) {
            $result = $this->update($existing->id, ['valor' => $value]);
            // Actualizar caché
            if (self::$cache !== null && isset(self::$cache[$key])) {
                self::$cache[$key]['valor'] = $value;
            }
            return $result;
        }
        return false;
    }

    /**
     * Invalidar la caché (útil después de cambios masivos).
     */
    public static function clearCache(): void
    {
        self::$cache = null;
    }
}

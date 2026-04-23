<?php
/**
 * InvSys - ConfigService
 * 
 * Servicio de configuración dinámica.
 * Lee y escribe configuraciones del sistema desde la tabla configuraciones.
 */

class ConfigService
{
    private Config $configModel;

    public function __construct()
    {
        $this->configModel = new Config();
    }

    /**
     * Obtener el valor de una configuración.
     *
     * @param string $key Clave de configuración
     * @param mixed $default Valor por defecto si no existe
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        // Usa la caché estática del modelo (1 sola query por request)
        $value = Config::get($key);
        
        if ($value === null) {
            return $default;
        }

        // Obtener tipo desde la caché (sin query adicional)
        $tipo = Config::getType($key);
        
        return match ($tipo) {
            'number'  => is_numeric($value) ? (float) $value : $default,
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'json'    => json_decode($value, true) ?? $default,
            default   => $value,
        };
    }

    /**
     * Establecer el valor de una configuración.
     *
     * @param string $key Clave
     * @param mixed $value Valor
     * @return bool
     */
    public function set(string $key, mixed $value): bool
    {
        $existing = $this->configModel->findOneBy('clave', $key);
        
        if (is_array($value) || is_object($value)) {
            $value = json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        if ($existing) {
            $result = $this->configModel->update($existing->id, ['valor' => (string) $value]);
            // Invalidar caché para que se recargue con los nuevos valores
            Config::clearCache();
            return $result;
        }

        $this->configModel->create([
            'clave' => $key,
            'valor' => (string) $value,
            'tipo'  => 'text',
        ]);

        // Invalidar caché al agregar nueva configuración
        Config::clearCache();

        return true;
    }

    /**
     * Obtener todas las configuraciones.
     *
     * @return array
     */
    public function getAll(): array
    {
        return $this->configModel->findAll('clave', 'ASC');
    }

    /**
     * Actualizar múltiples configuraciones a la vez.
     *
     * @param array $configs Array de [clave => valor]
     * @return bool
     */
    public function updateMultiple(array $configs): bool
    {
        foreach ($configs as $key => $value) {
            $this->set($key, $value);
        }
        return true;
    }
}

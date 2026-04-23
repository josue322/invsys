<?php
/**
 * InvSys - Modelo UserSetting
 */

class UserSetting extends Model
{
    protected string $table = 'user_settings';

    /**
     * Obtener una configuración de usuario.
     */
    public function getSetting(int $userId, string $key): ?string
    {
        $sql = "SELECT valor FROM {$this->table} WHERE usuario_id = :usuario_id AND clave = :clave LIMIT 1";
        $result = $this->query($sql, ['usuario_id' => $userId, 'clave' => $key])->fetch();
        return $result ? $result->valor : null;
    }

    /**
     * Establecer una configuración de usuario (insert o update).
     */
    public function setSetting(int $userId, string $key, string $value): bool
    {
        $existing = $this->getSetting($userId, $key);

        if ($existing !== null) {
            $sql = "UPDATE {$this->table} SET valor = :valor WHERE usuario_id = :usuario_id AND clave = :clave";
            $this->query($sql, ['valor' => $value, 'usuario_id' => $userId, 'clave' => $key]);
        } else {
            $this->create([
                'usuario_id' => $userId,
                'clave'      => $key,
                'valor'      => $value,
            ]);
        }

        return true;
    }
}

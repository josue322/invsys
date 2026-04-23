<?php
/**
 * InvSys - Modelo Sesion
 */

class Sesion extends Model
{
    protected string $table = 'sesiones';

    /**
     * Desactivar todas las sesiones de un usuario.
     */
    public function deactivateByUserId(int $userId): bool
    {
        $sql = "UPDATE {$this->table} SET activa = 0 WHERE usuario_id = :usuario_id AND activa = 1";
        return $this->query($sql, ['usuario_id' => $userId])->rowCount() > 0;
    }

    /**
     * Obtener sesiones activas de un usuario.
     */
    public function getActiveByUserId(int $userId): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE usuario_id = :usuario_id AND activa = 1 ORDER BY ultimo_acceso DESC";
        return $this->query($sql, ['usuario_id' => $userId])->fetchAll();
    }
}

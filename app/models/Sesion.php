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

    /**
     * Eliminar sesiones inactivas más antiguas que N días.
     * Previene el crecimiento indefinido de la tabla.
     *
     * @param int $days Días de retención (por defecto 30)
     * @return int Cantidad de sesiones eliminadas
     */
    public function cleanOld(int $days = 30): int
    {
        $sql = "DELETE FROM {$this->table} WHERE activa = 0 AND inicio < DATE_SUB(NOW(), INTERVAL :days DAY)";
        return $this->query($sql, ['days' => $days])->rowCount();
    }
}

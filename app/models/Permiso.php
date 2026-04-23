<?php
/**
 * InvSys - Modelo Permiso
 */

class Permiso extends Model
{
    protected string $table = 'permisos';

    /**
     * Obtener permisos de un rol específico.
     */
    public function getByRolId(int $rolId): array
    {
        $sql = "SELECT p.* 
                FROM {$this->table} p 
                INNER JOIN rol_permiso rp ON p.id = rp.permiso_id 
                WHERE rp.rol_id = :rol_id 
                ORDER BY p.modulo, p.accion";
        return $this->query($sql, ['rol_id' => $rolId])->fetchAll();
    }

    /**
     * Obtener todos los permisos agrupados por módulo.
     */
    public function getAllGrouped(): array
    {
        $permisos = $this->findAll('modulo', 'ASC');
        $grouped = [];
        
        foreach ($permisos as $permiso) {
            $grouped[$permiso->modulo][] = $permiso;
        }
        
        return $grouped;
    }
}

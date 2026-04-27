<?php
/**
 * InvSys - Modelo Departamento
 */

class Departamento extends Model
{
    protected string $table = 'departamentos';

    /**
     * Obtener todos los departamentos activos.
     */
    public function findAllActive(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE activo = 1 ORDER BY nombre ASC";
        return $this->query($sql)->fetchAll();
    }



    /**
     * Buscar departamento por ID.
     */
    public function findById(int $id): object|false
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        return $this->query($sql, ['id' => $id])->fetch();
    }
}

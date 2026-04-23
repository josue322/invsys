<?php
/**
 * InvSys - Modelo Rol
 */

class Rol extends Model
{
    protected string $table = 'roles';

    /**
     * Obtener todos los roles activos ordenados por nombre.
     */
    public function getAllActive(): array
    {
        return $this->findAll('nombre', 'ASC');
    }
}

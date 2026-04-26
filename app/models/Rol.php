<?php
/**
 * InvSys - Modelo Rol
 */

class Rol extends Model
{
    protected string $table = 'roles';

    /**
     * Obtener todos los roles ordenados por nombre.
     */
    public function getAll(): array
    {
        return $this->findAll('nombre', 'ASC');
    }
}

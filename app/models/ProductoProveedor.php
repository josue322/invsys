<?php
/**
 * InvSys - Modelo ProductoProveedor
 * 
 * Tabla pivote para la relación muchos-a-muchos
 * entre productos y proveedores.
 */

class ProductoProveedor extends Model
{
    protected string $table = 'producto_proveedor';

    /**
     * Obtener todos los proveedores vinculados a un producto.
     */
    public function findByProducto(int $productoId): array
    {
        $sql = "SELECT pp.*, p.nombre as proveedor_nombre, p.contacto, p.telefono, p.email
                FROM {$this->table} pp
                INNER JOIN proveedores p ON pp.proveedor_id = p.id
                WHERE pp.producto_id = :producto_id AND pp.activo = 1
                ORDER BY pp.es_preferido DESC, p.nombre ASC";
        return $this->query($sql, ['producto_id' => $productoId])->fetchAll();
    }

    /**
     * Obtener todos los productos vinculados a un proveedor.
     */
    public function findByProveedor(int $proveedorId): array
    {
        $sql = "SELECT pp.*, prod.nombre as producto_nombre, prod.sku, prod.stock, prod.precio
                FROM {$this->table} pp
                INNER JOIN productos prod ON pp.producto_id = prod.id
                WHERE pp.proveedor_id = :proveedor_id AND pp.activo = 1
                ORDER BY prod.nombre ASC";
        return $this->query($sql, ['proveedor_id' => $proveedorId])->fetchAll();
    }

    /**
     * Obtener el proveedor preferido de un producto.
     */
    public function findPreferido(int $productoId): object|false
    {
        $sql = "SELECT pp.*, p.nombre as proveedor_nombre, p.contacto, p.telefono, p.email
                FROM {$this->table} pp
                INNER JOIN proveedores p ON pp.proveedor_id = p.id
                WHERE pp.producto_id = :producto_id AND pp.es_preferido = 1 AND pp.activo = 1
                LIMIT 1";
        return $this->query($sql, ['producto_id' => $productoId])->fetch();
    }

    /**
     * Verificar si ya existe un vínculo entre producto y proveedor.
     */
    public function existsVinculo(int $productoId, int $proveedorId): bool
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} 
                WHERE producto_id = :producto_id AND proveedor_id = :proveedor_id";
        return (int) $this->query($sql, [
            'producto_id' => $productoId,
            'proveedor_id' => $proveedorId,
        ])->fetch()->total > 0;
    }

    /**
     * Vincular un proveedor a un producto.
     * Si ya existe el vínculo inactivo, lo reactiva.
     */
    public function vincular(int $productoId, int $proveedorId, array $data = []): int
    {
        // Verificar si existe un vínculo inactivo
        $existente = $this->query(
            "SELECT id FROM {$this->table} WHERE producto_id = :pid AND proveedor_id = :vid LIMIT 1",
            ['pid' => $productoId, 'vid' => $proveedorId]
        )->fetch();

        if ($existente) {
            // Reactivar y actualizar
            $updateData = array_merge($data, ['activo' => 1]);
            $this->update($existente->id, $updateData);
            return $existente->id;
        }

        // Crear nuevo vínculo
        return $this->create(array_merge([
            'producto_id' => $productoId,
            'proveedor_id' => $proveedorId,
        ], $data));
    }

    /**
     * Desvincular (soft delete) un proveedor de un producto.
     */
    public function desvincular(int $id): bool
    {
        return $this->update($id, ['activo' => 0, 'es_preferido' => 0]);
    }

    /**
     * Establecer un proveedor como preferido para un producto.
     * Quita el flag de preferido de los demás proveedores del mismo producto.
     */
    public function setPreferido(int $productoId, int $vinculoId): bool
    {
        // Quitar preferido de todos
        $this->query(
            "UPDATE {$this->table} SET es_preferido = 0 WHERE producto_id = :pid",
            ['pid' => $productoId]
        );

        // Establecer el nuevo preferido
        return $this->update($vinculoId, ['es_preferido' => 1]);
    }

    /**
     * Contar proveedores activos de un producto.
     */
    public function countByProducto(int $productoId): int
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} 
                WHERE producto_id = :producto_id AND activo = 1";
        return (int) $this->query($sql, ['producto_id' => $productoId])->fetch()->total;
    }
}

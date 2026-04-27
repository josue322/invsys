<?php
/**
 * InvSys - Modelo OrdenCompraDetalle
 */

class OrdenCompraDetalle extends Model
{
    protected string $table = 'orden_compra_detalles';

    /**
     * Obtener los detalles de una orden específica con los datos del producto.
     */
    public function getByOrden(int $ordenCompraId): array
    {
        $sql = "SELECT d.*, p.sku, p.nombre as producto_nombre, p.unidad_medida, p.es_perecedero
                FROM {$this->table} d
                INNER JOIN productos p ON d.producto_id = p.id
                WHERE d.orden_compra_id = :orden_compra_id";
        
        return $this->query($sql, ['orden_compra_id' => $ordenCompraId])->fetchAll();
    }
}

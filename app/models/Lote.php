<?php
/**
 * InvSys - Modelo Lote
 */

class Lote extends Model
{
    protected string $table = 'lotes';

    /**
     * Obtener lotes por producto para consumo FIFO/FEFO.
     */
    public function getAvailableByProduct(int $productoId): array
    {
        $sql = "SELECT l.*, p.nombre as proveedor_nombre 
                FROM {$this->table} l
                LEFT JOIN proveedores p ON l.proveedor_id = p.id
                WHERE l.producto_id = :producto_id 
                  AND l.stock_actual > 0 
                  AND l.estado = 'disponible'
                ORDER BY l.fecha_vencimiento ASC";
        return $this->query($sql, ['producto_id' => $productoId])->fetchAll();
    }

    /**
     * Obtener productos perecederos próximos a vencer, sumando el stock de los lotes.
     */
    public function getExpiringLots(int $days = 30): array
    {
        $sql = "SELECT l.*, 
                       p.nombre as producto_nombre, 
                       p.sku, 
                       c.nombre as categoria_nombre 
                FROM {$this->table} l 
                INNER JOIN productos p ON l.producto_id = p.id 
                LEFT JOIN categorias c ON p.categoria_id = c.id 
                WHERE p.activo = 1 
                  AND l.stock_actual > 0
                  AND l.fecha_vencimiento IS NOT NULL 
                  AND l.fecha_vencimiento <= DATE_ADD(CURDATE(), INTERVAL :days DAY)
                ORDER BY l.fecha_vencimiento ASC";
        return $this->query($sql, ['days' => $days])->fetchAll();
    }

    /**
     * Actualiza el stock de un lote específico.
     */
    public function updateStock(int $id, int $newStock): bool
    {
        $estado = $newStock <= 0 ? 'agotado' : 'disponible';
        $sql = "UPDATE {$this->table} SET stock_actual = :stock, estado = :estado WHERE id = :id";
        return $this->query($sql, [
            'stock' => $newStock, 
            'estado' => $estado,
            'id' => $id
        ])->rowCount() > 0;
    }
}

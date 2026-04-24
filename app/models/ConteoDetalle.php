<?php
/**
 * InvSys - Modelo ConteoDetalle
 * 
 * Items individuales de una sesión de conteo físico.
 */

class ConteoDetalle extends Model
{
    protected string $table = 'conteo_detalle';

    /**
     * Obtener todos los detalles de un conteo con info de producto.
     */
    public function getByConteo(int $conteoId, string $filter = 'todos'): array
    {
        $where = "cd.conteo_id = :id";

        if ($filter === 'pendientes') {
            $where .= " AND cd.stock_fisico IS NULL";
        } elseif ($filter === 'contados') {
            $where .= " AND cd.stock_fisico IS NOT NULL";
        } elseif ($filter === 'diferencias') {
            $where .= " AND cd.stock_fisico IS NOT NULL AND cd.diferencia != 0";
        }

        $sql = "SELECT cd.*, 
                       p.nombre as producto_nombre,
                       p.sku,
                       p.stock as stock_actual,
                       p.unidad_medida,
                       c.nombre as categoria_nombre,
                       ub.nombre as ubicacion_nombre
                FROM {$this->table} cd
                INNER JOIN productos p ON cd.producto_id = p.id
                LEFT JOIN categorias c ON p.categoria_id = c.id
                LEFT JOIN ubicaciones ub ON p.ubicacion_id = ub.id
                WHERE {$where}
                ORDER BY p.nombre ASC";

        return $this->query($sql, ['id' => $conteoId])->fetchAll();
    }

    /**
     * Actualizar el conteo físico de un item.
     */
    public function updateConteo(int $id, int $stockFisico, ?string $observaciones, int $userId): bool
    {
        $sql = "UPDATE {$this->table} 
                SET stock_fisico = :stock, observaciones = :obs, contado_por = :user, contado_at = NOW()
                WHERE id = :id";
        return $this->query($sql, [
            'id'    => $id,
            'stock' => $stockFisico,
            'obs'   => $observaciones,
            'user'  => $userId,
        ])->rowCount() > 0;
    }

    /**
     * Obtener solo los items con diferencia (para aplicar ajustes).
     */
    public function getDiferencias(int $conteoId): array
    {
        $sql = "SELECT cd.*, p.nombre as producto_nombre, p.sku, p.stock as stock_actual
                FROM {$this->table} cd
                INNER JOIN productos p ON cd.producto_id = p.id
                WHERE cd.conteo_id = :id 
                  AND cd.stock_fisico IS NOT NULL 
                  AND cd.diferencia != 0
                ORDER BY cd.diferencia ASC";

        return $this->query($sql, ['id' => $conteoId])->fetchAll();
    }

    /**
     * Cargar productos en un conteo (bulk insert).
     */
    public function loadProducts(int $conteoId, array $productos): int
    {
        $count = 0;
        $sql = "INSERT INTO {$this->table} (conteo_id, producto_id, stock_sistema) VALUES (:conteo, :producto, :stock)";

        foreach ($productos as $p) {
            $this->query($sql, [
                'conteo'   => $conteoId,
                'producto' => $p->id,
                'stock'    => $p->stock,
            ]);
            $count++;
        }

        return $count;
    }
}

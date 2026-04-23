<?php
/**
 * InvSys - Modelo PrecioHistorial
 * 
 * Registra el historial de cambios de precio de los productos.
 */

class PrecioHistorial extends Model
{
    protected string $table = 'precio_historial';

    /**
     * Registrar un cambio de precio.
     *
     * @param int $productoId ID del producto
     * @param float $precioAnterior Precio antes del cambio
     * @param float $precioNuevo Precio después del cambio
     * @param int|null $usuarioId ID del usuario que hizo el cambio
     * @param string|null $motivo Motivo del cambio
     * @return int ID del registro creado
     */
    public function registrar(
        int $productoId,
        float $precioAnterior,
        float $precioNuevo,
        ?int $usuarioId = null,
        ?string $motivo = null
    ): int {
        return $this->create([
            'producto_id'    => $productoId,
            'precio_anterior' => $precioAnterior,
            'precio_nuevo'   => $precioNuevo,
            'usuario_id'     => $usuarioId,
            'motivo'         => $motivo,
        ]);
    }

    /**
     * Obtener historial de precios de un producto (más reciente primero).
     *
     * @param int $productoId
     * @param int $limit
     * @return array
     */
    public function getByProducto(int $productoId, int $limit = 20): array
    {
        $sql = "SELECT ph.*, u.nombre as usuario_nombre
                FROM {$this->table} ph
                LEFT JOIN usuarios u ON ph.usuario_id = u.id
                WHERE ph.producto_id = :producto_id
                ORDER BY ph.created_at DESC
                LIMIT {$limit}";
        return $this->query($sql, ['producto_id' => $productoId])->fetchAll();
    }

    /**
     * Obtener datos para gráfico de evolución de precio.
     * Devuelve registros en orden cronológico con el precio nuevo.
     *
     * @param int $productoId
     * @param int $limit
     * @return array
     */
    public function getChartData(int $productoId, int $limit = 30): array
    {
        $sql = "SELECT precio_nuevo as precio, 
                       DATE_FORMAT(created_at, '%d/%m/%Y') as fecha,
                       created_at
                FROM {$this->table}
                WHERE producto_id = :producto_id
                ORDER BY created_at ASC
                LIMIT {$limit}";
        return $this->query($sql, ['producto_id' => $productoId])->fetchAll();
    }

    /**
     * Contar cambios de precio de un producto.
     *
     * @param int $productoId
     * @return int
     */
    public function countByProducto(int $productoId): int
    {
        return $this->count('producto_id = :pid', ['pid' => $productoId]);
    }
}

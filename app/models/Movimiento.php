<?php
/**
 * InvSys - Modelo Movimiento
 */

class Movimiento extends Model
{
    protected string $table = 'movimientos';

    /**
     * Obtener movimientos con producto y usuario (paginado y filtros).
     */
    public function getAllWithDetails(
        int $page = 1,
        int $perPage = 15,
        string $tipo = '',
        string $fechaDesde = '',
        string $fechaHasta = '',
        int $productoId = 0
    ): array {
        $offset = ($page - 1) * $perPage;
        $where = '1=1';
        $params = [];

        if (!empty($tipo)) {
            $where .= " AND m.tipo = :tipo";
            $params['tipo'] = $tipo;
        }

        if (!empty($fechaDesde)) {
            $where .= " AND DATE(m.created_at) >= :fecha_desde";
            $params['fecha_desde'] = $fechaDesde;
        }

        if (!empty($fechaHasta)) {
            $where .= " AND DATE(m.created_at) <= :fecha_hasta";
            $params['fecha_hasta'] = $fechaHasta;
        }

        if ($productoId > 0) {
            $where .= " AND m.producto_id = :producto_id";
            $params['producto_id'] = $productoId;
        }

        $countSql = "SELECT COUNT(*) as total FROM {$this->table} m WHERE {$where}";
        $total = $this->query($countSql, $params)->fetch()->total;

        $sql = "SELECT m.*, p.nombre as producto_nombre, p.sku as producto_sku, 
                       u.nombre as usuario_nombre,
                       l.numero_lote as lote_numero,
                       prov.nombre as proveedor_nombre,
                       d.nombre as departamento_nombre
                FROM {$this->table} m 
                INNER JOIN productos p ON m.producto_id = p.id 
                INNER JOIN usuarios u ON m.usuario_id = u.id 
                LEFT JOIN lotes l ON m.lote_id = l.id
                LEFT JOIN proveedores prov ON m.proveedor_id = prov.id
                LEFT JOIN departamentos d ON m.departamento_id = d.id
                WHERE {$where}
                ORDER BY m.created_at DESC 
                LIMIT {$perPage} OFFSET {$offset}";

        $data = $this->query($sql, $params)->fetchAll();

        return [
            'data' => $data,
            'total' => (int) $total,
            'pages' => (int) ceil($total / $perPage),
            'current' => $page,
            'perPage' => $perPage,
        ];
    }

    /**
     * Contar movimientos del día.
     */
    public function countToday(): int
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE DATE(created_at) = CURDATE()";
        return (int) $this->query($sql)->fetch()->total;
    }

    /**
     * Obtener movimientos por tipo para el gráfico (últimos 7 días).
     */
    public function getByTypeLastDays(int $days = 7): array
    {
        $sql = "SELECT DATE(created_at) as fecha, tipo, COUNT(*) as total, SUM(cantidad) as cantidad_total
                FROM {$this->table} 
                WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
                GROUP BY DATE(created_at), tipo 
                ORDER BY fecha ASC";
        return $this->query($sql, ['days' => $days])->fetchAll();
    }

    /**
     * Obtener los productos con más movimientos (top N).
     */
    public function getTopProducts(int $limit = 5): array
    {
        $sql = "SELECT p.nombre, p.sku, COUNT(m.id) as total_movimientos, 
                       SUM(CASE WHEN m.tipo = 'entrada' THEN m.cantidad ELSE 0 END) as total_entradas,
                       SUM(CASE WHEN m.tipo = 'salida' THEN m.cantidad ELSE 0 END) as total_salidas
                FROM {$this->table} m 
                INNER JOIN productos p ON m.producto_id = p.id 
                WHERE p.activo = 1
                GROUP BY p.id, p.nombre, p.sku 
                ORDER BY total_movimientos DESC 
                LIMIT {$limit}";
        return $this->query($sql)->fetchAll();
    }

    /**
     * Obtener todos los movimientos con detalles de producto y usuario.
     * Útil para exportaciones y reportes completos.
     *
     * @param int $limit Máximo de registros a retornar
     * @param string $fechaDesde Fecha inicio (Y-m-d) para filtrar
     * @param string $fechaHasta Fecha fin (Y-m-d) para filtrar
     * @return array
     */
    public function getAllForExport(int $limit = 1000, string $fechaDesde = '', string $fechaHasta = ''): array
    {
        $where = '1=1';
        $params = [];

        if (!empty($fechaDesde)) {
            $where .= " AND DATE(m.created_at) >= :fecha_desde";
            $params['fecha_desde'] = $fechaDesde;
        }

        if (!empty($fechaHasta)) {
            $where .= " AND DATE(m.created_at) <= :fecha_hasta";
            $params['fecha_hasta'] = $fechaHasta;
        }

        $sql = "SELECT m.*, p.nombre as producto_nombre, p.sku as producto_sku, 
                       u.nombre as usuario_nombre,
                       l.numero_lote as lote_numero,
                       prov.nombre as proveedor_nombre,
                       d.nombre as departamento_nombre
                FROM {$this->table} m 
                INNER JOIN productos p ON m.producto_id = p.id 
                INNER JOIN usuarios u ON m.usuario_id = u.id 
                LEFT JOIN lotes l ON m.lote_id = l.id
                LEFT JOIN proveedores prov ON m.proveedor_id = prov.id
                LEFT JOIN departamentos d ON m.departamento_id = d.id
                WHERE {$where}
                ORDER BY m.created_at DESC 
                LIMIT {$limit}";
        return $this->query($sql, $params)->fetchAll();
    }
}

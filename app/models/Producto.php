<?php
/**
 * InvSys - Modelo Producto
 */

class Producto extends Model
{
    protected string $table = 'productos';

    /**
     * Obtener todos los productos activos.
     */
    public function findAllActive(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE activo = 1 ORDER BY nombre ASC";
        return $this->query($sql)->fetchAll();
    }

    /**
     * Obtener productos con categoría (paginado y con filtros).
     */
    public function getAllWithCategory(
        int $page = 1,
        int $perPage = 15,
        string $search = '',
        int $categoriaId = 0,
        string $stockFilter = ''
    ): array {
        $offset = ($page - 1) * $perPage;
        $where = '1=1';
        $params = [];

        if (!empty($search)) {
            $where .= " AND (p.nombre LIKE :search OR p.sku LIKE :search2 OR p.descripcion LIKE :search3)";
            $params['search'] = "%{$search}%";
            $params['search2'] = "%{$search}%";
            $params['search3'] = "%{$search}%";
        }

        if ($categoriaId > 0) {
            $where .= " AND p.categoria_id = :categoria_id";
            $params['categoria_id'] = $categoriaId;
        }

        if ($stockFilter === 'bajo') {
            $where .= " AND p.stock <= p.stock_minimo AND p.stock > 0";
        } elseif ($stockFilter === 'agotado') {
            $where .= " AND p.stock <= 0";
        } elseif ($stockFilter === 'normal') {
            $where .= " AND p.stock > p.stock_minimo";
        }

        $countSql = "SELECT COUNT(*) as total FROM {$this->table} p WHERE {$where}";
        $total = $this->query($countSql, $params)->fetch()->total;

        $sql = "SELECT p.*, c.nombre as categoria_nombre, u.nombre as ubicacion_nombre 
                FROM {$this->table} p 
                LEFT JOIN categorias c ON p.categoria_id = c.id 
                LEFT JOIN ubicaciones u ON p.ubicacion_id = u.id
                WHERE {$where}
                ORDER BY p.id DESC 
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
     * Obtener un producto con su categoría.
     */
    public function findWithCategory(int $id): object|false
    {
        $sql = "SELECT p.*, c.nombre as categoria_nombre, u.nombre as ubicacion_nombre 
                FROM {$this->table} p 
                LEFT JOIN categorias c ON p.categoria_id = c.id 
                LEFT JOIN ubicaciones u ON p.ubicacion_id = u.id
                WHERE p.id = :id LIMIT 1";
        return $this->query($sql, ['id' => $id])->fetch();
    }

    /**
     * Verificar si un SKU ya existe (excluyendo un ID).
     */
    public function skuExists(string $sku, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE sku = :sku";
        $params = ['sku' => $sku];

        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }
        return (int) $this->query($sql, $params)->fetch()->total > 0;
    }
    /**
     * Actualizar stock de un producto.
     */
    public function updateStock(int $id, int $newStock): bool
    {
        $sql = "UPDATE {$this->table} SET stock = :stock WHERE id = :id";
        return $this->query($sql, ['stock' => $newStock, 'id' => $id])->rowCount() > 0;
    }

    /**
     * Obtener total de productos activos.
     */
    public function countActive(): int
    {
        return $this->count('activo = 1');
    }

    /**
     * Obtener valor total del inventario (suma de precio * stock).
     */
    public function getTotalInventoryValue(): float
    {
        $sql = "SELECT COALESCE(SUM(precio * stock), 0) as total FROM {$this->table} WHERE activo = 1";
        return (float) $this->query($sql)->fetch()->total;
    }

    /**
     * Obtener productos con stock bajo.
     */
    public function getLowStock(): array
    {
        $sql = "SELECT p.*, c.nombre as categoria_nombre 
                FROM {$this->table} p 
                LEFT JOIN categorias c ON p.categoria_id = c.id 
                WHERE p.activo = 1 AND p.stock <= p.stock_minimo 
                ORDER BY p.stock ASC";
        return $this->query($sql)->fetchAll();
    }

    /**
     * Obtener productos por categoría para gráficos.
     */
    public function getCountByCategory(): array
    {
        $sql = "SELECT c.nombre as categoria, COUNT(p.id) as total 
                FROM {$this->table} p 
                INNER JOIN categorias c ON p.categoria_id = c.id 
                WHERE p.activo = 1 
                GROUP BY c.id, c.nombre 
                ORDER BY total DESC";
        return $this->query($sql)->fetchAll();
    }

    /**
     * Obtener productos perecederos activos (para verificación de vencimiento).
     */
    public function findPerishableActive(): array
    {
        $sql = "SELECT p.*, l.fecha_vencimiento, l.numero_lote 
                FROM {$this->table} p 
                INNER JOIN lotes l ON p.id = l.producto_id 
                WHERE p.activo = 1 AND p.es_perecedero = 1 AND l.stock_actual > 0 
                ORDER BY l.fecha_vencimiento ASC";
        return $this->query($sql)->fetchAll();
    }

    /**
     * Obtener productos próximos a vencer o vencidos.
     *
     * @param int $days Días de anticipación
     */
    public function getExpiringProducts(int $days = 30): array
    {
        $sql = "SELECT p.*, c.nombre as categoria_nombre, l.fecha_vencimiento, l.numero_lote, l.stock_actual as lote_stock
                FROM {$this->table} p 
                LEFT JOIN categorias c ON p.categoria_id = c.id 
                INNER JOIN lotes l ON p.id = l.producto_id
                WHERE p.activo = 1 
                  AND p.es_perecedero = 1 
                  AND l.stock_actual > 0
                  AND l.fecha_vencimiento <= DATE_ADD(CURDATE(), INTERVAL :days DAY)
                ORDER BY l.fecha_vencimiento ASC";
        return $this->query($sql, ['days' => $days])->fetchAll();
    }

    /**
     * Obtener historial de movimientos de un producto específico.
     */
    public function getMovimientos(int $productoId, int $limit = 20): array
    {
        $sql = "SELECT m.*, u.nombre as usuario_nombre
                FROM movimientos m
                INNER JOIN usuarios u ON m.usuario_id = u.id
                WHERE m.producto_id = :producto_id
                ORDER BY m.created_at DESC
                LIMIT {$limit}";
        return $this->query($sql, ['producto_id' => $productoId])->fetchAll();
    }

    /**
     * Obtener todos los productos activos con su categoría.
     * Útil para exportaciones y reportes completos.
     *
     * @return array
     */
    public function getAllActiveWithCategory(): array
    {
        $sql = "SELECT p.*, c.nombre as categoria_nombre, u.nombre as ubicacion_nombre 
                FROM {$this->table} p 
                LEFT JOIN categorias c ON p.categoria_id = c.id 
                LEFT JOIN ubicaciones u ON p.ubicacion_id = u.id
                WHERE p.activo = 1
                ORDER BY p.nombre ASC";
        return $this->query($sql)->fetchAll();
    }

    /**
     * Búsqueda rápida para AJAX autocomplete.
     */
    public function searchQuick(string $term, int $limit = 10): array
    {
        $sql = "SELECT p.id, p.nombre, p.sku, p.precio, p.stock, p.imagen, p.unidad_medida,
                       c.nombre as categoria_nombre, u.nombre as ubicacion_nombre
                FROM {$this->table} p
                LEFT JOIN categorias c ON p.categoria_id = c.id
                LEFT JOIN ubicaciones u ON p.ubicacion_id = u.id
                WHERE p.activo = 1
                  AND (p.nombre LIKE :term OR p.sku LIKE :term2)
                ORDER BY p.nombre ASC
                LIMIT {$limit}";
        return $this->query($sql, [
            'term' => "%{$term}%",
            'term2' => "%{$term}%",
        ])->fetchAll();
    }
}

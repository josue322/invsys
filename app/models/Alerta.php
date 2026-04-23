<?php
/**
 * InvSys - Modelo Alerta
 */

class Alerta extends Model
{
    protected string $table = 'alertas';

    /**
     * Contar alertas no leídas.
     */
    public function countUnread(): int
    {
        return $this->count('leida = 0');
    }

    /**
     * Obtener alertas no leídas con info del producto.
     */
    public function getUnreadWithProduct(): array
    {
        $sql = "SELECT a.*, p.nombre as producto_nombre, p.sku as producto_sku, p.stock 
                FROM {$this->table} a 
                INNER JOIN productos p ON a.producto_id = p.id 
                WHERE a.leida = 0 
                ORDER BY a.created_at DESC";
        return $this->query($sql)->fetchAll();
    }

    /**
     * Obtener todas las alertas con paginación.
     */
    public function getAllWithProduct(int $page = 1, int $perPage = 15, string $filter = 'todas'): array
    {
        $offset = ($page - 1) * $perPage;
        $where = '1=1';
        $params = [];

        if ($filter === 'leidas') {
            $where .= " AND a.leida = 1";
        } elseif ($filter === 'no_leidas') {
            $where .= " AND a.leida = 0";
        }

        $countSql = "SELECT COUNT(*) as total FROM {$this->table} a WHERE {$where}";
        $total = $this->query($countSql, $params)->fetch()->total;

        $sql = "SELECT a.*, p.nombre as producto_nombre, p.sku as producto_sku, p.stock 
                FROM {$this->table} a 
                INNER JOIN productos p ON a.producto_id = p.id 
                WHERE {$where}
                ORDER BY a.leida ASC, a.created_at DESC 
                LIMIT {$perPage} OFFSET {$offset}";

        $data = $this->query($sql, $params)->fetchAll();

        return [
            'data'    => $data,
            'total'   => (int) $total,
            'pages'   => (int) ceil($total / $perPage),
            'current' => $page,
            'perPage' => $perPage,
        ];
    }

    /**
     * Buscar alerta no leída por producto y tipo.
     */
    public function findUnreadByProducto(int $productoId, string $tipo): object|false
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE producto_id = :producto_id AND tipo = :tipo AND leida = 0 
                LIMIT 1";
        return $this->query($sql, ['producto_id' => $productoId, 'tipo' => $tipo])->fetch();
    }

    /**
     * Marcar todas las alertas como leídas.
     */
    public function markAllAsRead(): bool
    {
        $sql = "UPDATE {$this->table} SET leida = 1 WHERE leida = 0";
        $this->query($sql);
        return true;
    }

    /**
     * Obtener las N alertas más recientes no leídas (para dropdown AJAX).
     */
    public function getRecent(int $limit = 5): array
    {
        $sql = "SELECT a.*, p.nombre as producto_nombre 
                FROM {$this->table} a 
                INNER JOIN productos p ON a.producto_id = p.id 
                WHERE a.leida = 0 
                ORDER BY a.created_at DESC 
                LIMIT {$limit}";
        return $this->query($sql)->fetchAll();
    }
}

<?php
/**
 * InvSys - Modelo Categoria
 * 
 * Gestión de categorías de productos.
 */

class Categoria extends Model
{
    protected string $table = 'categorias';

    /**
     * Nombre de la columna de estado activo.
     * Override del default 'activo' porque la tabla usa 'activa'.
     */
    protected string $activeColumn = 'activa';

    /**
     * Obtener todas las categorías activas.
     */
    public function getAllActive(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE activa = 1 ORDER BY nombre ASC";
        return $this->query($sql)->fetchAll();
    }

    /**
     * Obtener categorías paginadas con conteo de productos y filtro de búsqueda.
     */
    public function getAllPaginated(int $page = 1, int $perPage = 15, string $search = '', string $status = ''): array
    {
        $offset = ($page - 1) * $perPage;
        $where = '1=1';
        $params = [];

        if (!empty($search)) {
            $where .= " AND (c.nombre LIKE :search OR c.descripcion LIKE :search2)";
            $params['search'] = "%{$search}%";
            $params['search2'] = "%{$search}%";
        }

        if ($status === 'activa') {
            $where .= " AND c.activa = 1";
        } elseif ($status === 'inactiva') {
            $where .= " AND c.activa = 0";
        }

        // Count total
        $countSql = "SELECT COUNT(*) as total FROM {$this->table} c WHERE {$where}";
        $total = $this->query($countSql, $params)->fetch()->total;

        // Get data with product count
        $sql = "SELECT c.*, 
                    COUNT(p.id) as total_productos,
                    SUM(CASE WHEN p.activo = 1 THEN 1 ELSE 0 END) as productos_activos
                FROM {$this->table} c 
                LEFT JOIN productos p ON c.id = p.categoria_id
                WHERE {$where}
                GROUP BY c.id
                ORDER BY c.nombre ASC
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
     * Verificar si ya existe una categoría con ese nombre (excluyendo un ID).
     */
    public function nameExists(string $nombre, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE nombre = :nombre";
        $params = ['nombre' => $nombre];

        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        return (int) $this->query($sql, $params)->fetch()->total > 0;
    }

    /**
     * Verificar si una categoría tiene productos asociados.
     */
    public function hasProducts(int $id): bool
    {
        $sql = "SELECT COUNT(*) as total FROM productos WHERE categoria_id = :id";
        return (int) $this->query($sql, ['id' => $id])->fetch()->total > 0;
    }

    /**
     * Contar categorías activas.
     */
    public function countActive(): int
    {
        return $this->count('activa = 1');
    }
}

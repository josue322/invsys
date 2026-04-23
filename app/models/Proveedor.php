<?php
/**
 * InvSys - Modelo Proveedor
 */

class Proveedor extends Model
{
    protected string $table = 'proveedores';

    /**
     * Obtener todos los proveedores activos, ordenados por nombre.
     */
    public function findAllActive(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE activo = 1 ORDER BY nombre ASC";
        return $this->query($sql)->fetchAll();
    }

    /**
     * Obtener proveedores (paginado y con filtros).
     */
    public function getAll(int $page = 1, int $perPage = 15, string $search = ''): array
    {
        $offset = ($page - 1) * $perPage;
        $where = '1=1';
        $params = [];

        if (!empty($search)) {
            $where .= " AND (nombre LIKE :search OR ruc_dni LIKE :search2 OR contacto LIKE :search3)";
            $params['search'] = "%{$search}%";
            $params['search2'] = "%{$search}%";
            $params['search3'] = "%{$search}%";
        }

        $countSql = "SELECT COUNT(*) as total FROM {$this->table} WHERE {$where}";
        $total = $this->query($countSql, $params)->fetch()->total;

        $sql = "SELECT * FROM {$this->table} WHERE {$where} ORDER BY nombre ASC LIMIT {$perPage} OFFSET {$offset}";
        $data = $this->query($sql, $params)->fetchAll();

        return [
            'data' => $data,
            'total' => (int) $total,
            'pages' => (int) ceil($total / $perPage),
            'current' => $page,
            'perPage' => $perPage,
        ];
    }
}

<?php
/**
 * InvSys - Modelo Ubicacion
 */

class Ubicacion extends Model
{
    protected string $table = 'ubicaciones';
    protected string $activeColumn = 'activa';

    /**
     * Obtener todas las ubicaciones activas.
     */
    public function findAllActive(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE activa = 1 ORDER BY nombre ASC";
        return $this->query($sql)->fetchAll();
    }

    /**
     * Obtener ubicaciones (paginado y filtros).
     */
    public function getAll(int $page = 1, int $perPage = 15, string $search = ''): array
    {
        $offset = ($page - 1) * $perPage;
        $where = '1=1';
        $params = [];

        if (!empty($search)) {
            $where .= " AND (nombre LIKE :search OR descripcion LIKE :search2)";
            $params['search'] = "%{$search}%";
            $params['search2'] = "%{$search}%";
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

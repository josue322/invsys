<?php
/**
 * InvSys - Modelo Devolucion
 */

class Devolucion extends Model
{
    protected string $table = 'devoluciones';

    /**
     * Generar siguiente número correlativo (ej. DEV-0001).
     */
    public function generateNumero(): string
    {
        $sql = "SELECT numero_devolucion FROM {$this->table} ORDER BY id DESC LIMIT 1";
        $ultimo = $this->query($sql)->fetch();

        if (!$ultimo) {
            return 'DEV-0001';
        }

        $numero = (int) substr($ultimo->numero_devolucion, 4);
        return sprintf("DEV-%04d", $numero + 1);
    }

    /**
     * Obtener todas las devoluciones con joins.
     */
    public function getAllWithDetails(int $page = 1, int $perPage = 15, string $search = '', string $estado = ''): array
    {
        $offset = ($page - 1) * $perPage;
        $where = [];
        $params = [];

        if (!empty($search)) {
            $where[] = "(d.numero_devolucion LIKE :search OR dep.nombre LIKE :search2)";
            $params['search'] = "%{$search}%";
            $params['search2'] = "%{$search}%";
        }

        if (!empty($estado)) {
            $where[] = "d.estado = :estado";
            $params['estado'] = $estado;
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $countSql = "SELECT COUNT(*) as total 
                     FROM {$this->table} d 
                     LEFT JOIN departamentos dep ON d.departamento_id = dep.id 
                     {$whereClause}";
        $total = $this->query($countSql, $params)->fetch()->total;

        $sql = "SELECT d.*, 
                       dep.nombre as departamento_nombre,
                       u.nombre as usuario_nombre,
                       req.numero_requisicion
                FROM {$this->table} d 
                LEFT JOIN departamentos dep ON d.departamento_id = dep.id 
                LEFT JOIN usuarios u ON d.usuario_id = u.id 
                LEFT JOIN requisiciones req ON d.requisicion_id = req.id
                {$whereClause}
                ORDER BY d.created_at DESC 
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
     * Obtener una devolución por ID con detalles de relaciones.
     */
    public function getWithRelations(int $id): object|false
    {
        $sql = "SELECT d.*, 
                       dep.nombre as departamento_nombre,
                       u.nombre as usuario_nombre,
                       req.numero_requisicion
                FROM {$this->table} d 
                LEFT JOIN departamentos dep ON d.departamento_id = dep.id 
                LEFT JOIN usuarios u ON d.usuario_id = u.id 
                LEFT JOIN requisiciones req ON d.requisicion_id = req.id
                WHERE d.id = :id";
        return $this->query($sql, ['id' => $id])->fetch();
    }
}

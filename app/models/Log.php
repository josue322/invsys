<?php
/**
 * InvSys - Modelo Log
 */

class Log extends Model
{
    protected string $table = 'logs';

    /**
     * Obtener logs con nombre de usuario.
     */
    public function getAllWithUser(int $page = 1, int $perPage = 20, string $modulo = '', string $fecha = ''): array
    {
        $offset = ($page - 1) * $perPage;
        $where = '1=1';
        $params = [];

        if (!empty($modulo)) {
            $where .= " AND l.modulo = :modulo";
            $params['modulo'] = $modulo;
        }

        if (!empty($fecha)) {
            $where .= " AND DATE(l.created_at) = :fecha";
            $params['fecha'] = $fecha;
        }

        $countSql = "SELECT COUNT(*) as total FROM {$this->table} l WHERE {$where}";
        $total = $this->query($countSql, $params)->fetch()->total;

        $sql = "SELECT l.*, u.nombre as usuario_nombre, u.email as usuario_email
                FROM {$this->table} l 
                LEFT JOIN usuarios u ON l.usuario_id = u.id 
                WHERE {$where}
                ORDER BY l.created_at DESC 
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
     * Obtener módulos distintos para filtro.
     */
    public function getDistinctModules(): array
    {
        $sql = "SELECT DISTINCT modulo FROM {$this->table} ORDER BY modulo";
        return $this->query($sql)->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Obtener logs de un usuario específico (para perfil).
     */
    public function getByUserId(int $userId, int $page = 1, int $perPage = 15): array
    {
        $offset = ($page - 1) * $perPage;

        $countSql = "SELECT COUNT(*) as total FROM {$this->table} WHERE usuario_id = :uid";
        $total = $this->query($countSql, ['uid' => $userId])->fetch()->total;

        $sql = "SELECT * FROM {$this->table} 
                WHERE usuario_id = :uid 
                ORDER BY created_at DESC 
                LIMIT {$perPage} OFFSET {$offset}";

        $data = $this->query($sql, ['uid' => $userId])->fetchAll();

        return [
            'data'    => $data,
            'total'   => (int) $total,
            'pages'   => (int) ceil($total / $perPage),
            'current' => $page,
            'perPage' => $perPage,
        ];
    }
}

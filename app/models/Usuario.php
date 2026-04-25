<?php
/**
 * InvSys - Modelo Usuario
 */

class Usuario extends Model
{
    protected string $table = 'usuarios';

    /**
     * Buscar usuario por email.
     */
    public function findByEmail(string $email): object|false
    {
        return $this->findOneBy('email', $email);
    }

    /**
     * Actualizar último login.
     */
    public function updateLastLogin(int $userId): bool
    {
        $sql = "UPDATE {$this->table} SET ultimo_login = NOW() WHERE id = :id";
        return $this->query($sql, ['id' => $userId])->rowCount() > 0;
    }

    /**
     * Obtener usuarios con su rol (JOIN), con filtros opcionales.
     *
     * @param int    $page    Página actual
     * @param int    $perPage Registros por página
     * @param string $search  Buscar en nombre o email
     * @param int    $rolId   Filtrar por rol (0 = todos)
     * @param string $estado  'activo', 'inactivo' o '' (todos)
     */
    public function getAllWithRole(int $page = 1, int $perPage = 15, string $search = '', int $rolId = 0, string $estado = ''): array
    {
        $offset = ($page - 1) * $perPage;
        $where = [];
        $params = [];

        if (!empty($search)) {
            $where[] = "(u.nombre LIKE :search1 OR u.email LIKE :search2)";
            $params['search1'] = "%{$search}%";
            $params['search2'] = "%{$search}%";
        }
        if ($rolId > 0) {
            $where[] = "u.rol_id = :rol_id";
            $params['rol_id'] = $rolId;
        }
        if ($estado === 'activo') {
            $where[] = "u.activo = 1";
        } elseif ($estado === 'inactivo') {
            $where[] = "u.activo = 0";
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $countSql = "SELECT COUNT(*) as total FROM {$this->table} u {$whereClause}";
        $total = $this->query($countSql, $params)->fetch()->total;

        $sql = "SELECT u.*, r.nombre as rol_nombre 
                FROM {$this->table} u 
                LEFT JOIN roles r ON u.rol_id = r.id 
                {$whereClause}
                ORDER BY u.id DESC 
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
     * Contar usuarios activos por rol.
     * Útil para verificar que no se elimine el último admin.
     */
    public function countActiveByRole(int $rolId): int
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE rol_id = :rol_id AND activo = 1";
        return (int) $this->query($sql, ['rol_id' => $rolId])->fetch()->total;
    }

    /**
     * Obtener un usuario con su rol.
     */
    public function findWithRole(int $id): object|false
    {
        $sql = "SELECT u.*, r.nombre as rol_nombre 
                FROM {$this->table} u 
                LEFT JOIN roles r ON u.rol_id = r.id 
                WHERE u.id = :id LIMIT 1";
        return $this->query($sql, ['id' => $id])->fetch();
    }

    /**
     * Verificar si existe un email (excluyendo un ID).
     */
    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE email = :email";
        $params = ['email' => $email];

        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        return (int) $this->query($sql, $params)->fetch()->total > 0;
    }
}

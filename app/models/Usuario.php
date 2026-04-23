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
     * Obtener usuarios con su rol (JOIN).
     */
    public function getAllWithRole(int $page = 1, int $perPage = 15): array
    {
        $offset = ($page - 1) * $perPage;

        $countSql = "SELECT COUNT(*) as total FROM {$this->table}";
        $total = $this->query($countSql)->fetch()->total;

        $sql = "SELECT u.*, r.nombre as rol_nombre 
                FROM {$this->table} u 
                LEFT JOIN roles r ON u.rol_id = r.id 
                ORDER BY u.id DESC 
                LIMIT {$perPage} OFFSET {$offset}";
        
        $data = $this->query($sql)->fetchAll();

        return [
            'data'    => $data,
            'total'   => (int) $total,
            'pages'   => (int) ceil($total / $perPage),
            'current' => $page,
            'perPage' => $perPage,
        ];
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

<?php
/**
 * InvSys - Modelo Conteo
 * 
 * Sesiones de conteo/auditoría de inventario físico.
 */

class Conteo extends Model
{
    protected string $table = 'conteos';

    /**
     * Obtener todos los conteos con paginación.
     */
    public function getAllPaginated(int $page = 1, int $perPage = 15): array
    {
        $offset = ($page - 1) * $perPage;

        $sqlCount = "SELECT COUNT(*) as total FROM {$this->table}";
        $total = (int) $this->query($sqlCount)->fetch()->total;

        $sql = "SELECT c.*, 
                       u.nombre as usuario_nombre,
                       uc.nombre as cerrado_nombre,
                       (SELECT COUNT(*) FROM conteo_detalle WHERE conteo_id = c.id) as total_productos,
                       (SELECT COUNT(*) FROM conteo_detalle WHERE conteo_id = c.id AND stock_fisico IS NOT NULL) as productos_contados,
                       (SELECT COUNT(*) FROM conteo_detalle WHERE conteo_id = c.id AND stock_fisico IS NOT NULL AND diferencia != 0) as productos_con_diferencia
                FROM {$this->table} c
                LEFT JOIN usuarios u ON c.usuario_id = u.id
                LEFT JOIN usuarios uc ON c.cerrado_por = uc.id
                ORDER BY c.created_at DESC
                LIMIT :limit OFFSET :offset";

        $data = $this->query($sql, ['limit' => $perPage, 'offset' => $offset])->fetchAll();

        return [
            'data'     => $data,
            'total'    => $total,
            'pages'    => ceil($total / $perPage),
            'current'  => $page,
            'perPage'  => $perPage,
        ];
    }

    /**
     * Obtener un conteo con información del filtro.
     */
    public function findWithMeta(int $id): ?object
    {
        $sql = "SELECT c.*, 
                       u.nombre as usuario_nombre,
                       uc.nombre as cerrado_nombre
                FROM {$this->table} c
                LEFT JOIN usuarios u ON c.usuario_id = u.id
                LEFT JOIN usuarios uc ON c.cerrado_por = uc.id
                WHERE c.id = :id
                LIMIT 1";

        $result = $this->query($sql, ['id' => $id])->fetch();
        return $result ?: null;
    }

    /**
     * Obtener resumen de un conteo.
     */
    public function getSummary(int $conteoId): object
    {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN stock_fisico IS NOT NULL THEN 1 ELSE 0 END) as contados,
                    SUM(CASE WHEN stock_fisico IS NOT NULL AND diferencia = 0 THEN 1 ELSE 0 END) as iguales,
                    SUM(CASE WHEN stock_fisico IS NOT NULL AND diferencia > 0 THEN 1 ELSE 0 END) as sobrantes,
                    SUM(CASE WHEN stock_fisico IS NOT NULL AND diferencia < 0 THEN 1 ELSE 0 END) as faltantes
                FROM conteo_detalle
                WHERE conteo_id = :id";

        return $this->query($sql, ['id' => $conteoId])->fetch();
    }

    /**
     * Cerrar un conteo.
     */
    public function close(int $id, int $userId): bool
    {
        $sql = "UPDATE {$this->table} 
                SET estado = 'cerrado', cerrado_por = :userId, fecha_cierre = NOW() 
                WHERE id = :id AND estado = 'abierto'";
        return $this->query($sql, ['id' => $id, 'userId' => $userId])->rowCount() > 0;
    }

    /**
     * Marcar como ajustado.
     */
    public function markAdjusted(int $id): bool
    {
        $sql = "UPDATE {$this->table} SET estado = 'ajustado' WHERE id = :id AND estado = 'cerrado'";
        return $this->query($sql, ['id' => $id])->rowCount() > 0;
    }

    /**
     * Eliminar un conteo (solo si está abierto).
     */
    public function deleteIfOpen(int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id AND estado = 'abierto'";
        return $this->query($sql, ['id' => $id])->rowCount() > 0;
    }
}

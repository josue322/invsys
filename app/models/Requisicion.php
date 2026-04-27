<?php
/**
 * InvSys - Modelo Requisicion
 */

class Requisicion extends Model
{
    protected string $table = 'requisiciones';


    /**
     * Buscar por ID simple
     */
    public function findById(int $id): object|false
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        return $this->query($sql, ['id' => $id])->fetch();
    }

    /**
     * Generar un número de requisición único (Formato: REQ-YYYYMMDD-0001)
     */
    public function generateNumeroRequisicion(): string
    {
        $prefix = 'REQ-' . date('Ymd') . '-';
        $sql = "SELECT numero_requisicion FROM {$this->table} WHERE numero_requisicion LIKE :prefix ORDER BY id DESC LIMIT 1";
        $last = $this->query($sql, ['prefix' => $prefix . '%'])->fetch();

        if ($last) {
            $lastNumber = (int) substr($last->numero_requisicion, -4);
            $newNumber = str_pad((string)($lastNumber + 1), 4, '0', STR_PAD_LEFT);
            return $prefix . $newNumber;
        }

        return $prefix . '0001';
    }

    /**
     * Obtener listado con detalles del departamento y usuario
     */
    public function getAllWithDetails(int $page = 1, int $perPage = 10, string $estado = '', string $fechaDesde = '', string $fechaHasta = ''): array
    {
        $offset = ($page - 1) * $perPage;
        
        $where = "1=1";
        $params = [];

        if ($estado) {
            $where .= " AND r.estado = :estado";
            $params['estado'] = $estado;
        }

        if ($fechaDesde) {
            $where .= " AND r.fecha_solicitud >= :desde";
            $params['desde'] = $fechaDesde;
        }

        if ($fechaHasta) {
            $where .= " AND r.fecha_solicitud <= :hasta";
            $params['hasta'] = $fechaHasta;
        }

        $totalSql = "SELECT COUNT(*) as total FROM {$this->table} r WHERE $where";
        $total = $this->query($totalSql, $params)->fetch()->total;

        $sql = "SELECT r.*, 
                d.nombre as departamento_nombre,
                u.nombre as usuario_nombre
                FROM {$this->table} r
                LEFT JOIN departamentos d ON r.departamento_id = d.id
                LEFT JOIN usuarios u ON r.usuario_id = u.id
                WHERE $where
                ORDER BY r.id DESC
                LIMIT $perPage OFFSET $offset";
                
        $data = $this->query($sql, $params)->fetchAll();

        return [
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'last_page' => ceil($total / $perPage)
        ];
    }

    /**
     * Obtener requisición con sus productos detalles
     */
    public function getWithDetails(int $id): object|false
    {
        $sql = "SELECT r.*, 
                d.nombre as departamento_nombre, d.centro_costo,
                u.nombre as usuario_nombre
                FROM {$this->table} r
                LEFT JOIN departamentos d ON r.departamento_id = d.id
                LEFT JOIN usuarios u ON r.usuario_id = u.id
                WHERE r.id = :id LIMIT 1";
        
        $req = $this->query($sql, ['id' => $id])->fetch();

        if ($req) {
            $sqlDetalles = "SELECT rd.*, 
                            p.nombre as producto_nombre, 
                            p.sku, 
                            p.unidad_medida, 
                            p.es_perecedero
                            FROM requisicion_detalles rd
                            JOIN productos p ON rd.producto_id = p.id
                            WHERE rd.requisicion_id = :id";
            $req->detalles = $this->query($sqlDetalles, ['id' => $id])->fetchAll();
        }

        return $req;
    }
}

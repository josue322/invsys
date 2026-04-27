<?php
/**
 * InvSys - Modelo OrdenCompra
 */

class OrdenCompra extends Model
{
    protected string $table = 'ordenes_compra';

    /**
     * Obtener listado de órdenes con proveedor y usuario
     */
    public function getAllWithDetails(int $page = 1, int $perPage = 15, string $estado = '', string $fechaDesde = '', string $fechaHasta = ''): array
    {
        $offset = ($page - 1) * $perPage;
        $where = '1=1';
        $params = [];

        if (!empty($estado)) {
            $where .= " AND o.estado = :estado";
            $params['estado'] = $estado;
        }

        if (!empty($fechaDesde)) {
            $where .= " AND o.fecha_emision >= :fecha_desde";
            $params['fecha_desde'] = $fechaDesde;
        }

        if (!empty($fechaHasta)) {
            $where .= " AND o.fecha_emision <= :fecha_hasta";
            $params['fecha_hasta'] = $fechaHasta;
        }

        $countSql = "SELECT COUNT(*) as total FROM {$this->table} o WHERE {$where}";
        $total = $this->query($countSql, $params)->fetch()->total;

        $sql = "SELECT o.*, p.nombre as proveedor_nombre, p.ruc_dni as proveedor_documento, u.nombre as usuario_nombre
                FROM {$this->table} o
                INNER JOIN proveedores p ON o.proveedor_id = p.id
                INNER JOIN usuarios u ON o.usuario_id = u.id
                WHERE {$where}
                ORDER BY o.created_at DESC
                LIMIT {$perPage} OFFSET {$offset}";

        $data = $this->query($sql, $params)->fetchAll();

        return [
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'last_page' => ceil($total / $perPage)
        ];
    }

    /**
     * Obtener una orden específica con sus detalles
     */
    public function getWithDetails(int $id): ?object
    {
        $sql = "SELECT o.*, p.nombre as proveedor_nombre, p.ruc_dni as proveedor_documento, p.email as proveedor_email, p.telefono as proveedor_telefono, u.nombre as usuario_nombre
                FROM {$this->table} o
                INNER JOIN proveedores p ON o.proveedor_id = p.id
                INNER JOIN usuarios u ON o.usuario_id = u.id
                WHERE o.id = :id";
        
        $orden = $this->query($sql, ['id' => $id])->fetch();
        if (!$orden) return null;

        $detalleModel = new OrdenCompraDetalle();
        $orden->detalles = $detalleModel->getByOrden($id);

        return $orden;
    }

    /**
     * Generar un nuevo número de orden único
     */
    public function generateNumeroOrden(): string
    {
        $prefijo = 'OC-' . date('Ymd') . '-';
        $sql = "SELECT numero_orden FROM {$this->table} WHERE numero_orden LIKE :prefijo ORDER BY id DESC LIMIT 1";
        $ultimo = $this->query($sql, ['prefijo' => $prefijo . '%'])->fetch();

        if ($ultimo) {
            $partes = explode('-', $ultimo->numero_orden);
            $secuencia = (int) end($partes) + 1;
        } else {
            $secuencia = 1;
        }

        return $prefijo . str_pad((string)$secuencia, 4, '0', STR_PAD_LEFT);
    }
}

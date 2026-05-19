<?php
/**
 * InvSys - Modelo NumeroSerie
 * Gestión de trazabilidad unitaria por número de serie.
 */

class NumeroSerie extends Model
{
    protected string $table = 'numeros_serie';

    /**
     * Obtener todos los seriales de un producto.
     */
    public function findByProducto(int $productoId): array
    {
        $sql = "SELECT ns.*, 
                       me.referencia as entrada_ref, me.created_at as fecha_entrada,
                       ms.referencia as salida_ref, ms.created_at as fecha_salida
                FROM {$this->table} ns
                LEFT JOIN movimientos me ON ns.movimiento_entrada_id = me.id
                LEFT JOIN movimientos ms ON ns.movimiento_salida_id = ms.id
                WHERE ns.producto_id = :pid
                ORDER BY ns.created_at DESC";
        return $this->query($sql, ['pid' => $productoId])->fetchAll();
    }

    /**
     * Obtener seriales disponibles de un producto (para salidas).
     */
    public function getDisponiblesByProducto(int $productoId): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE producto_id = :pid AND estado = 'disponible'
                ORDER BY created_at ASC";
        return $this->query($sql, ['pid' => $productoId])->fetchAll();
    }

    /**
     * Contar seriales disponibles de un producto.
     */
    public function countDisponibles(int $productoId): int
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} 
                WHERE producto_id = :pid AND estado = 'disponible'";
        return (int) $this->query($sql, ['pid' => $productoId])->fetch()->total;
    }

    /**
     * Registrar seriales en una entrada de inventario.
     */
    public function registrarEntrada(int $productoId, array $seriales, int $movimientoId): int
    {
        $insertados = 0;
        foreach ($seriales as $serial) {
            $serial = trim($serial);
            if (empty($serial)) continue;

            $this->create([
                'producto_id' => $productoId,
                'numero_serie' => $serial,
                'estado' => 'disponible',
                'movimiento_entrada_id' => $movimientoId,
            ]);
            $insertados++;
        }
        return $insertados;
    }

    /**
     * Registrar salida: marcar seriales como asignados.
     */
    public function registrarSalida(array $serieIds, int $movimientoId): void
    {
        if (empty($serieIds)) return;

        foreach ($serieIds as $id) {
            $sql = "UPDATE {$this->table} 
                    SET estado = 'asignado', movimiento_salida_id = :mid 
                    WHERE id = :id AND estado = 'disponible'";
            $this->query($sql, ['mid' => $movimientoId, 'id' => (int) $id]);
        }
    }

    /**
     * Verificar si un serial ya existe (globalmente en cualquier producto).
     */
    public function serialExists(int $productoId, string $serial): bool
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} 
                WHERE numero_serie = :serial";
        return (int) $this->query($sql, ['serial' => $serial])->fetch()->total > 0;
    }

    /**
     * Contar seriales por estado para un producto.
     */
    public function countByEstado(int $productoId): object
    {
        $sql = "SELECT 
                    SUM(estado = 'disponible') as disponible,
                    SUM(estado = 'asignado') as asignado,
                    SUM(estado = 'en_reparacion') as en_reparacion,
                    SUM(estado = 'dado_de_baja') as dado_de_baja,
                    COUNT(*) as total
                FROM {$this->table}
                WHERE producto_id = :pid";
        return $this->query($sql, ['pid' => $productoId])->fetch();
    }
}

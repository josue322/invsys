<?php
/**
 * InvSys - Modelo DevolucionDetalle
 */

class DevolucionDetalle extends Model
{
    protected string $table = 'devolucion_detalles';

    /**
     * Obtener los detalles de una devolución específica con joins a productos y lotes.
     */
    public function getByDevolucion(int $devolucionId): array
    {
        $sql = "SELECT dd.*, 
                       p.nombre as producto_nombre, 
                       p.sku as producto_sku,
                       l.numero_lote
                FROM {$this->table} dd
                INNER JOIN productos p ON dd.producto_id = p.id
                LEFT JOIN lotes l ON dd.lote_id = l.id
                WHERE dd.devolucion_id = :devolucion_id
                ORDER BY dd.id ASC";
                
        return $this->query($sql, ['devolucion_id' => $devolucionId])->fetchAll();
    }
}

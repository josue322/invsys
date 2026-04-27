<?php
/**
 * InvSys - Modelo Kardex
 * 
 * Modelo virtual — no tiene tabla propia.
 * Genera el Kardex (historial de saldo corrido) a partir
 * de la tabla `movimientos` para un producto específico.
 */

class Kardex extends Model
{
    protected string $table = 'movimientos';

    /**
     * Obtener Kardex de un producto con saldo corrido.
     * Cada fila: fecha, tipo, referencia, entrada, salida, saldo acumulado.
     */
    public function getKardex(int $productoId, ?string $fechaDesde = null, ?string $fechaHasta = null): array
    {
        $sql = "SELECT m.id, m.tipo, m.cantidad, m.observaciones, m.created_at,
                       u.nombre as usuario_nombre,
                       l.numero_lote
                FROM movimientos m
                LEFT JOIN usuarios u ON m.usuario_id = u.id
                LEFT JOIN lotes l ON m.lote_id = l.id
                WHERE m.producto_id = :producto_id";
        $params = ['producto_id' => $productoId];

        if ($fechaDesde) {
            $sql .= " AND DATE(m.created_at) >= :fecha_desde";
            $params['fecha_desde'] = $fechaDesde;
        }
        if ($fechaHasta) {
            $sql .= " AND DATE(m.created_at) <= :fecha_hasta";
            $params['fecha_hasta'] = $fechaHasta;
        }

        $sql .= " ORDER BY m.created_at ASC, m.id ASC";

        $movimientos = $this->query($sql, $params)->fetchAll();

        // Calcular saldo inicial (antes del rango)
        $saldoInicial = 0;
        if ($fechaDesde) {
            $sqlInicial = "SELECT 
                COALESCE(SUM(CASE WHEN tipo = 'entrada' THEN cantidad ELSE 0 END), 0) -
                COALESCE(SUM(CASE WHEN tipo = 'salida' THEN cantidad ELSE 0 END), 0) +
                COALESCE(SUM(CASE WHEN tipo = 'ajuste' THEN cantidad ELSE 0 END), 0)
                as saldo
                FROM movimientos
                WHERE producto_id = :producto_id AND DATE(created_at) < :fecha_desde";
            $result = $this->query($sqlInicial, [
                'producto_id' => $productoId,
                'fecha_desde' => $fechaDesde,
            ])->fetch();
            $saldoInicial = (int) ($result->saldo ?? 0);
        }

        // Construir kardex con saldo corrido
        $kardex = [];
        $saldo = $saldoInicial;

        foreach ($movimientos as $m) {
            $entrada = 0;
            $salida = 0;

            switch ($m->tipo) {
                case 'entrada':
                    $entrada = (int) $m->cantidad;
                    $saldo += $entrada;
                    break;
                case 'salida':
                    $salida = (int) $m->cantidad;
                    $saldo -= $salida;
                    break;
                case 'ajuste':
                    $qty = (int) $m->cantidad;
                    if ($qty >= 0) {
                        $entrada = $qty;
                        $saldo += $qty;
                    } else {
                        $salida = abs($qty);
                        $saldo -= abs($qty);
                    }
                    break;
            }

            $kardex[] = (object) [
                'id' => $m->id,
                'fecha' => $m->created_at,
                'tipo' => $m->tipo,
                'referencia' => $m->numero_lote ?? ($m->observaciones ? mb_substr($m->observaciones, 0, 40) : '—'),
                'entrada' => $entrada,
                'salida' => $salida,
                'saldo' => $saldo,
                'usuario' => $m->usuario_nombre ?? '—',
                'observaciones' => $m->observaciones ?? '',
            ];
        }

        return [
            'saldo_inicial' => $saldoInicial,
            'movimientos' => $kardex,
            'saldo_final' => $saldo,
            'total_entradas' => array_sum(array_column($kardex, 'entrada')),
            'total_salidas' => array_sum(array_column($kardex, 'salida')),
        ];
    }

    /**
     * Obtener Kardex valorizado (con valores monetarios).
     */
    public function getKardexValorizado(int $productoId, ?string $fechaDesde = null, ?string $fechaHasta = null): array
    {
        $producto = (new Producto())->findById($productoId);
        if (!$producto) return ['saldo_inicial' => 0, 'movimientos' => [], 'saldo_final' => 0];

        $precioUnitario = (float) ($producto->precio_compra > 0 ? $producto->precio_compra : $producto->precio);
        $kardexData = $this->getKardex($productoId, $fechaDesde, $fechaHasta);

        foreach ($kardexData['movimientos'] as &$row) {
            $row->valor_entrada = $row->entrada * $precioUnitario;
            $row->valor_salida = $row->salida * $precioUnitario;
            $row->valor_saldo = $row->saldo * $precioUnitario;
        }

        $kardexData['precio_unitario'] = $precioUnitario;
        $kardexData['valor_total_entradas'] = $kardexData['total_entradas'] * $precioUnitario;
        $kardexData['valor_total_salidas'] = $kardexData['total_salidas'] * $precioUnitario;

        return $kardexData;
    }
}

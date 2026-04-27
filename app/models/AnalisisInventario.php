<?php
/**
 * InvSys - Modelo AnalisisInventario
 * 
 * Análisis avanzados sobre el inventario:
 * - Clasificación ABC (Pareto)
 * - Índice de Rotación
 * - Productos sin movimiento (inventario muerto)
 * - Mermas (ajustes negativos)
 */

class AnalisisInventario extends Model
{
    protected string $table = 'productos';

    /**
     * Clasificación ABC por valor de inventario (Precio × Stock).
     * A = 80% del valor total, B = 15%, C = 5%
     * 
     * @return array ['items' => [...], 'totals' => [...]]
     */
    public function getClasificacionABC(): array
    {
        $sql = "SELECT p.id, p.nombre, p.sku, p.stock, p.precio, p.precio_compra,
                       c.nombre as categoria_nombre,
                       (p.precio * p.stock) as valor_inventario
                FROM productos p
                LEFT JOIN categorias c ON p.categoria_id = c.id
                WHERE p.activo = 1 AND p.stock > 0
                ORDER BY valor_inventario DESC";
        
        $productos = $this->query($sql)->fetchAll();

        if (empty($productos)) {
            return ['items' => [], 'totals' => ['A' => 0, 'B' => 0, 'C' => 0, 'total' => 0]];
        }

        $valorTotal = array_sum(array_map(fn($p) => (float) $p->valor_inventario, $productos));
        
        $acumulado = 0;
        $items = [];
        $totals = ['A' => 0, 'B' => 0, 'C' => 0, 'total' => $valorTotal,
                    'count_A' => 0, 'count_B' => 0, 'count_C' => 0];

        foreach ($productos as $p) {
            $valor = (float) $p->valor_inventario;
            $acumulado += $valor;
            $porcentajeAcum = $valorTotal > 0 ? ($acumulado / $valorTotal) * 100 : 0;
            
            if ($porcentajeAcum <= 80) {
                $clase = 'A';
            } elseif ($porcentajeAcum <= 95) {
                $clase = 'B';
            } else {
                $clase = 'C';
            }

            $totals[$clase] += $valor;
            $totals['count_' . $clase]++;

            $items[] = (object) [
                'id' => $p->id,
                'nombre' => $p->nombre,
                'sku' => $p->sku,
                'categoria' => $p->categoria_nombre ?? 'Sin categoría',
                'stock' => (int) $p->stock,
                'precio' => (float) $p->precio,
                'valor' => $valor,
                'porcentaje' => $valorTotal > 0 ? round(($valor / $valorTotal) * 100, 2) : 0,
                'porcentaje_acumulado' => round($porcentajeAcum, 2),
                'clase' => $clase,
            ];
        }

        return ['items' => $items, 'totals' => $totals];
    }

    /**
     * Índice de rotación de inventario.
     * Fórmula: Salidas del período / Stock promedio
     * Un índice alto = producto se mueve rápido
     * 
     * @param int $dias Período en días (default 90)
     */
    public function getRotacion(int $dias = 90): array
    {
        $fechaDesde = date('Y-m-d', strtotime("-{$dias} days"));

        $sql = "SELECT p.id, p.nombre, p.sku, p.stock, p.precio,
                       c.nombre as categoria_nombre,
                       COALESCE(salidas.total_salidas, 0) as total_salidas,
                       COALESCE(entradas.total_entradas, 0) as total_entradas
                FROM productos p
                LEFT JOIN categorias c ON p.categoria_id = c.id
                LEFT JOIN (
                    SELECT producto_id, SUM(cantidad) as total_salidas
                    FROM movimientos
                    WHERE tipo = 'salida' AND DATE(created_at) >= :fecha_desde1
                    GROUP BY producto_id
                ) salidas ON p.id = salidas.producto_id
                LEFT JOIN (
                    SELECT producto_id, SUM(cantidad) as total_entradas
                    FROM movimientos
                    WHERE tipo = 'entrada' AND DATE(created_at) >= :fecha_desde2
                    GROUP BY producto_id
                ) entradas ON p.id = entradas.producto_id
                WHERE p.activo = 1
                ORDER BY total_salidas DESC";

        $productos = $this->query($sql, [
            'fecha_desde1' => $fechaDesde,
            'fecha_desde2' => $fechaDesde,
        ])->fetchAll();

        $items = [];
        foreach ($productos as $p) {
            $salidas = (int) $p->total_salidas;
            $entradas = (int) $p->total_entradas;
            $stock = (int) $p->stock;
            
            // Stock promedio estimado: (stock actual + entradas - salidas + stock actual) / 2
            $stockInicial = $stock - $entradas + $salidas;
            $stockPromedio = max(1, ($stockInicial + $stock) / 2);
            $rotacion = round($salidas / $stockPromedio, 2);

            // Clasificar velocidad
            if ($rotacion >= 3) {
                $velocidad = 'alta';
            } elseif ($rotacion >= 1) {
                $velocidad = 'media';
            } elseif ($rotacion > 0) {
                $velocidad = 'baja';
            } else {
                $velocidad = 'nula';
            }

            // Días de inventario (cuántos días dura el stock actual)
            $diasInventario = $salidas > 0 
                ? round(($stock / ($salidas / $dias)), 0) 
                : ($stock > 0 ? 999 : 0);

            $items[] = (object) [
                'id' => $p->id,
                'nombre' => $p->nombre,
                'sku' => $p->sku,
                'categoria' => $p->categoria_nombre ?? 'Sin categoría',
                'stock' => $stock,
                'salidas' => $salidas,
                'entradas' => $entradas,
                'rotacion' => $rotacion,
                'velocidad' => $velocidad,
                'dias_inventario' => min((int) $diasInventario, 999),
            ];
        }

        return $items;
    }

    /**
     * Productos sin movimiento en los últimos N días (inventario muerto).
     * 
     * @param int $dias Período sin movimiento (default 60)
     */
    public function getProductosSinMovimiento(int $dias = 60): array
    {
        $fechaLimite = date('Y-m-d', strtotime("-{$dias} days"));

        $sql = "SELECT p.id, p.nombre, p.sku, p.stock, p.precio,
                       c.nombre as categoria_nombre,
                       (p.precio * p.stock) as valor_retenido,
                       MAX(m.created_at) as ultimo_movimiento,
                       DATEDIFF(NOW(), MAX(m.created_at)) as dias_sin_movimiento
                FROM productos p
                LEFT JOIN categorias c ON p.categoria_id = c.id
                LEFT JOIN movimientos m ON p.id = m.producto_id
                WHERE p.activo = 1 AND p.stock > 0
                GROUP BY p.id, p.nombre, p.sku, p.stock, p.precio, c.nombre
                HAVING ultimo_movimiento IS NULL OR ultimo_movimiento < :fecha_limite
                ORDER BY valor_retenido DESC";

        return $this->query($sql, ['fecha_limite' => $fechaLimite])->fetchAll();
    }

    /**
     * Mermas: ajustes negativos que representan pérdidas.
     */
    public function getMermas(string $fechaDesde, string $fechaHasta): array
    {
        $sql = "SELECT m.id, m.cantidad, m.observaciones, m.created_at,
                       p.nombre as producto_nombre, p.sku, p.precio,
                       u.nombre as usuario_nombre
                FROM movimientos m
                INNER JOIN productos p ON m.producto_id = p.id
                LEFT JOIN usuarios u ON m.usuario_id = u.id
                WHERE m.tipo = 'ajuste' AND m.cantidad < 0
                  AND DATE(m.created_at) >= :fecha_desde
                  AND DATE(m.created_at) <= :fecha_hasta
                ORDER BY m.created_at DESC";

        return $this->query($sql, [
            'fecha_desde' => $fechaDesde,
            'fecha_hasta' => $fechaHasta,
        ])->fetchAll();
    }

    /**
     * Resumen rápido para el dashboard de análisis.
     */
    public function getResumen(): array
    {
        $productoModel = new Producto();
        
        $totalProductos = (int) $this->query(
            "SELECT COUNT(*) as total FROM productos WHERE activo = 1"
        )->fetch()->total;

        $valorTotal = (float) $this->query(
            "SELECT COALESCE(SUM(precio * stock), 0) as total FROM productos WHERE activo = 1"
        )->fetch()->total;

        $sinMovimiento30 = count($this->getProductosSinMovimiento(30));

        return [
            'total_productos' => $totalProductos,
            'valor_inventario' => $valorTotal,
            'sin_movimiento_30d' => $sinMovimiento30,
        ];
    }
}

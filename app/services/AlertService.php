<?php
/**
 * InvSys - AlertService
 * 
 * Servicio de alertas.
 * Gestiona la creación automática de alertas por stock mínimo,
 * verificación de niveles de inventario y marcado de alertas.
 */

class AlertService
{
    private Alerta $alertaModel;
    private Producto $productoModel;
    private Config $configModel;

    public function __construct()
    {
        $this->alertaModel = new Alerta();
        $this->productoModel = new Producto();
        $this->configModel = new Config();
    }

    /**
     * Verificar stock de un producto después de un movimiento.
     * Crea alertas automáticas si el stock está por debajo del mínimo.
     *
     * @param int $productoId ID del producto a verificar
     */
    public function checkStock(int $productoId): void
    {
        $producto = $this->productoModel->findById($productoId);

        if (!$producto || !$producto->activo) {
            return;
        }

        $stockMinimo = $producto->stock_minimo;

        if ($producto->stock <= 0) {
            // Stock agotado
            $this->createAlert(
                $productoId,
                'stock_agotado',
                "⚠️ STOCK AGOTADO: El producto \"{$producto->nombre}\" (SKU: {$producto->sku}) no tiene existencias."
            );
        } elseif ($producto->stock <= $stockMinimo) {
            // Stock bajo
            $this->createAlert(
                $productoId,
                'stock_minimo',
                "El producto \"{$producto->nombre}\" (SKU: {$producto->sku}) tiene stock bajo ({$producto->stock} unidades). Stock mínimo: {$stockMinimo}."
            );
        }
    }

    /**
     * Crear una alerta si no existe una similar no leída.
     *
     * @param int $productoId
     * @param string $tipo
     * @param string $mensaje
     */
    private function createAlert(int $productoId, string $tipo, string $mensaje): void
    {
        // Verificar si ya existe una alerta similar no leída
        $existente = $this->alertaModel->findUnreadByProducto($productoId, $tipo);

        if (!$existente) {
            $this->alertaModel->create([
                'producto_id' => $productoId,
                'tipo' => $tipo,
                'mensaje' => $mensaje,
                'leida' => 0,
            ]);
        }
    }

    /**
     * Verificar el stock de todos los productos activos.
     * Útil para ejecutar periódicamente o después de configurar stock mínimo.
     */
    public function checkAllProducts(): void
    {
        $productos = $this->productoModel->findAllActive();

        foreach ($productos as $producto) {
            $this->checkStock($producto->id);
        }
    }

    /**
     * Marcar alerta como leída.
     *
     * @param int $alertaId
     * @return bool
     */
    public function markAsRead(int $alertaId): bool
    {
        return $this->alertaModel->update($alertaId, ['leida' => 1]);
    }

    /**
     * Marcar todas las alertas como leídas.
     *
     * @return bool
     */
    public function markAllAsRead(): bool
    {
        return $this->alertaModel->markAllAsRead();
    }

    /**
     * Verificar si los lotes de un producto perecedero están próximos a vencer.
     */
    public function checkExpiration(int $productoId): void
    {
        $producto = $this->productoModel->findById($productoId);

        if (!$producto || !$producto->activo || !$producto->es_perecedero) {
            return;
        }

        // Obtener Lotes Disponibles
        $lotes = (new Lote())->getAvailableByProduct($productoId);

        $diasAlerta = (int) ($this->configModel->getValue('dias_alerta_vencimiento') ?? 30);
        $hoy = strtotime('today');

        foreach ($lotes as $lote) {
            if (empty($lote->fecha_vencimiento))
                continue;

            $fechaVencimiento = strtotime($lote->fecha_vencimiento);
            $diasRestantes = (int) floor(($fechaVencimiento - $hoy) / 86400);

            if ($diasRestantes < 0) {
                // Producto ya vencido
                $this->createAlert(
                    $productoId,
                    'otro',
                    "🚫 LOTE VENCIDO: El Lote {$lote->numero_lote} de \"{$producto->nombre}\" (SKU: {$producto->sku}) venció el " . date('d/m/Y', $fechaVencimiento) . ". Stock comprometido: {$lote->stock_actual} und."
                );
            } elseif ($diasRestantes <= $diasAlerta) {
                // Producto próximo a vencer
                $this->createAlert(
                    $productoId,
                    'otro',
                    "⏰ LOTE POR VENCER: El Lote {$lote->numero_lote} de \"{$producto->nombre}\" (SKU: {$producto->sku}) vence el " . date('d/m/Y', $fechaVencimiento) . " ({$diasRestantes} días restantes)."
                );
            }
        }
    }

    /**
     * Verificar vencimiento de todos los productos perecederos activos.
     * Útil para ejecutar periódicamente.
     */
    public function checkAllExpirations(): void
    {
        $productos = $this->productoModel->findPerishableActive();

        foreach ($productos as $p) {
            $this->checkExpiration($p->id);
        }
    }
}

<?php
/**
 * InvSys - OrdenCompraController
 */

class OrdenCompraController extends Controller
{
    private OrdenCompra $ordenModel;
    private OrdenCompraDetalle $detalleModel;
    private Proveedor $proveedorModel;
    private Producto $productoModel;
    private Movimiento $movimientoModel;
    private SecurityService $securityService;

    public function __construct()
    {
        $this->ordenModel = new OrdenCompra();
        $this->detalleModel = new OrdenCompraDetalle();
        $this->proveedorModel = new Proveedor();
        $this->productoModel = new Producto();
        $this->movimientoModel = new Movimiento();
        $this->securityService = SecurityService::getInstance();
    }

    /**
     * Listado de órdenes de compra
     */
    public function index(): void
    {
        $page = (int) $this->query('page', 1);
        $estado = $this->query('estado', '');
        $fechaDesde = $this->query('fecha_desde', '');
        $fechaHasta = $this->query('fecha_hasta', '');

        $result = $this->ordenModel->getAllWithDetails($page, $this->getPerPage(), $estado, $fechaDesde, $fechaHasta);

        $this->view('compras/index', [
            'titulo' => 'Órdenes de Compra',
            'ordenes' => $result['data'],
            'total' => $result['total'],
            'page' => $result['page'],
            'last_page' => $result['last_page'],
            'filtros' => [
                'estado' => $estado,
                'fecha_desde' => $fechaDesde,
                'fecha_hasta' => $fechaHasta
            ],
            'flash' => $this->getFlash()
        ]);
    }

    /**
     * Formulario para crear orden de compra
     */
    public function crear(): void
    {
        $proveedores = $this->proveedorModel->findAllActive();
        $productos = $this->productoModel->findAllActive();
        $csrfToken = $this->generateCSRF();

        $this->view('compras/crear', [
            'titulo' => 'Nueva Orden de Compra',
            'proveedores' => $proveedores,
            'productos' => $productos,
            'csrfToken' => $csrfToken,
            'flash' => $this->getFlash()
        ]);
    }

    /**
     * Guardar nueva orden de compra
     */
    public function store(): void
    {
        if (!$this->validateCSRF()) {
            $this->redirect('compras/crear');
            return;
        }

        $proveedorId = (int) $this->input('proveedor_id');
        $fechaEsperada = $this->input('fecha_esperada');
        $notas = $this->input('notas', '');
        $estado = $this->input('estado', 'borrador');

        $productosIds = $_POST['producto_id'] ?? [];
        $cantidades = $_POST['cantidad'] ?? [];
        $precios = $_POST['precio_unitario'] ?? [];

        if (empty($proveedorId) || empty($productosIds)) {
            $this->setFlash('error', 'Debe seleccionar un proveedor y al menos un producto.');
            $this->redirect('compras/crear');
            return;
        }

        try {
            $this->ordenModel->beginTransaction();

            $numeroOrden = $this->ordenModel->generateNumeroOrden();

            $total = 0;
            $detalles = [];

            foreach ($productosIds as $index => $prodId) {
                $qty = (int) ($cantidades[$index] ?? 0);
                $price = (float) ($precios[$index] ?? 0);

                if ($qty > 0 && $price >= 0) {
                    $sub = $qty * $price;
                    $total += $sub;
                    $detalles[] = [
                        'producto_id' => (int) $prodId,
                        'cantidad' => $qty,
                        'precio_unitario' => $price,
                        'subtotal' => $sub
                    ];
                }
            }

            if (empty($detalles)) {
                throw new Exception("No hay productos con cantidades válidas.");
            }

            $ordenId = $this->ordenModel->create([
                'numero_orden' => $numeroOrden,
                'proveedor_id' => $proveedorId,
                'usuario_id' => currentUserId(),
                'estado' => $estado,
                'fecha_emision' => date('Y-m-d'),
                'fecha_esperada' => $fechaEsperada ?: null,
                'total' => $total,
                'notas' => $notas
            ]);

            foreach ($detalles as $det) {
                $det['orden_compra_id'] = $ordenId;
                $this->detalleModel->create($det);
            }

            $this->ordenModel->commit();
            $this->securityService->logAction(currentUserId(), 'create', 'compras', "Creó la orden de compra {$numeroOrden}");
            $this->setFlash('success', "Orden de compra {$numeroOrden} generada con éxito.");
            $this->redirect('compras/show/' . $ordenId);

        } catch (Exception $e) {
            $this->ordenModel->rollback();
            $this->setFlash('error', 'Error al guardar la orden: ' . $e->getMessage());
            $this->redirect('compras/crear');
        }
    }

    /**
     * Ver detalles de la orden
     */
    public function show(int $id): void
    {
        $orden = $this->ordenModel->getWithDetails($id);
        if (!$orden) {
            $this->setFlash('error', 'Orden no encontrada.');
            $this->redirect('compras');
            return;
        }

        $csrfToken = $this->generateCSRF();

        $this->view('compras/show', [
            'titulo' => 'Detalle de Orden de Compra',
            'orden' => $orden,
            'csrfToken' => $csrfToken,
            'flash' => $this->getFlash()
        ]);
    }

    /**
     * Marcar orden como recibida y generar movimientos
     */
    public function recibir(int $id): void
    {
        if (!$this->validateCSRF()) {
            $this->redirect('compras/show/' . $id);
            return;
        }

        $orden = $this->ordenModel->getWithDetails($id);
        if (!$orden || $orden->estado === 'recibida' || $orden->estado === 'cancelada') {
            $this->setFlash('error', 'La orden no se puede recibir en su estado actual.');
            $this->redirect('compras');
            return;
        }

        // Recuperar lotes/vencimientos del formulario de recepción
        $lotes = $_POST['lotes'] ?? [];
        $vencimientos = $_POST['vencimientos'] ?? [];

        try {
            $this->ordenModel->beginTransaction();

            // Evitar doble proceso
            $ordenLockResult = $this->ordenModel->rawQuery("SELECT estado FROM ordenes_compra WHERE id = ? FOR UPDATE", [$id]);
            $ordenLock = $ordenLockResult[0] ?? null;
            if ($ordenLock && $ordenLock->estado === 'recibida') {
                throw new Exception("La orden ya fue recibida por otro usuario.");
            }

            $loteModel = new Lote();

            // Crear los movimientos
            foreach ($orden->detalles as $det) {
                // Bloquear producto
                $producto = $this->productoModel->findByIdForUpdate($det->producto_id);
                if (!$producto) {
                    continue;
                }

                $stockAnterior = $producto->stock;
                $stockNuevo = $stockAnterior + $det->cantidad;
                $loteId = null;

                // Actualizar costo del producto al último ingresado
                $this->productoModel->update($producto->id, ['precio_compra' => $det->precio_unitario]);

                if ($producto->es_perecedero) {
                    $numLote = $lotes[$det->id] ?? null;
                    $fVenc = $vencimientos[$det->id] ?? null;

                    if (empty($numLote) || empty($fVenc)) {
                        throw new Exception("El producto {$producto->nombre} requiere Lote y Fecha de Vencimiento.");
                    }

                    $loteId = $loteModel->create([
                        'producto_id' => $producto->id,
                        'numero_lote' => $numLote,
                        'cantidad_inicial' => $det->cantidad,
                        'stock_actual' => $det->cantidad,
                        'fecha_vencimiento' => $fVenc,
                        'proveedor_id' => $orden->proveedor_id,
                        'estado' => 'disponible'
                    ]);
                }

                $this->movimientoModel->create([
                    'producto_id' => $producto->id,
                    'usuario_id' => currentUserId(),
                    'lote_id' => $loteId,
                    'proveedor_id' => $orden->proveedor_id,
                    'destino' => null,
                    'tipo' => 'entrada',
                    'cantidad' => $det->cantidad,
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo' => $stockNuevo,
                    'referencia' => $orden->numero_orden,
                    'observaciones' => "Recepción de Orden de Compra",
                ]);

                $this->productoModel->updateStock($producto->id, $stockNuevo);
            }

            // Marcar orden como recibida
            $this->ordenModel->update($id, ['estado' => 'recibida']);

            $this->ordenModel->commit();
            $this->securityService->logAction(currentUserId(), 'update', 'compras', "Recibió la orden de compra {$orden->numero_orden}");
            $this->setFlash('success', "Orden de compra {$orden->numero_orden} recibida. Inventario actualizado.");

        } catch (Exception $e) {
            $this->ordenModel->rollback();
            $this->setFlash('error', 'Error al recibir la orden: ' . $e->getMessage());
        }

        $this->redirect('compras/show/' . $id);
    }

    /**
     * Cancelar orden
     */
    public function cancelar(int $id): void
    {
        if (!$this->validateCSRF()) {
            $this->redirect('compras/show/' . $id);
            return;
        }

        $orden = $this->ordenModel->findById($id);
        if (!$orden || in_array($orden->estado, ['recibida', 'cancelada'])) {
            $this->setFlash('error', 'No se puede cancelar esta orden.');
            $this->redirect('compras');
            return;
        }

        $this->ordenModel->update($id, ['estado' => 'cancelada']);
        $this->securityService->logAction(currentUserId(), 'update', 'compras', "Canceló la orden de compra {$orden->numero_orden}");
        $this->setFlash('success', "Orden de compra {$orden->numero_orden} cancelada.");

        $this->redirect('compras/show/' . $id);
    }
}

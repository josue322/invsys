<?php
/**
 * InvSys - MovimientoController
 * Actualizado para Gestión Lotes FEFO/FIFO y Proveedores
 */

class MovimientoController extends Controller
{
    private Movimiento $movimientoModel;
    private Producto $productoModel;
    private Proveedor $proveedorModel;
    private Lote $loteModel;
    private SecurityService $securityService;
    private AlertService $alertService;

    public function __construct()
    {
        $this->movimientoModel = new Movimiento();
        $this->productoModel = new Producto();
        $this->proveedorModel = new Proveedor();
        $this->loteModel = new Lote();
        $this->securityService = SecurityService::getInstance();
        $this->alertService = new AlertService();
    }

    /**
     * Listado de movimientos con filtros.
     */
    public function index(): void
    {
        $page = (int) $this->query('page', 1);
        $tipo = $this->query('tipo', '');
        $fechaDesde = $this->query('fecha_desde', '');
        $fechaHasta = $this->query('fecha_hasta', '');
        $productoId = (int) $this->query('producto', 0);

        $movimientos = $this->movimientoModel->getAllWithDetails(
            $page,
            (int) sysConfig('registros_por_pagina', '15'),
            $tipo,
            $fechaDesde,
            $fechaHasta,
            $productoId
        );

        $productos = $this->productoModel->findAllActive();
        $flash = $this->getFlash();

        $this->view('movimientos/index', [
            'titulo' => 'Movimientos de Inventario',
            'movimientos' => $movimientos,
            'productos' => $productos,
            'tipo' => $tipo,
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta,
            'productoId' => $productoId,
            'flash' => $flash,
        ]);
    }

    /**
     * Formulario para registrar un movimiento.
     */
    public function create(): void
    {
        $productos = $this->productoModel->findAllActive();
        $proveedores = $this->proveedorModel->findAllActive();
        $csrfToken = $this->generateCSRF();

        $this->view('movimientos/create', [
            'titulo' => 'Nuevo Movimiento',
            'productos' => $productos,
            'proveedores' => $proveedores,
            'csrfToken' => $csrfToken,
        ]);
    }

    /**
     * Registrar un nuevo movimiento con actualización automática de stock, Lotes y Proveedores.
     */
    public function store(): void
    {
        if (!$this->validateCSRF()) {
            $this->redirect('movimientos/crear');
            return;
        }

        $productoId = (int) $this->input('producto_id');
        $tipo = $this->input('tipo');
        $cantidad = (int) $this->input('cantidad');
        $referencia = $this->input('referencia', '');
        $observaciones = $this->input('observaciones', '');

        // Campos Corporativos WMS
        $proveedorId = (int) $this->input('proveedor_id', 0) ?: null;
        $destino = $this->input('destino', null);
        $numeroLote = $this->input('numero_lote', '');
        $fechaVencimiento = $this->input('fecha_vencimiento', '');

        // Validaciones Básicas (antes de la transacción)
        $errors = [];
        if ($productoId <= 0)
            $errors[] = 'Debe seleccionar un producto.';
        if (!in_array($tipo, ['entrada', 'salida', 'ajuste']))
            $errors[] = 'Tipo de movimiento inválido.';
        if ($cantidad <= 0)
            $errors[] = 'La cantidad debe ser mayor a 0.';

        // Verificar que el producto existe (lectura rápida sin lock)
        $productoCheck = $productoId > 0 ? $this->productoModel->findById($productoId) : null;
        if (!$productoCheck && $productoId > 0)
            $errors[] = 'Producto no encontrado.';

        // Validaciones Lotes (Entradas)
        if ($productoCheck && $productoCheck->es_perecedero && $tipo === 'entrada') {
            if (empty($numeroLote))
                $errors[] = 'El producto es perecedero. Requiere un Número de Lote.';
            if (empty($fechaVencimiento))
                $errors[] = 'El producto es perecedero. Requiere una Fecha de Vencimiento.';
        }

        if (!empty($errors)) {
            $this->setFlash('error', implode('<br>', $errors));
            $this->redirect('movimientos/crear');
            return;
        }

        // ─── INICIO DE TRANSACCIÓN CON BLOQUEO ───
        $this->movimientoModel->beginTransaction();

        try {
            // Obtener producto con bloqueo de fila (FOR UPDATE).
            // Esto impide que otro usuario modifique el stock de este
            // producto hasta que esta transacción termine (commit/rollback).
            $producto = $this->productoModel->findByIdForUpdate($productoId);

            if (!$producto) {
                $this->movimientoModel->rollback();
                $this->setFlash('error', 'Producto no encontrado.');
                $this->redirect('movimientos/crear');
                return;
            }

            $stockAnterior = $producto->stock;

            // Validar stock negativo (con datos bloqueados, 100% confiable)
            if ($tipo === 'salida' && ($stockAnterior - $cantidad < 0)) {
                $permitirNegativo = filter_var(sysConfig('permitir_stock_negativo', '0'), FILTER_VALIDATE_BOOLEAN);
                if (!$permitirNegativo) {
                    $this->movimientoModel->rollback();
                    $this->setFlash('error', "Stock insuficiente. Actual: {$stockAnterior}, Solicitado: {$cantidad}.");
                    $this->redirect('movimientos/crear');
                    return;
                }
            }

            if ($tipo === 'entrada') {
                // ENTRADA
                $stockNuevo = $stockAnterior + $cantidad;
                $loteId = null;

                if ($producto->es_perecedero) {
                    $loteId = $this->loteModel->create([
                        'producto_id' => $productoId,
                        'numero_lote' => $numeroLote,
                        'cantidad_inicial' => $cantidad,
                        'stock_actual' => $cantidad,
                        'fecha_vencimiento' => $fechaVencimiento,
                        'proveedor_id' => $proveedorId,
                        'estado' => 'disponible'
                    ]);
                }

                $this->movimientoModel->create([
                    'producto_id' => $productoId,
                    'usuario_id' => currentUserId(),
                    'lote_id' => $loteId,
                    'proveedor_id' => $proveedorId,
                    'destino' => null,
                    'tipo' => $tipo,
                    'cantidad' => $cantidad,
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo' => $stockNuevo,
                    'referencia' => $referencia,
                    'observaciones' => $observaciones,
                ]);

                $this->productoModel->updateStock($productoId, $stockNuevo);

            } elseif ($tipo === 'salida') {
                // SALIDA
                if ($producto->es_perecedero) {
                    // Lógica FEFO (First Expire, First Out)
                    $lotesDisponibles = $this->loteModel->getAvailableByProduct($productoId);

                    $cantidadRestante = $cantidad;
                    $stockAcumulativo = $stockAnterior;

                    foreach ($lotesDisponibles as $lote) {
                        if ($cantidadRestante <= 0)
                            break;

                        $cantidadADescontar = min($lote->stock_actual, $cantidadRestante);
                        $nuevoStockLote = $lote->stock_actual - $cantidadADescontar;

                        // Actualizar BD de este Lote
                        $this->loteModel->updateStock($lote->id, $nuevoStockLote);

                        // Registrar el movimiento fraccionado para Trazabilidad
                        $stockAcumNuevo = $stockAcumulativo - $cantidadADescontar;
                        $this->movimientoModel->create([
                            'producto_id' => $productoId,
                            'usuario_id' => currentUserId(),
                            'lote_id' => $lote->id,
                            'proveedor_id' => null,
                            'destino' => $destino,
                            'tipo' => $tipo,
                            'cantidad' => $cantidadADescontar,
                            'stock_anterior' => $stockAcumulativo,
                            'stock_nuevo' => $stockAcumNuevo,
                            'referencia' => $referencia,
                            'observaciones' => $observaciones . " (Del Lote: $lote->numero_lote)",
                        ]);

                        $cantidadRestante -= $cantidadADescontar;
                        $stockAcumulativo = $stockAcumNuevo;
                    }

                    if ($cantidadRestante > 0) {
                        // Stock negativo forzado, creamos un movimiento genérico sin lote
                        $stockAcumNuevo = $stockAcumulativo - $cantidadRestante;
                        $this->movimientoModel->create([
                            'producto_id' => $productoId,
                            'usuario_id' => currentUserId(),
                            'lote_id' => null,
                            'proveedor_id' => null,
                            'destino' => $destino,
                            'tipo' => $tipo,
                            'cantidad' => $cantidadRestante,
                            'stock_anterior' => $stockAcumulativo,
                            'stock_nuevo' => $stockAcumNuevo,
                            'referencia' => $referencia,
                            'observaciones' => $observaciones . " (Sin lote suficiente - Negativo)",
                        ]);
                    }

                    $stockNuevo = $stockAnterior - $cantidad;
                    $this->productoModel->updateStock($productoId, $stockNuevo);

                } else {
                    // Salida normal sin lote
                    $stockNuevo = $stockAnterior - $cantidad;
                    $this->movimientoModel->create([
                        'producto_id' => $productoId,
                        'usuario_id' => currentUserId(),
                        'lote_id' => null,
                        'proveedor_id' => null,
                        'destino' => $destino,
                        'tipo' => $tipo,
                        'cantidad' => $cantidad,
                        'stock_anterior' => $stockAnterior,
                        'stock_nuevo' => $stockNuevo,
                        'referencia' => $referencia,
                        'observaciones' => $observaciones,
                    ]);
                    $this->productoModel->updateStock($productoId, $stockNuevo);
                }
            } elseif ($tipo === 'ajuste') {
                // AJUSTE DIRECTO (Inventory Check)
                $stockNuevo = $cantidad; // cantidad represents the new stock

                $this->movimientoModel->create([
                    'producto_id' => $productoId,
                    'usuario_id' => currentUserId(),
                    'lote_id' => null,
                    'proveedor_id' => null,
                    'destino' => null,
                    'tipo' => $tipo,
                    'cantidad' => abs($stockNuevo - $stockAnterior),
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo' => $stockNuevo,
                    'referencia' => $referencia,
                    'observaciones' => $observaciones,
                ]);

                $this->productoModel->updateStock($productoId, $stockNuevo);
            }

            $this->movimientoModel->commit();

            // Alertas (fuera de la transacción para no bloquear más de lo necesario)
            $this->alertService->checkStock($productoId);

            $this->securityService->logAction(currentUserId(), "movimiento_{$tipo}", 'movimientos', "Se registró un movimiento de {$cantidad} para {$producto->nombre}");
            $this->setFlash('success', "Movimiento registrado exitosamente.");
            $this->redirect('movimientos');

        } catch (\Exception $e) {
            $this->movimientoModel->rollback();
            $this->setFlash('error', 'Error al registrar: ' . $e->getMessage());
            $this->redirect('movimientos/crear');
        }
    }
}

<?php
/**
 * InvSys - RequisicionController
 * 
 * Gestiona el proceso de solicitud y despacho de inventario interno.
 */

class RequisicionController extends Controller
{
    private Requisicion $requisicionModel;
    private RequisicionDetalle $detalleModel;
    private Departamento $departamentoModel;
    private Producto $productoModel;
    private Movimiento $movimientoModel;
    private SecurityService $securityService;

    public function __construct()
    {
        $this->requisicionModel = new Requisicion();
        $this->detalleModel = new RequisicionDetalle();
        $this->departamentoModel = new Departamento();
        $this->productoModel = new Producto();
        $this->movimientoModel = new Movimiento();
        $this->securityService = SecurityService::getInstance();
    }

    /**
     * Listado de requisiciones
     */
    public function index(): void
    {
        $page = (int) $this->query('page', 1);
        $estado = $this->query('estado', '');
        $fechaDesde = $this->query('fecha_desde', '');
        $fechaHasta = $this->query('fecha_hasta', '');

        $result = $this->requisicionModel->getAllWithDetails($page, $this->getPerPage(), $estado, $fechaDesde, $fechaHasta);

        $this->view('requisiciones/index', [
            'titulo' => 'Requisiciones Internas',
            'requisiciones' => $result['data'],
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
     * Formulario para crear requisición
     */
    public function crear(): void
    {
        $departamentos = $this->departamentoModel->findAllActive();

        // Obtener productos activos con stock > 0
        $sql = "SELECT p.* FROM productos p WHERE p.activo = 1 AND p.stock > 0 ORDER BY p.nombre ASC";
        $productos = $this->productoModel->rawQuery($sql);

        $this->view('requisiciones/crear', [
            'titulo' => 'Nueva Requisición',
            'departamentos' => $departamentos,
            'productos' => $productos,
            'csrfToken' => $this->generateCSRF(),
            'flash' => $this->getFlash()
        ]);
    }

    /**
     * Guardar requisición
     */
    public function store(): void
    {
        if (!$this->validateCSRF()) {
            $this->redirect('requisiciones/crear');
            return;
        }

        $departamentoId = (int) $this->input('departamento_id');
        $fechaSolicitud = $this->input('fecha_solicitud', date('Y-m-d'));
        $notas = $this->input('notas', '');
        $estado = $this->input('estado', 'borrador');

        $productosIds = $this->input('producto_id', []);
        $cantidades = $this->input('cantidad', []);

        if (empty($departamentoId) || empty($productosIds)) {
            $this->setFlash('error', 'Debe seleccionar un departamento y al menos un producto.');
            $this->redirect('requisiciones/crear');
            return;
        }

        try {
            $this->requisicionModel->beginTransaction();

            $numeroRequisicion = $this->requisicionModel->generateNumeroRequisicion();
            $detalles = [];

            foreach ($productosIds as $index => $prodId) {
                $qty = (int) ($cantidades[$index] ?? 0);

                if ($qty > 0) {
                    $detalles[] = [
                        'producto_id' => (int) $prodId,
                        'cantidad_solicitada' => $qty,
                        'cantidad_despachada' => 0
                    ];
                }
            }

            if (empty($detalles)) {
                throw new Exception("No hay productos con cantidades válidas.");
            }

            $reqId = $this->requisicionModel->create([
                'numero_requisicion' => $numeroRequisicion,
                'departamento_id' => $departamentoId,
                'usuario_id' => currentUserId(),
                'estado' => $estado,
                'fecha_solicitud' => $fechaSolicitud,
                'notas' => $notas
            ]);

            foreach ($detalles as $det) {
                $det['requisicion_id'] = $reqId;
                $this->detalleModel->create($det);
            }

            $this->requisicionModel->commit();
            $this->securityService->logAction(currentUserId(), 'create', 'requisiciones', "Creó requisición $numeroRequisicion para depto ID $departamentoId");

            $this->setFlash('success', "Requisición $numeroRequisicion guardada exitosamente.");
            $this->redirect('requisiciones/show/' . $reqId);

        } catch (Exception $e) {
            $this->requisicionModel->rollback();
            $this->setFlash('error', 'Error al guardar: ' . $e->getMessage());
            $this->redirect('requisiciones/crear');
        }
    }

    /**
     * Ver detalles
     */
    public function show(int $id): void
    {
        $requisicion = $this->requisicionModel->getWithDetails($id);
        if (!$requisicion) {
            $this->setFlash('error', 'Requisición no encontrada.');
            $this->redirect('requisiciones');
            return;
        }

        // Obtener lotes disponibles si la requisición está pendiente o aprobada
        $lotesDisponibles = [];
        if (in_array($requisicion->estado, ['borrador', 'pendiente', 'aprobada'])) {
            $loteModel = new Lote();
            foreach ($requisicion->detalles as $det) {
                if ($det->es_perecedero) {
                    $sql = "SELECT id, numero_lote, stock_actual, fecha_vencimiento 
                            FROM lotes WHERE producto_id = :pid AND stock_actual > 0 AND estado != 'aislado'
                            ORDER BY fecha_vencimiento ASC"; // FEFO (First Expired First Out)
                    $lotesDisponibles[$det->producto_id] = $loteModel->rawQuery($sql, ['pid' => $det->producto_id]);
                }
            }
        }

        $this->view('requisiciones/show', [
            'titulo' => 'Detalle de Requisición',
            'requisicion' => $requisicion,
            'lotesDisponibles' => $lotesDisponibles,
            'csrfToken' => $this->generateCSRF(),
            'flash' => $this->getFlash()
        ]);
    }

    /**
     * Aprobar requisición (Solo Admin/Supervisor)
     */
    public function aprobar(int $id): void
    {
        if (!hasPermission('requisiciones.aprobar')) {
            $this->setFlash('error', 'No tiene permisos para aprobar requisiciones.');
            $this->redirect('requisiciones/show/' . $id);
            return;
        }

        if (!$this->validateCSRF()) {
            $this->redirect('requisiciones/show/' . $id);
            return;
        }

        $requisicion = $this->requisicionModel->findById($id);
        if (!$requisicion || $requisicion->estado !== 'pendiente') {
            $this->setFlash('error', 'Requisición no encontrada o no está en estado pendiente.');
            $this->redirect('requisiciones');
            return;
        }

        $this->requisicionModel->update($id, ['estado' => 'aprobada']);
        $this->securityService->logAction(currentUserId(), 'approve', 'requisiciones', "Aprobó requisición ID $id");

        $this->setFlash('success', 'Requisición aprobada. Lista para despacho.');
        $this->redirect('requisiciones/show/' . $id);
    }

    /**
     * Despachar requisición (Salida de almacén)
     */
    public function despachar(int $id): void
    {
        if (!$this->validateCSRF()) {
            $this->redirect('requisiciones/show/' . $id);
            return;
        }

        $req = $this->requisicionModel->getWithDetails($id);
        if (!$req || $req->estado !== 'aprobada') {
            $this->setFlash('error', 'Requisición no encontrada o no está aprobada para despacho.');
            $this->redirect('requisiciones');
            return;
        }

        // Recuperar información de lotes y despachos del formulario
        $cantidadesADespachar = $this->input('despachar', []);
        $lotesSeleccionados = $this->input('lotes', []);

        try {
            $this->requisicionModel->beginTransaction();

            $reqLockResult = $this->requisicionModel->rawQuery("SELECT estado FROM requisiciones WHERE id = ? FOR UPDATE", [$id]);
            $reqLock = $reqLockResult[0] ?? null;
            if ($reqLock && $reqLock->estado === 'despachada') {
                throw new Exception("Ya fue despachada por otro usuario.");
            }

            $loteModel = new Lote();

            // Procesar cada detalle
            foreach ($req->detalles as $det) {
                $qtyDespachar = (int) ($cantidadesADespachar[$det->id] ?? 0);

                if ($qtyDespachar <= 0) {
                    continue; // No se despacha nada de esta línea
                }

                $producto = $this->productoModel->findByIdForUpdate($det->producto_id);
                if (!$producto)
                    continue;

                if ($producto->stock < $qtyDespachar) {
                    throw new Exception("Stock insuficiente para el producto: {$producto->nombre}. Solicitado: $qtyDespachar, Disponible: {$producto->stock}");
                }

                $loteId = null;
                // Manejo de lotes
                if ($producto->es_perecedero) {
                    $loteId = (int) ($lotesSeleccionados[$det->id] ?? 0);
                    if (!$loteId) {
                        throw new Exception("Debe seleccionar un lote para el producto perecedero: {$producto->nombre}");
                    }

                    $loteLockResult = $loteModel->rawQuery("SELECT stock_actual, estado FROM lotes WHERE id = ? FOR UPDATE", [$loteId]);
                    $lote = $loteLockResult[0] ?? null;

                    if (!$lote || $lote->stock_actual < $qtyDespachar) {
                        throw new Exception("El lote seleccionado para {$producto->nombre} no tiene stock suficiente.");
                    }

                    // Actualizar stock del lote
                    $nuevoStockLote = $lote->stock_actual - $qtyDespachar;
                    $nuevoEstadoLote = $nuevoStockLote == 0 ? 'agotado' : $lote->estado;
                    $loteModel->update($loteId, [
                        'stock_actual' => $nuevoStockLote,
                        'estado' => $nuevoEstadoLote
                    ]);
                }

                // Generar el movimiento de SALIDA
                $stockAnterior = $producto->stock;
                $stockNuevo = $stockAnterior - $qtyDespachar;

                $this->movimientoModel->create([
                    'producto_id' => $producto->id,
                    'usuario_id' => currentUserId(),
                    'lote_id' => $loteId,
                    'proveedor_id' => null,
                    'departamento_id' => $req->departamento_id,
                    'tipo' => 'salida',
                    'cantidad' => $qtyDespachar,
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo' => $stockNuevo,
                    'referencia' => $req->numero_requisicion,
                    'observaciones' => "Despacho de requisición"
                ]);

                // Actualizar stock del producto
                $this->productoModel->updateStock($producto->id, $stockNuevo);

                // Actualizar detalle de requisición
                $this->detalleModel->update($det->id, ['cantidad_despachada' => $qtyDespachar]);
            }

            // Marcar requisición como despachada
            $this->requisicionModel->update($id, [
                'estado' => 'despachada',
                'fecha_despacho' => date('Y-m-d H:i:s')
            ]);

            $this->requisicionModel->commit();
            $this->securityService->logAction(currentUserId(), 'update', 'requisiciones', "Despachó requisición {$req->numero_requisicion}");

            $this->setFlash('success', "Requisición despachada. Inventario rebajado correctamente.");

        } catch (Exception $e) {
            $this->requisicionModel->rollback();
            $this->setFlash('error', $e->getMessage());
        }

        $this->redirect('requisiciones/show/' . $id);
    }

    /**
     * Cancelar
     */
    public function cancelar(int $id): void
    {
        if (!$this->validateCSRF()) {
            $this->redirect('requisiciones/show/' . $id);
            return;
        }

        $req = $this->requisicionModel->findById($id);
        if (!$req || in_array($req->estado, ['despachada', 'cancelada'])) {
            $this->setFlash('error', 'No se puede cancelar en este estado.');
            $this->redirect('requisiciones');
            return;
        }

        $this->requisicionModel->update($id, ['estado' => 'cancelada']);
        $this->securityService->logAction(currentUserId(), 'update', 'requisiciones', "Canceló requisición {$req->numero_requisicion}");
        $this->setFlash('success', "Requisición cancelada.");

        $this->redirect('requisiciones/show/' . $id);
    }

    /**
     * Exportar a PDF
     */
    public function exportPDF(int $id): void
    {
        $requisicion = $this->requisicionModel->getWithDetails($id);
        if (!$requisicion) {
            $this->setFlash('error', 'Requisición no encontrada.');
            $this->redirect('requisiciones');
            return;
        }

        $pdf = new PdfGenerator();
        $pdf->AliasNbPages();
        $pdf->setDocumentTitle('Requisición Interna');
        $pdf->AddPage();

        // Info de cabecera
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(30, 6, 'Numero:', 0, 0);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(60, 6, $requisicion->numero_requisicion, 0, 0);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(30, 6, 'Fecha:', 0, 0);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(60, 6, formatDate($requisicion->fecha_solicitud), 0, 1);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(30, 6, 'Departamento:', 0, 0);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(60, 6, PdfGenerator::decode($requisicion->departamento_nombre), 0, 0);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(30, 6, 'Solicitante:', 0, 0);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(60, 6, PdfGenerator::decode($requisicion->usuario_nombre), 0, 1);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(30, 6, 'Estado:', 0, 0);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(60, 6, strtoupper($requisicion->estado), 0, 1);

        if ($requisicion->notas) {
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(30, 6, 'Notas:', 0, 0);
            $pdf->SetFont('Arial', '', 10);
            $pdf->MultiCell(0, 6, PdfGenerator::decode($requisicion->notas));
        }

        $pdf->Ln(10);

        // Tabla de productos
        $header = ['SKU', 'Producto', 'Cant. Solicitada', 'Cant. Despachada', 'UM'];
        $widths = [30, 70, 35, 35, 20];
        $aligns = ['C', 'L', 'C', 'C', 'C'];

        $data = [];
        foreach ($requisicion->detalles as $det) {
            $data[] = [
                $det->sku,
                $det->producto_nombre,
                $det->cantidad_solicitada,
                $det->cantidad_despachada,
                $det->unidad_medida
            ];
        }

        $pdf->BasicTable($header, $data, $widths, $aligns);

        // Cajones de firma
        $pdf->Ln(30);
        $pdf->SetFont('Arial', '', 10);

        $y = $pdf->GetY();
        $pdf->Line(20, $y, 80, $y);
        $pdf->Line(110, $y, 170, $y);

        $pdf->SetXY(20, $y + 2);
        $pdf->Cell(60, 5, 'Firma Solicitante', 0, 0, 'C');

        $pdf->SetXY(110, $y + 2);
        $pdf->Cell(60, 5, PdfGenerator::decode('Firma Aprobación/Almacén'), 0, 0, 'C');

        // Descargar PDF
        $pdf->Output('D', 'Requisicion_' . $requisicion->numero_requisicion . '.pdf');
    }
}

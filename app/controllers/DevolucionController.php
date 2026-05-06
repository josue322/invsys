<?php
/**
 * InvSys - Controlador de Devoluciones (Fase 10)
 */

class DevolucionController extends Controller
{
    private Devolucion $devolucionModel;
    private DevolucionDetalle $detalleModel;
    private Producto $productoModel;
    private Departamento $departamentoModel;
    private Requisicion $requisicionModel;
    private Movimiento $movimientoModel;
    private Lote $loteModel;

    public function __construct()
    {
        $this->devolucionModel = new Devolucion();
        $this->detalleModel = new DevolucionDetalle();
        $this->productoModel = new Producto();
        $this->departamentoModel = new Departamento();
        $this->requisicionModel = new Requisicion();
        $this->movimientoModel = new Movimiento();
        $this->loteModel = new Lote();
    }

    /**
     * Listado de devoluciones
     */
    public function index(): void
    {
        $page = (int) $this->query('page', 1);
        $search = $this->query('search', '');
        $estado = $this->query('estado', '');

        $devoluciones = $this->devolucionModel->getAllWithDetails($page, $this->getPerPage(), $search, $estado);

        $this->view('devoluciones/index', [
            'titulo' => 'Devoluciones de Inventario',
            'devoluciones' => $devoluciones,
            'search' => $search,
            'estado' => $estado,
            'flash' => $this->getFlash()
        ]);
    }

    /**
     * Formulario para crear nueva devolución
     */
    public function crear(): void
    {
        // Obtener datos para los selects
        $departamentos = $this->departamentoModel->findAllActive();
        $requisiciones = $this->requisicionModel->rawQuery("SELECT id, numero_requisicion FROM requisiciones WHERE estado = 'despachada' ORDER BY id DESC");

        // En lugar de cargar todos los productos de golpe (podrían ser miles), 
        // la vista usará un buscador en AJAX o cargará un límite, pero por simplicidad de UI 
        // cargaremos todos los activos como en requisiciones
        $productos = $this->productoModel->findAllActive();

        $this->view('devoluciones/crear', [
            'titulo' => 'Nueva Devolución',
            'departamentos' => $departamentos,
            'requisiciones' => $requisiciones,
            'productos' => $productos,
            'csrfToken' => $this->generateCSRF(),
            'flash' => $this->getFlash()
        ]);
    }

    /**
     * Guardar la devolución (Estado Borrador/Pendiente)
     */
    public function store(): void
    {
        if (!$this->validateCSRF()) {
            $this->redirect('devoluciones/crear');
            return;
        }

        $departamento_id = (int) $this->input('departamento_id', 0);
        $requisicion_id = !empty($this->input('requisicion_id')) ? (int) $this->input('requisicion_id') : null;
        $notas = $this->input('notas', '');
        $productosSeleccionados = $this->input('productos', []);

        if (empty($productosSeleccionados) || $departamento_id <= 0) {
            $this->setFlash('error', 'Debe seleccionar un departamento y al menos un producto.');
            $this->redirect('devoluciones/crear');
            return;
        }

        try {
            $this->devolucionModel->beginTransaction();

            $numeroDevolucion = $this->devolucionModel->generateNumero();

            $devolucionId = $this->devolucionModel->create([
                'numero_devolucion' => $numeroDevolucion,
                'departamento_id' => $departamento_id,
                'requisicion_id' => $requisicion_id,
                'usuario_id' => currentUserId(),
                'estado' => 'pendiente',
                'fecha_solicitud' => date('Y-m-d'),
                'notas' => $notas
            ]);

            foreach ($productosSeleccionados as $pid) {
                $cantidadesInput = $this->input('cantidades', []);
                $motivosInput = $this->input('motivos', []);
                $estadosInput = $this->input('estados', []);
                
                $cantidad = (int) ($cantidadesInput[$pid] ?? 0);
                $motivo = trim($motivosInput[$pid] ?? '');
                $estadoProd = $estadosInput[$pid] ?? 'bueno';

                if ($cantidad > 0) {
                    $this->detalleModel->create([
                        'devolucion_id' => $devolucionId,
                        'producto_id' => $pid,
                        'cantidad' => $cantidad,
                        'motivo' => empty($motivo) ? 'No especificado' : $motivo,
                        'estado_producto' => $estadoProd
                    ]);
                }
            }

            $this->devolucionModel->commit();
            $this->setFlash('success', "Devolución $numeroDevolucion registrada y pendiente de aprobación.");
            $this->redirect('devoluciones');

        } catch (Exception $e) {
            $this->devolucionModel->rollback();
            $this->setFlash('error', 'Error al registrar: ' . $e->getMessage());
            $this->redirect('devoluciones/crear');
        }
    }

    /**
     * Ver detalles de la devolución y gestionar aprobación
     */
    public function show(int $id): void
    {
        $devolucion = $this->devolucionModel->getWithRelations($id);
        if (!$devolucion) {
            $this->setFlash('error', 'Devolución no encontrada.');
            $this->redirect('devoluciones');
            return;
        }

        $detalles = $this->detalleModel->getByDevolucion($id);

        $this->view('devoluciones/ver', [
            'titulo' => 'Detalle de Devolución',
            'devolucion' => $devolucion,
            'detalles' => $detalles,
            'csrfToken' => $this->generateCSRF(),
            'flash' => $this->getFlash()
        ]);
    }

    /**
     * Aprobar la devolución y retornar productos al stock
     */
    public function aprobar(int $id): void
    {
        if (!$this->validateCSRF()) {
            $this->redirect('devoluciones/ver/' . $id);
            return;
        }

        if (!hasPermission('devoluciones.aprobar')) {
            $this->setFlash('error', 'No tiene permisos para aprobar devoluciones.');
            $this->redirect('devoluciones');
            return;
        }

        $devolucion = $this->devolucionModel->getWithRelations($id);
        if (!$devolucion || $devolucion->estado !== 'pendiente') {
            $this->setFlash('error', 'Estado inválido para aprobar.');
            $this->redirect('devoluciones');
            return;
        }

        $detalles = $this->detalleModel->getByDevolucion($id);

        try {
            $this->devolucionModel->beginTransaction();

            $devLockResult = $this->devolucionModel->rawQuery("SELECT estado FROM devoluciones WHERE id = ? FOR UPDATE", [$id]);
            $devLock = $devLockResult[0] ?? null;
            if ($devLock && $devLock->estado !== 'pendiente') {
                throw new Exception("Ya fue procesada por otro usuario.");
            }

            foreach ($detalles as $det) {
                // Actualizar stock del producto
                $producto = $this->productoModel->findByIdForUpdate($det->producto_id);
                if (!$producto)
                    continue;

                $nuevoStock = $producto->stock + $det->cantidad;
                $this->productoModel->update($producto->id, ['stock' => $nuevoStock]);

                // Registrar el movimiento contable (Kardex)
                // Usamos tipo 'devolucion' que añadimos en la migración
                $observaciones = "Devolución de " . htmlspecialchars($devolucion->departamento_nombre) . ". Motivo: " . $det->motivo . " | Estado: " . $det->estado_producto;
                if ($det->estado_producto === 'dañado') {
                    $observaciones .= " [ATENCIÓN: PRODUCTO DAÑADO. REQUIERE AJUSTE MANUAL DE SALIDA]";
                }

                $this->movimientoModel->create([
                    'producto_id' => $producto->id,
                    'usuario_id' => currentUserId(),
                    'departamento_id' => $devolucion->departamento_id,
                    'tipo' => 'devolucion',
                    'cantidad' => $det->cantidad,
                    'observaciones' => $observaciones,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }

            // Actualizar estado de la devolución
            $this->devolucionModel->update($id, [
                'estado' => 'aprobada',
                'fecha_procesamiento' => date('Y-m-d H:i:s')
            ]);

            $this->devolucionModel->commit();
            $this->setFlash('success', 'Devolución aprobada y stock reingresado exitosamente.');

        } catch (Exception $e) {
            $this->devolucionModel->rollback();
            $this->setFlash('error', 'Error al aprobar: ' . $e->getMessage());
        }

        $this->redirect('devoluciones/ver/' . $id);
    }

    /**
     * Rechazar la devolución
     */
    public function rechazar(int $id): void
    {
        if (!$this->validateCSRF()) {
            $this->redirect('devoluciones/ver/' . $id);
            return;
        }

        if (!hasPermission('devoluciones.aprobar')) {
            $this->setFlash('error', 'No tiene permisos para rechazar devoluciones.');
            $this->redirect('devoluciones');
            return;
        }

        $devolucion = $this->devolucionModel->getWithRelations($id);
        if (!$devolucion || $devolucion->estado !== 'pendiente') {
            $this->setFlash('error', 'Estado inválido para rechazar.');
            $this->redirect('devoluciones');
            return;
        }

        $this->devolucionModel->update($id, [
            'estado' => 'rechazada',
            'fecha_procesamiento' => date('Y-m-d H:i:s')
        ]);

        $this->setFlash('success', 'Devolución rechazada correctamente.');
        $this->redirect('devoluciones/ver/' . $id);
    }
}

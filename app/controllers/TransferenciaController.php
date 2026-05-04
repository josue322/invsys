<?php
/**
 * InvSys - Transferencia Controller
 *
 * Gestiona el movimiento de un producto completo de una ubicación a otra.
 */

class TransferenciaController extends Controller
{
    private Movimiento $movimientoModel;
    private Producto $productoModel;
    private Ubicacion $ubicacionModel;
    private SecurityService $securityService;

    public function __construct()
    {
        $this->movimientoModel = new Movimiento();
        $this->productoModel = new Producto();
        $this->ubicacionModel = new Ubicacion();
        $this->securityService = SecurityService::getInstance();
    }

    /**
     * Listar historial de transferencias.
     */
    public function index(): void
    {
        $page = (int) $this->query('page', 1);
        $perPage = $this->getPerPage();
        $fechaDesde = $this->query('fecha_desde', '');
        $fechaHasta = $this->query('fecha_hasta', '');
        $productoId = (int) $this->query('producto_id', 0);

        // Utilizamos getAllWithDetails del modelo Movimiento filtrando por tipo 'transferencia'
        $result = $this->movimientoModel->getAllWithDetails(
            $page,
            $perPage,
            'transferencia',
            $fechaDesde,
            $fechaHasta,
            $productoId
        );

        $productos = $this->productoModel->findAllActive();

        $this->view('transferencias/index', [
            'title' => 'Historial de Transferencias',
            'movimientos' => $result['data'],
            'total' => $result['total'],
            'pages' => $result['pages'],
            'current' => $result['current'],
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta,
            'productoId' => $productoId,
            'productos' => $productos
        ]);
    }

    /**
     * Mostrar formulario para nueva transferencia.
     */
    public function crear(): void
    {
        $productos = $this->productoModel->getAllActiveWithCategory();
        $ubicaciones = $this->ubicacionModel->findAllActive();

        $this->view('transferencias/crear', [
            'title' => 'Nueva Transferencia de Inventario',
            'productos' => $productos,
            'ubicaciones' => $ubicaciones,
            'csrfToken' => $this->generateCSRF()
        ]);
    }

    /**
     * Procesar la transferencia de un producto a una nueva ubicación.
     */
    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->validateCSRF()) {
            $this->redirect('transferencias/crear');
        }

        $productoId = (int) $this->input('producto_id', 0);
        $nuevaUbicacionId = (int) $this->input('ubicacion_destino_id', 0);
        $observaciones = $this->input('observaciones', '');

        if ($productoId <= 0 || $nuevaUbicacionId <= 0) {
            $this->setFlash('error', 'Debe seleccionar un producto y una ubicación de destino.');
            $this->redirect('transferencias/crear');
        }

        try {
            $this->productoModel->beginTransaction();

            $producto = $this->productoModel->findByIdForUpdate($productoId);
            if (!$producto) {
                throw new \Exception('El producto seleccionado no existe.');
            }

            if ($producto->ubicacion_id == $nuevaUbicacionId) {
                throw new \Exception('El producto ya se encuentra en la ubicación seleccionada.');
            }

            // Obtener nombres de ubicaciones para el historial
            $ubicacionOrigen = $producto->ubicacion_id ? $this->ubicacionModel->findById($producto->ubicacion_id) : null;
            $ubicacionDestino = $this->ubicacionModel->findById($nuevaUbicacionId);

            if (!$ubicacionDestino) {
                throw new \Exception('La ubicación de destino no existe.');
            }

            $nombreOrigen = $ubicacionOrigen ? $ubicacionOrigen->nombre : 'Sin ubicación';
            $nombreDestino = $ubicacionDestino->nombre;

            // 1. Actualizar ubicación del producto
            $this->productoModel->update($productoId, [
                'ubicacion_id' => $nuevaUbicacionId
            ]);

            // 2. Registrar el movimiento en el Kardex
            $notaMovimiento = "Desde: {$nombreOrigen} -> Hacia: {$nombreDestino}";
            if (!empty($observaciones)) {
                $notaMovimiento .= " | Obs: " . $observaciones;
            }

            $this->movimientoModel->create([
                'producto_id' => $productoId,
                'usuario_id' => currentUser()['id'],
                'destino' => "Transferencia a {$nombreDestino}",
                'tipo' => 'transferencia',
                'cantidad' => $producto->stock, // Transferimos el total del stock actual
                'stock_anterior' => $producto->stock,
                'stock_nuevo' => $producto->stock,
                'referencia' => 'TRANSF-' . date('YmdHis'),
                'observaciones' => $notaMovimiento
            ]);

            $this->productoModel->commit();

            $this->securityService->logAction(
                currentUserId(),
                'transferencia',
                'transferencias',
                "Transfirió '{$producto->nombre}' de '{$nombreOrigen}' a '{$nombreDestino}'"
            );

            $this->setFlash('success', "Transferencia completada. El producto '{$producto->nombre}' fue movido a '{$nombreDestino}'.");
            $this->redirect('transferencias');

        } catch (\Exception $e) {
            $this->productoModel->rollback();
            $this->setFlash('error', $e->getMessage());
            $this->redirect('transferencias/crear');
        }
    }
}

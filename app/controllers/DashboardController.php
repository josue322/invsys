<?php
/**
 * InvSys - DashboardController
 */

class DashboardController extends Controller
{
    /**
     * Mostrar dashboard principal con KPIs.
     */
    public function index(): void
    {
        $productoModel = new Producto();
        $movimientoModel = new Movimiento();
        $alertaModel = new Alerta();
        $alertService = new AlertService();

        // Verificar vencimientos (máximo 1 vez por hora para evitar N+1 queries)
        $lastCheck = $_SESSION['last_expiry_check'] ?? 0;
        if (time() - $lastCheck > 3600) {
            $alertService->checkAllExpirations();
            $_SESSION['last_expiry_check'] = time();
        }

        // KPIs
        $totalProductos = $productoModel->countActive();
        $valorInventario = $productoModel->getTotalInventoryValue();
        $alertasActivas = $alertaModel->countUnread();
        $movimientosHoy = $movimientoModel->countToday();
        $productosStockBajo = $productoModel->getLowStock();
        $topProductos = $movimientoModel->getTopProducts(5);
        $productosPorCategoria = $productoModel->getCountByCategory();
        $movimientosSemana = $movimientoModel->getByTypeLastDays(7);
        $productosProximosVencer = $productoModel->getExpiringProducts(30);

        $csrfToken = $this->generateCSRF();
        $flash = $this->getFlash();

        $this->view('dashboard/index', [
            'titulo'              => 'Dashboard',
            'totalProductos'      => $totalProductos,
            'valorInventario'     => $valorInventario,
            'alertasActivas'      => $alertasActivas,
            'movimientosHoy'      => $movimientosHoy,
            'productosStockBajo'  => $productosStockBajo,
            'topProductos'        => $topProductos,
            'productosPorCategoria' => $productosPorCategoria,
            'movimientosSemana'   => $movimientosSemana,
            'productosProximosVencer' => $productosProximosVencer,
            'csrfToken'           => $csrfToken,
            'flash'               => $flash,
            'loadChartJS'         => true,
        ]);
    }

    /**
     * Obtener datos del dashboard en JSON (para AJAX).
     */
    public function getData(): void
    {
        $productoModel = new Producto();
        $movimientoModel = new Movimiento();
        $alertaModel = new Alerta();

        $this->json([
            'totalProductos'      => $productoModel->countActive(),
            'valorInventario'     => $productoModel->getTotalInventoryValue(),
            'alertasActivas'      => $alertaModel->countUnread(),
            'movimientosHoy'      => $movimientoModel->countToday(),
            'productosPorCategoria' => $productoModel->getCountByCategory(),
            'movimientosSemana'   => $movimientoModel->getByTypeLastDays(7),
            'topProductos'        => $movimientoModel->getTopProducts(5),
        ]);
    }
}

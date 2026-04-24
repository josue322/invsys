<?php
/**
 * InvSys - AlertaController
 */

class AlertaController extends Controller
{
    private AlertService $alertService;
    private SecurityService $securityService;

    public function __construct()
    {
        $this->alertService = new AlertService();
        $this->securityService = SecurityService::getInstance();
    }

    /**
     * Listado de alertas.
     */
    public function index(): void
    {
        $page = (int) $this->query('page', 1);
        $filter = $this->query('filter', 'todas');
        
        $alertaModel = new Alerta();
        $alertas = $alertaModel->getAllWithProduct($page, $this->getPerPage(), $filter);

        $csrfToken = $this->generateCSRF();
        $flash = $this->getFlash();

        $this->view('alertas/index', [
            'titulo'    => 'Alertas',
            'alertas'   => $alertas,
            'filter'    => $filter,
            'csrfToken' => $csrfToken,
            'flash'     => $flash,
        ]);
    }

    /**
     * Marcar alerta como leída.
     */
    public function markRead(string $id): void
    {
        $this->alertService->markAsRead((int) $id);

        $this->securityService->logAction(
            currentUserId(), 'marcar_alerta_leida', 'alertas',
            "Alerta #{$id} marcada como leída"
        );

        $this->setFlash('success', 'Alerta marcada como leída.');
        $this->redirect('alertas');
    }

    /**
     * Marcar todas las alertas como leídas.
     */
    public function markAllRead(): void
    {
        $this->alertService->markAllAsRead();

        $this->securityService->logAction(
            currentUserId(), 'marcar_todas_alertas', 'alertas',
            'Todas las alertas marcadas como leídas'
        );

        $this->setFlash('success', 'Todas las alertas marcadas como leídas.');
        $this->redirect('alertas');
    }

    /**
     * Obtener conteo de alertas no leídas (AJAX).
     */
    public function count(): void
    {
        $alertaModel = new Alerta();
        $this->json([
            'count' => $alertaModel->countUnread(),
        ]);
    }

    /**
     * Obtener alertas recientes no leídas (AJAX para dropdown).
     */
    public function recent(): void
    {
        $alertaModel = new Alerta();
        $alertas = $alertaModel->getRecent(5);

        $results = array_map(function ($a) {
            return [
                'id'      => $a->id,
                'tipo'    => $a->tipo,
                'mensaje' => truncate($a->mensaje, 80),
                'fecha'   => formatDate($a->created_at, 'd/m H:i'),
            ];
        }, $alertas);

        $this->json($results);
    }
}

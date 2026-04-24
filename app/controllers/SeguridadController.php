<?php
/**
 * InvSys - SeguridadController
 */

class SeguridadController extends Controller
{
    /**
     * Mostrar logs de auditoría.
     */
    public function index(): void
    {
        $logModel = new Log();

        $page = (int) $this->query('page', 1);
        $modulo = $this->query('modulo', '');
        $fecha = $this->query('fecha', '');

        $logs = $logModel->getAllWithUser($page, $this->getPerPage(), $modulo, $fecha);
        $modulos = $logModel->getDistinctModules();
        $flash = $this->getFlash();

        $this->view('seguridad/index', [
            'titulo'  => 'Seguridad y Auditoría',
            'logs'    => $logs,
            'modulos' => $modulos,
            'modulo'  => $modulo,
            'fecha'   => $fecha,
            'flash'   => $flash,
        ]);
    }
}

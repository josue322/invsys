<?php
/**
 * InvSys - UbicacionController
 *
 * Gestión CRUD completa de ubicaciones de almacén.
 * Incluye listado paginado, búsqueda, crear, editar,
 * activar/desactivar y eliminar (soft-delete).
 */

class UbicacionController extends Controller
{
    private Ubicacion $ubicacionModel;
    private SecurityService $securityService;

    public function __construct()
    {
        $this->ubicacionModel = new Ubicacion();
        $this->securityService = SecurityService::getInstance();
    }

    /**
     * Listado de ubicaciones con filtros y paginación.
     */
    public function index(): void
    {
        $page = (int) $this->query('page', 1);
        $search = $this->query('search', '');
        $data = $this->ubicacionModel->getAll($page, $this->getPerPage(), $search);

        $this->view('ubicaciones/index', [
            'titulo'      => 'Ubicaciones de Almacén',
            'ubicaciones' => $data['data'],
            'total'       => $data['total'],
            'pages'       => $data['pages'],
            'current'     => $data['current'],
            'pagination'  => $data,
            'search'      => $search,
            'flash'       => $this->getFlash(),
            'csrfToken'   => $this->generateCSRF()
        ]);
    }

    /**
     * Formulario de creación.
     */
    public function create(): void
    {
        $this->view('ubicaciones/create', [
            'titulo'    => 'Nueva Ubicación',
            'csrfToken' => $this->generateCSRF()
        ]);
    }

    /**
     * Guardar nueva ubicación.
     */
    public function store(): void
    {
        if (!$this->validateCSRF()) {
            $this->redirect('ubicaciones/crear');
            return;
        }

        $nombre = trim($this->input('nombre', ''));
        $descripcion = trim($this->input('descripcion', ''));

        if (empty($nombre)) {
            $this->setFlash('error', 'El nombre es obligatorio.');
            $this->redirect('ubicaciones/crear');
            return;
        }

        try {
            $id = $this->ubicacionModel->create([
                'nombre'      => $nombre,
                'descripcion' => $descripcion ?: null,
                'activa'      => 1
            ]);

            $this->securityService->logAction(
                currentUserId(), 'crear_ubicacion', 'ubicaciones',
                "Ubicación creada: {$nombre} (ID: {$id})"
            );

            $this->setFlash('success', 'Ubicación creada correctamente.');
            $this->redirect('ubicaciones');
        } catch (\PDOException $e) {
            if ($e->errorInfo[1] === 1062) {
                $this->setFlash('error', 'Ya existe una ubicación con este nombre.');
            } else {
                $this->setFlash('error', 'Error al crear: ' . $e->getMessage());
            }
            $this->redirect('ubicaciones/crear');
        }
    }

    /**
     * Formulario de edición.
     */
    public function edit(string $id): void
    {
        $ubicacion = $this->ubicacionModel->findById((int) $id);
        if (!$ubicacion) {
            $this->setFlash('error', 'Ubicación no encontrada.');
            $this->redirect('ubicaciones');
            return;
        }

        $this->view('ubicaciones/edit', [
            'titulo'    => 'Editar Ubicación',
            'ubicacion' => $ubicacion,
            'csrfToken' => $this->generateCSRF()
        ]);
    }

    /**
     * Actualizar ubicación existente.
     */
    public function update(string $id): void
    {
        $id = (int) $id;

        if (!$this->validateCSRF()) {
            $this->redirect("ubicaciones/editar/{$id}");
            return;
        }

        $ubicacion = $this->ubicacionModel->findById($id);
        if (!$ubicacion) {
            $this->setFlash('error', 'Ubicación no encontrada.');
            $this->redirect('ubicaciones');
            return;
        }

        $nombre = trim($this->input('nombre', ''));
        $descripcion = trim($this->input('descripcion', ''));
        $activa = $this->input('activa') ? 1 : 0;

        if (empty($nombre)) {
            $this->setFlash('error', 'El nombre es obligatorio.');
            $this->redirect("ubicaciones/editar/{$id}");
            return;
        }

        try {
            $this->ubicacionModel->update($id, [
                'nombre'      => $nombre,
                'descripcion' => $descripcion ?: null,
                'activa'      => $activa,
            ]);

            $this->securityService->logAction(
                currentUserId(), 'editar_ubicacion', 'ubicaciones',
                "Ubicación editada: {$nombre} (ID: {$id})"
            );

            $this->setFlash('success', 'Ubicación actualizada correctamente.');
            $this->redirect('ubicaciones');
        } catch (\PDOException $e) {
            if ($e->errorInfo[1] === 1062) {
                $this->setFlash('error', 'Ya existe otra ubicación con ese nombre.');
            } else {
                $this->setFlash('error', 'Error al actualizar ubicación.');
            }
            $this->redirect("ubicaciones/editar/{$id}");
        }
    }

    /**
     * Activar/desactivar ubicación (toggle).
     */
    public function toggle(string $id): void
    {
        $id = (int) $id;

        if (!$this->validateCSRF()) {
            $this->redirect('ubicaciones');
            return;
        }

        $ubicacion = $this->ubicacionModel->findById($id);
        if (!$ubicacion) {
            $this->setFlash('error', 'Ubicación no encontrada.');
            $this->redirect('ubicaciones');
            return;
        }

        $newStatus = $this->ubicacionModel->toggleActive($id);
        $statusText = $newStatus ? 'activada' : 'desactivada';

        $this->securityService->logAction(
            currentUserId(), 'toggle_ubicacion', 'ubicaciones',
            "Ubicación {$statusText}: {$ubicacion->nombre} (ID: {$id})"
        );

        $this->setFlash('success', "Ubicación \"{$ubicacion->nombre}\" {$statusText}.");
        $this->redirect('ubicaciones');
    }

    /**
     * Eliminar ubicación (soft-delete).
     */
    public function destroy(string $id): void
    {
        $id = (int) $id;

        if (!$this->validateCSRF()) {
            $this->redirect('ubicaciones');
            return;
        }

        $ubicacion = $this->ubicacionModel->findById($id);
        if (!$ubicacion) {
            $this->setFlash('error', 'Ubicación no encontrada.');
            $this->redirect('ubicaciones');
            return;
        }

        $this->ubicacionModel->softDelete($id);

        $this->securityService->logAction(
            currentUserId(), 'eliminar_ubicacion', 'ubicaciones',
            "Ubicación desactivada: {$ubicacion->nombre} (ID: {$id})"
        );

        $this->setFlash('success', "Ubicación \"{$ubicacion->nombre}\" eliminada exitosamente.");
        $this->redirect('ubicaciones');
    }
}

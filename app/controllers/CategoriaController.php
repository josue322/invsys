<?php
/**
 * InvSys - CategoriaController
 * 
 * Gestión CRUD completa de categorías de productos.
 * Incluye listado paginado, búsqueda, crear, editar,
 * activar/desactivar y eliminar (con validación de productos asociados).
 */

class CategoriaController extends Controller
{
    private Categoria $categoriaModel;
    private SecurityService $securityService;

    public function __construct()
    {
        $this->categoriaModel = new Categoria();
        $this->securityService = SecurityService::getInstance();
    }

    /**
     * Listado de categorías con filtros y paginación.
     */
    public function index(): void
    {
        $page = (int) $this->query('page', 1);
        $search = $this->query('search', '');
        $status = $this->query('estado', '');

        $categorias = $this->categoriaModel->getAllPaginated($page, $this->getPerPage(), $search, $status);
        $totalActivas = $this->categoriaModel->countActive();

        $csrfToken = $this->generateCSRF();
        $flash = $this->getFlash();

        $this->view('categorias/index', [
            'titulo'        => 'Categorías',
            'categorias'    => $categorias,
            'totalActivas'  => $totalActivas,
            'search'        => $search,
            'status'        => $status,
            'csrfToken'     => $csrfToken,
            'flash'         => $flash,
        ]);
    }

    /**
     * Formulario de creación.
     */
    public function create(): void
    {
        $csrfToken = $this->generateCSRF();

        $this->view('categorias/create', [
            'titulo'    => 'Nueva Categoría',
            'csrfToken' => $csrfToken,
        ]);
    }

    /**
     * Guardar nueva categoría.
     */
    public function store(): void
    {
        if (!$this->validateCSRF()) {
            $this->redirect('categorias/crear');
            return;
        }

        $nombre = trim($this->input('nombre'));
        $descripcion = trim($this->input('descripcion', ''));

        // Validaciones
        $errors = [];
        if (empty($nombre)) {
            $errors[] = 'El nombre de la categoría es obligatorio.';
        } elseif (mb_strlen($nombre) > 100) {
            $errors[] = 'El nombre no puede exceder 100 caracteres.';
        }

        if ($this->categoriaModel->nameExists($nombre)) {
            $errors[] = 'Ya existe una categoría con ese nombre.';
        }

        if (!empty($errors)) {
            $this->setFlash('error', implode('<br>', $errors));
            $this->redirect('categorias/crear');
            return;
        }

        $id = $this->categoriaModel->create([
            'nombre'      => $nombre,
            'descripcion' => $descripcion ?: null,
            'activa'      => 1,
        ]);

        $this->securityService->logAction(
            currentUserId(), 'crear_categoria', 'categorias',
            "Categoría creada: {$nombre} (ID: {$id})"
        );

        $this->setFlash('success', "Categoría \"{$nombre}\" creada exitosamente.");
        $this->redirect('categorias');
    }

    /**
     * Formulario de edición.
     */
    public function edit(string $id): void
    {
        $categoria = $this->categoriaModel->findById((int) $id);
        if (!$categoria) {
            $this->setFlash('error', 'Categoría no encontrada.');
            $this->redirect('categorias');
            return;
        }

        $csrfToken = $this->generateCSRF();

        $this->view('categorias/edit', [
            'titulo'    => 'Editar Categoría',
            'categoria' => $categoria,
            'csrfToken' => $csrfToken,
        ]);
    }

    /**
     * Actualizar categoría existente.
     */
    public function update(string $id): void
    {
        $id = (int) $id;

        if (!$this->validateCSRF()) {
            $this->redirect("categorias/editar/{$id}");
            return;
        }

        $categoria = $this->categoriaModel->findById($id);
        if (!$categoria) {
            $this->setFlash('error', 'Categoría no encontrada.');
            $this->redirect('categorias');
            return;
        }

        $nombre = trim($this->input('nombre'));
        $descripcion = trim($this->input('descripcion', ''));
        $activa = $this->input('activa') ? 1 : 0;

        // Validaciones
        $errors = [];
        if (empty($nombre)) {
            $errors[] = 'El nombre de la categoría es obligatorio.';
        } elseif (mb_strlen($nombre) > 100) {
            $errors[] = 'El nombre no puede exceder 100 caracteres.';
        }

        if ($this->categoriaModel->nameExists($nombre, $id)) {
            $errors[] = 'Ya existe otra categoría con ese nombre.';
        }

        if (!empty($errors)) {
            $this->setFlash('error', implode('<br>', $errors));
            $this->redirect("categorias/editar/{$id}");
            return;
        }

        $this->categoriaModel->update($id, [
            'nombre'      => $nombre,
            'descripcion' => $descripcion ?: null,
            'activa'      => $activa,
        ]);

        $this->securityService->logAction(
            currentUserId(), 'editar_categoria', 'categorias',
            "Categoría editada: {$nombre} (ID: {$id})"
        );

        $this->setFlash('success', "Categoría \"{$nombre}\" actualizada exitosamente.");
        $this->redirect('categorias');
    }

    /**
     * Activar/desactivar categoría (toggle).
     */
    public function toggle(string $id): void
    {
        $id = (int) $id;

        if (!$this->validateCSRF()) {
            $this->redirect('categorias');
            return;
        }

        $categoria = $this->categoriaModel->findById($id);
        if (!$categoria) {
            $this->setFlash('error', 'Categoría no encontrada.');
            $this->redirect('categorias');
            return;
        }

        $newStatus = $this->categoriaModel->toggleActive($id);
        $statusText = $newStatus ? 'activada' : 'desactivada';

        $this->securityService->logAction(
            currentUserId(), 'toggle_categoria', 'categorias',
            "Categoría {$statusText}: {$categoria->nombre} (ID: {$id})"
        );

        $this->setFlash('success', "Categoría \"{$categoria->nombre}\" {$statusText}.");
        $this->redirect('categorias');
    }

    /**
     * Eliminar (desactivar) categoría.
     * Usa soft-delete para consistencia con el resto del sistema.
     * Solo si no tiene productos activos asociados.
     */
    public function destroy(string $id): void
    {
        $id = (int) $id;

        if (!$this->validateCSRF()) {
            $this->redirect('categorias');
            return;
        }

        $categoria = $this->categoriaModel->findById($id);
        if (!$categoria) {
            $this->setFlash('error', 'Categoría no encontrada.');
            $this->redirect('categorias');
            return;
        }

        // Verificar que no tenga productos activos
        if ($this->categoriaModel->hasProducts($id)) {
            $this->setFlash('error', "No se puede eliminar \"{$categoria->nombre}\" porque tiene productos asociados. Desactívela en su lugar.");
            $this->redirect('categorias');
            return;
        }

        // Soft-delete: desactivar en lugar de eliminar permanentemente
        $this->categoriaModel->softDelete($id);

        $this->securityService->logAction(
            currentUserId(), 'eliminar_categoria', 'categorias',
            "Categoría desactivada: {$categoria->nombre} (ID: {$id})"
        );

        $this->setFlash('success', "Categoría \"{$categoria->nombre}\" desactivada exitosamente.");
        $this->redirect('categorias');
    }
}

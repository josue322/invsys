<?php
/**
 * InvSys - DepartamentoController
 * 
 * Gestiona el catálogo de departamentos o áreas que solicitan inventario.
 */

class DepartamentoController extends Controller
{
    private Departamento $departamentoModel;
    private SecurityService $securityService;

    public function __construct()
    {
        $this->departamentoModel = new Departamento();
        $this->securityService = SecurityService::getInstance();
    }

    /**
     * Listado de departamentos
     */
    public function index(): void
    {
        $page = (int) $this->query('page', 1);
        $search = $this->query('search', '');
        
        $perPage = $this->getPerPage();
        $offset = ($page - 1) * $perPage;

        $where = "1=1";
        $params = [];

        if ($search) {
            $where .= " AND nombre LIKE :search";
            $params['search'] = "%{$search}%";
        }

        // Obtener total
        $totalSql = "SELECT COUNT(*) as total FROM departamentos WHERE $where";
        $totalResult = $this->departamentoModel->rawQuery($totalSql, $params);
        $total = $totalResult[0]->total ?? 0;

        // Obtener datos
        $sql = "SELECT * FROM departamentos WHERE $where ORDER BY nombre ASC LIMIT $perPage OFFSET $offset";
        $departamentos = $this->departamentoModel->rawQuery($sql, $params);

        $this->view('departamentos/index', [
            'titulo' => 'Departamentos',
            'departamentos' => $departamentos,
            'total' => $total,
            'page' => $page,
            'last_page' => ceil($total / $perPage),
            'search' => $search,
            'flash' => $this->getFlash()
        ]);
    }

    /**
     * Formulario para crear departamento
     */
    public function create(): void
    {
        $this->view('departamentos/create', [
            'titulo' => 'Nuevo Departamento',
            'csrfToken' => $this->generateCSRF(),
            'flash' => $this->getFlash()
        ]);
    }

    /**
     * Guardar nuevo departamento
     */
    public function store(): void
    {
        if (!$this->validateCSRF()) {
            $this->redirect('departamentos/create');
            return;
        }

        $nombre = trim($this->input('nombre'));
        $responsable = trim($this->input('responsable', ''));
        $centro_costo = trim($this->input('centro_costo', ''));
        $telefono = trim($this->input('telefono', ''));
        
        if (empty($nombre)) {
            $this->setFlash('error', 'El nombre del departamento es obligatorio.');
            $this->redirect('departamentos/create');
            return;
        }

        // Verificar duplicados
        $existe = $this->departamentoModel->rawQuery("SELECT id FROM departamentos WHERE nombre = :n LIMIT 1", ['n' => $nombre]);
        if (!empty($existe)) {
            $this->setFlash('error', 'Ya existe un departamento con ese nombre.');
            $this->redirect('departamentos/create');
            return;
        }

        try {
            $this->departamentoModel->create([
                'nombre' => $nombre,
                'responsable' => $responsable,
                'centro_costo' => $centro_costo,
                'telefono' => $telefono,
                'activo' => 1
            ]);

            $this->securityService->logAction(currentUserId(), 'create', 'departamentos', "Creó el departamento: $nombre");
            $this->setFlash('success', 'Departamento creado exitosamente.');
            $this->redirect('departamentos');
        } catch (Exception $e) {
            $this->setFlash('error', 'Error al crear departamento: ' . $e->getMessage());
            $this->redirect('departamentos/create');
        }
    }

    /**
     * Formulario para editar departamento
     */
    public function edit(int $id): void
    {
        $departamento = $this->departamentoModel->findById($id);
        if (!$departamento) {
            $this->setFlash('error', 'Departamento no encontrado.');
            $this->redirect('departamentos');
            return;
        }

        $this->view('departamentos/edit', [
            'titulo' => 'Editar Departamento',
            'departamento' => $departamento,
            'csrfToken' => $this->generateCSRF(),
            'flash' => $this->getFlash()
        ]);
    }

    /**
     * Actualizar departamento
     */
    public function update(int $id): void
    {
        if (!$this->validateCSRF()) {
            $this->redirect('departamentos/edit/' . $id);
            return;
        }

        $nombre = trim($this->input('nombre'));
        $responsable = trim($this->input('responsable', ''));
        $centro_costo = trim($this->input('centro_costo', ''));
        $telefono = trim($this->input('telefono', ''));
        $activo = (int) $this->input('activo', 1);

        if (empty($nombre)) {
            $this->setFlash('error', 'El nombre es obligatorio.');
            $this->redirect('departamentos/edit/' . $id);
            return;
        }

        // Verificar duplicado
        $existe = $this->departamentoModel->rawQuery("SELECT id FROM departamentos WHERE nombre = :n AND id != :id LIMIT 1", ['n' => $nombre, 'id' => $id]);
        if (!empty($existe)) {
            $this->setFlash('error', 'Ya existe otro departamento con ese nombre.');
            $this->redirect('departamentos/edit/' . $id);
            return;
        }

        try {
            $this->departamentoModel->update($id, [
                'nombre' => $nombre,
                'responsable' => $responsable,
                'centro_costo' => $centro_costo,
                'telefono' => $telefono,
                'activo' => $activo
            ]);

            $this->securityService->logAction(currentUserId(), 'update', 'departamentos', "Actualizó el departamento: $nombre");
            $this->setFlash('success', 'Departamento actualizado exitosamente.');
            $this->redirect('departamentos');
        } catch (Exception $e) {
            $this->setFlash('error', 'Error al actualizar departamento: ' . $e->getMessage());
            $this->redirect('departamentos/edit/' . $id);
        }
    }

    /**
     * Activar / Desactivar departamento
     */
    public function toggle(int $id): void
    {
        if (!$this->validateCSRF()) {
            $this->redirect('departamentos');
            return;
        }

        if (!hasPermission('departamentos.crear')) {
            $this->setFlash('error', 'No tiene permisos para modificar departamentos.');
            $this->redirect('departamentos');
            return;
        }

        $departamento = $this->departamentoModel->findById($id);
        if (!$departamento) {
            $this->setFlash('error', 'Departamento no encontrado.');
            $this->redirect('departamentos');
            return;
        }

        $nuevoEstado = $departamento->activo ? 0 : 1;
        $this->departamentoModel->update($id, ['activo' => $nuevoEstado]);
        
        $accion = $nuevoEstado ? "Activó" : "Desactivó";
        $this->securityService->logAction(currentUserId(), 'update', 'departamentos', "$accion el departamento: {$departamento->nombre}");
        
        $this->setFlash('success', "Departamento " . strtolower($accion) . " exitosamente.");
        $this->redirect('departamentos');
    }

    /**
     * Eliminar departamento (si no tiene dependencias o si se usa borrado lógico/físico seguro)
     */
    public function eliminar(int $id): void
    {
        if (!$this->validateCSRF()) {
            $this->redirect('departamentos');
            return;
        }

        if (!hasPermission('departamentos.eliminar')) {
            $this->setFlash('error', 'No tiene permisos para eliminar departamentos.');
            $this->redirect('departamentos');
            return;
        }

        $departamento = $this->departamentoModel->findById($id);
        if (!$departamento) {
            $this->setFlash('error', 'Departamento no encontrado.');
            $this->redirect('departamentos');
            return;
        }

        try {
            // Eliminar el departamento
            $this->departamentoModel->rawQuery("DELETE FROM departamentos WHERE id = ?", [$id]);

            $this->securityService->logAction(currentUserId(), 'delete', 'departamentos', "Eliminó el departamento: {$departamento->nombre}");
            $this->setFlash('success', 'Departamento eliminado permanentemente.');

        } catch (Exception $e) {
            // Seguramente error por llave foránea
            $this->setFlash('error', 'No se puede eliminar el departamento porque tiene registros asociados (ej: requisiciones, usuarios). Le recomendamos desactivarlo.');
        }

        $this->redirect('departamentos');
    }
}

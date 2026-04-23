<?php
/**
 * InvSys - ProveedorController
 *
 * Gestión CRUD completa de proveedores.
 * Incluye listado paginado, búsqueda, crear, editar,
 * activar/desactivar y eliminar (soft-delete).
 */

class ProveedorController extends Controller
{
    private Proveedor $proveedorModel;
    private SecurityService $securityService;

    public function __construct()
    {
        $this->proveedorModel = new Proveedor();
        $this->securityService = SecurityService::getInstance();
    }

    /**
     * Listado de proveedores con filtros y paginación.
     */
    public function index(): void
    {
        $page = (int) $this->query('page', 1);
        $search = $this->query('search', '');
        $data = $this->proveedorModel->getAll($page, (int) sysConfig('registros_por_pagina', '15'), $search);

        $this->view('proveedores/index', [
            'titulo'      => 'Proveedores',
            'proveedores' => $data['data'],
            'total'       => $data['total'],
            'pages'       => $data['pages'],
            'current'     => $data['current'],
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
        $this->view('proveedores/create', [
            'titulo'    => 'Nuevo Proveedor',
            'csrfToken' => $this->generateCSRF()
        ]);
    }

    /**
     * Guardar nuevo proveedor.
     */
    public function store(): void
    {
        if (!$this->validateCSRF()) {
            $this->redirect('proveedores/crear');
            return;
        }

        $data = $this->getProveedorData();
        $errors = $this->validateProveedor($data);

        if (!empty($errors)) {
            $this->setFlash('error', implode('<br>', $errors));
            $this->redirect('proveedores/crear');
            return;
        }

        $data['activo'] = 1;

        try {
            $id = $this->proveedorModel->create($data);

            $this->securityService->logAction(
                currentUserId(), 'crear_proveedor', 'proveedores',
                "Proveedor creado: {$data['nombre']} (ID: {$id})"
            );

            $this->setFlash('success', 'Proveedor creado correctamente.');
            $this->redirect('proveedores');
        } catch (\PDOException $e) {
            if ($e->errorInfo[1] === 1062) {
                $this->setFlash('error', 'Ya existe un proveedor con ese RUC/DNI.');
            } else {
                $this->setFlash('error', 'Error al crear proveedor.');
            }
            $this->redirect('proveedores/crear');
        }
    }

    /**
     * Formulario de edición.
     */
    public function edit(string $id): void
    {
        $proveedor = $this->proveedorModel->findById((int) $id);
        if (!$proveedor) {
            $this->setFlash('error', 'Proveedor no encontrado.');
            $this->redirect('proveedores');
            return;
        }

        $this->view('proveedores/edit', [
            'titulo'    => 'Editar Proveedor',
            'proveedor' => $proveedor,
            'csrfToken' => $this->generateCSRF()
        ]);
    }

    /**
     * Actualizar proveedor existente.
     */
    public function update(string $id): void
    {
        $id = (int) $id;

        if (!$this->validateCSRF()) {
            $this->redirect("proveedores/editar/{$id}");
            return;
        }

        $proveedor = $this->proveedorModel->findById($id);
        if (!$proveedor) {
            $this->setFlash('error', 'Proveedor no encontrado.');
            $this->redirect('proveedores');
            return;
        }

        $data = $this->getProveedorData();
        $errors = $this->validateProveedor($data, $id);

        if (!empty($errors)) {
            $this->setFlash('error', implode('<br>', $errors));
            $this->redirect("proveedores/editar/{$id}");
            return;
        }

        // Incluir estado si se envió
        $activo = $this->input('activo');
        if ($activo !== null) {
            $data['activo'] = (int) $activo ? 1 : 0;
        }

        try {
            $this->proveedorModel->update($id, $data);

            $this->securityService->logAction(
                currentUserId(), 'editar_proveedor', 'proveedores',
                "Proveedor editado: {$data['nombre']} (ID: {$id})"
            );

            $this->setFlash('success', 'Proveedor actualizado correctamente.');
            $this->redirect('proveedores');
        } catch (\PDOException $e) {
            if ($e->errorInfo[1] === 1062) {
                $this->setFlash('error', 'Ya existe otro proveedor con ese RUC/DNI.');
            } else {
                $this->setFlash('error', 'Error al actualizar proveedor.');
            }
            $this->redirect("proveedores/editar/{$id}");
        }
    }

    /**
     * Activar/desactivar proveedor (toggle).
     */
    public function toggle(string $id): void
    {
        $id = (int) $id;

        if (!$this->validateCSRF()) {
            $this->redirect('proveedores');
            return;
        }

        $proveedor = $this->proveedorModel->findById($id);
        if (!$proveedor) {
            $this->setFlash('error', 'Proveedor no encontrado.');
            $this->redirect('proveedores');
            return;
        }

        $newStatus = $this->proveedorModel->toggleActive($id);
        $statusText = $newStatus ? 'activado' : 'desactivado';

        $this->securityService->logAction(
            currentUserId(), 'toggle_proveedor', 'proveedores',
            "Proveedor {$statusText}: {$proveedor->nombre} (ID: {$id})"
        );

        $this->setFlash('success', "Proveedor \"{$proveedor->nombre}\" {$statusText}.");
        $this->redirect('proveedores');
    }

    /**
     * Eliminar proveedor (soft-delete).
     */
    public function destroy(string $id): void
    {
        $id = (int) $id;

        if (!$this->validateCSRF()) {
            $this->redirect('proveedores');
            return;
        }

        $proveedor = $this->proveedorModel->findById($id);
        if (!$proveedor) {
            $this->setFlash('error', 'Proveedor no encontrado.');
            $this->redirect('proveedores');
            return;
        }

        $this->proveedorModel->softDelete($id);

        $this->securityService->logAction(
            currentUserId(), 'eliminar_proveedor', 'proveedores',
            "Proveedor desactivado: {$proveedor->nombre} (ID: {$id})"
        );

        $this->setFlash('success', "Proveedor \"{$proveedor->nombre}\" eliminado exitosamente.");
        $this->redirect('proveedores');
    }

    // =========================================================
    // MÉTODOS PRIVADOS
    // =========================================================

    /**
     * Obtener datos del formulario de proveedor.
     */
    private function getProveedorData(): array
    {
        return [
            'nombre'   => trim($this->input('nombre', '')),
            'ruc_dni'  => trim($this->input('ruc_dni', '')) ?: null,
            'contacto' => trim($this->input('contacto', '')) ?: null,
            'telefono' => trim($this->input('telefono', '')) ?: null,
            'email'    => trim($this->input('email', '')) ?: null,
            'direccion' => trim($this->input('direccion', '')) ?: null,
        ];
    }

    /**
     * Validar datos del proveedor.
     */
    private function validateProveedor(array $data, ?int $excludeId = null): array
    {
        $errors = [];

        if (empty($data['nombre'])) {
            $errors[] = 'El nombre es obligatorio.';
        } elseif (mb_strlen($data['nombre']) > 150) {
            $errors[] = 'El nombre no puede exceder 150 caracteres.';
        }

        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'El correo electrónico no es válido.';
        }

        // Verificar RUC/DNI único si se proporcionó
        if (!empty($data['ruc_dni'])) {
            $existing = $this->proveedorModel->findOneBy('ruc_dni', $data['ruc_dni']);
            if ($existing && (!$excludeId || $existing->id != $excludeId)) {
                $errors[] = 'Ya existe un proveedor con ese RUC/DNI.';
            }
        }

        return $errors;
    }
}

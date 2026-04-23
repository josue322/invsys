<?php
/**
 * InvSys - ProveedorController
 */

class ProveedorController extends Controller
{
    private Proveedor $proveedorModel;

    public function __construct()
    {
        $this->proveedorModel = new Proveedor();
    }

    public function index(): void
    {
        $page = (int) $this->query('page', 1);
        $search = $this->query('search', '');
        $data = $this->proveedorModel->getAll($page, (int) sysConfig('registros_por_pagina', '15'), $search);
        
        $this->view('proveedores/index', [
            'titulo' => 'Proveedores',
            'proveedores' => $data['data'],
            'total' => $data['total'],
            'pages' => $data['pages'],
            'current' => $data['current'],
            'search' => $search,
            'flash' => $this->getFlash(),
            'csrfToken' => $this->generateCSRF()
        ]);
    }

    public function create(): void
    {
        $this->view('proveedores/create', [
            'titulo' => 'Nuevo Proveedor',
            'csrfToken' => $this->generateCSRF()
        ]);
    }

    public function store(): void
    {
        if (!$this->validateCSRF()) {
            $this->redirect('proveedores/crear');
            return;
        }

        $nombre = trim($this->input('nombre', ''));
        $ruc_dni = trim($this->input('ruc_dni', ''));
        $contacto = trim($this->input('contacto', ''));
        $telefono = trim($this->input('telefono', ''));
        $email = trim($this->input('email', ''));

        if (empty($nombre)) {
            $this->setFlash('error', 'El nombre es obligatorio.');
            $this->redirect('proveedores/crear');
            return;
        }

        try {
            $this->proveedorModel->create([
                'nombre' => $nombre,
                'ruc_dni' => $ruc_dni,
                'contacto' => $contacto,
                'telefono' => $telefono,
                'email' => $email,
                'activo' => 1
            ]);
            $this->setFlash('success', 'Proveedor creado correctamente.');
            $this->redirect('proveedores');
        } catch (\Exception $e) {
            $this->setFlash('error', 'Error al crear proveedor.');
            $this->redirect('proveedores/crear');
        }
    }
}

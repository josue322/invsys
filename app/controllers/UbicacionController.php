<?php
/**
 * InvSys - UbicacionController
 */

class UbicacionController extends Controller
{
    private Ubicacion $ubicacionModel;

    public function __construct()
    {
        $this->ubicacionModel = new Ubicacion();
    }

    public function index(): void
    {
        $page = (int) $this->query('page', 1);
        $search = $this->query('search', '');
        $data = $this->ubicacionModel->getAll($page, (int) sysConfig('registros_por_pagina', '15'), $search);
        
        $this->view('ubicaciones/index', [
            'titulo' => 'Ubicaciones de Almacén',
            'ubicaciones' => $data['data'],
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
        $this->view('ubicaciones/create', [
            'titulo' => 'Nueva Ubicación',
            'csrfToken' => $this->generateCSRF()
        ]);
    }

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
            $this->ubicacionModel->create([
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'activa' => 1
            ]);
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
}

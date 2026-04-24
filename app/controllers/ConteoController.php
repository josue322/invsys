<?php
/**
 * InvSys - ConteoController
 * 
 * Gestión de sesiones de conteo/auditoría de inventario físico.
 * Permite crear sesiones, registrar conteos, comparar diferencias
 * y aplicar ajustes automáticos.
 */

class ConteoController extends Controller
{
    private Conteo $conteoModel;
    private ConteoDetalle $detalleModel;
    private Producto $productoModel;
    private Movimiento $movimientoModel;
    private Categoria $categoriaModel;
    private Ubicacion $ubicacionModel;
    private SecurityService $securityService;
    private AlertService $alertService;

    public function __construct()
    {
        $this->conteoModel      = new Conteo();
        $this->detalleModel     = new ConteoDetalle();
        $this->productoModel    = new Producto();
        $this->movimientoModel  = new Movimiento();
        $this->categoriaModel   = new Categoria();
        $this->ubicacionModel   = new Ubicacion();
        $this->securityService  = SecurityService::getInstance();
        $this->alertService     = new AlertService();
    }

    /**
     * Listar sesiones de conteo.
     */
    public function index(): void
    {
        $page = (int) ($_GET['page'] ?? 1);
        $result = $this->conteoModel->getAllPaginated($page);

        $this->view('conteos/index', [
            'titulo'     => 'Conteo Físico',
            'conteos'    => $result['data'],
            'pagination' => $result,
            'flash'      => $this->getFlash(),
        ]);
    }

    /**
     * Formulario para crear nueva sesión de conteo.
     */
    public function create(): void
    {
        $this->view('conteos/create', [
            'titulo'      => 'Nuevo Conteo Físico',
            'categorias'  => $this->categoriaModel->findAllActive(),
            'ubicaciones' => $this->ubicacionModel->findAllActive(),
            'csrfToken'   => $this->generateCSRF(),
            'flash'       => $this->getFlash(),
        ]);
    }

    /**
     * Crear sesión y cargar productos.
     */
    public function store(): void
    {
        if (!$this->validateCSRF()) {
            $this->redirect('conteos/crear');
            return;
        }

        $nombre      = trim($this->input('nombre', ''));
        $descripcion = trim($this->input('descripcion', ''));
        $filtroTipo  = $this->input('filtro_tipo', 'todos');
        $filtroId    = (int) $this->input('filtro_id', 0) ?: null;

        // Validaciones
        if (empty($nombre)) {
            $this->setFlash('error', 'El nombre de la sesión es obligatorio.');
            $this->redirect('conteos/crear');
            return;
        }

        if (!in_array($filtroTipo, ['todos', 'categoria', 'ubicacion'])) {
            $filtroTipo = 'todos';
        }

        // Obtener productos según filtro
        $productos = $this->getFilteredProducts($filtroTipo, $filtroId);

        if (empty($productos)) {
            $this->setFlash('error', 'No se encontraron productos activos con el filtro seleccionado.');
            $this->redirect('conteos/crear');
            return;
        }

        // Crear sesión
        $conteoId = $this->conteoModel->create([
            'nombre'      => $nombre,
            'descripcion' => $descripcion,
            'estado'      => 'abierto',
            'filtro_tipo' => $filtroTipo,
            'filtro_id'   => $filtroId,
            'usuario_id'  => currentUserId(),
        ]);

        // Cargar productos en la sesión
        $count = $this->detalleModel->loadProducts($conteoId, $productos);

        $this->securityService->logAction(
            currentUserId(), 'crear_conteo', 'conteos',
            "Sesión '{$nombre}' creada con {$count} productos"
        );

        $this->setFlash('success', "Sesión de conteo creada con {$count} productos.");
        $this->redirect("conteos/{$conteoId}");
    }

    /**
     * Ver/editar una sesión de conteo (registrar conteos físicos).
     */
    public function show(string $id): void
    {
        $conteoId = (int) $id;
        $conteo = $this->conteoModel->findWithMeta($conteoId);

        if (!$conteo) {
            $this->setFlash('error', 'Sesión de conteo no encontrada.');
            $this->redirect('conteos');
            return;
        }

        $filter = $_GET['filter'] ?? 'todos';
        $items = $this->detalleModel->getByConteo($conteoId, $filter);
        $summary = $this->conteoModel->getSummary($conteoId);

        // Nombre del filtro aplicado
        $filtroNombre = 'Todos los productos';
        if ($conteo->filtro_tipo === 'categoria' && $conteo->filtro_id) {
            $cat = $this->categoriaModel->findById($conteo->filtro_id);
            $filtroNombre = 'Categoría: ' . ($cat->nombre ?? 'N/A');
        } elseif ($conteo->filtro_tipo === 'ubicacion' && $conteo->filtro_id) {
            $ub = $this->ubicacionModel->findById($conteo->filtro_id);
            $filtroNombre = 'Ubicación: ' . ($ub->nombre ?? 'N/A');
        }

        $this->view('conteos/show', [
            'titulo'       => $conteo->nombre,
            'conteo'       => $conteo,
            'items'        => $items,
            'summary'      => $summary,
            'currentFilter' => $filter,
            'filtroNombre' => $filtroNombre,
            'csrfToken'    => $this->generateCSRF(),
            'flash'        => $this->getFlash(),
        ]);
    }

    /**
     * AJAX: Actualizar conteo físico de un item individual.
     */
    public function updateItem(): void
    {
        header('Content-Type: application/json');

        if (!$this->validateCSRF()) {
            echo json_encode(['success' => false, 'error' => 'Token inválido']);
            return;
        }

        $itemId = (int) $this->input('item_id');
        $stockFisico = $this->input('stock_fisico');
        $observaciones = $this->input('observaciones', '');

        if ($stockFisico === '' || $stockFisico === null) {
            echo json_encode(['success' => false, 'error' => 'Ingrese una cantidad']);
            return;
        }

        $stockFisico = (int) $stockFisico;
        if ($stockFisico < 0) {
            echo json_encode(['success' => false, 'error' => 'La cantidad no puede ser negativa']);
            return;
        }

        // Verificar que el item existe y su conteo está abierto
        $item = $this->detalleModel->findById($itemId);
        if (!$item) {
            echo json_encode(['success' => false, 'error' => 'Item no encontrado']);
            return;
        }

        $conteo = $this->conteoModel->findById($item->conteo_id);
        if (!$conteo || $conteo->estado !== 'abierto') {
            echo json_encode(['success' => false, 'error' => 'Esta sesión ya no permite edición']);
            return;
        }

        $this->detalleModel->updateConteo($itemId, $stockFisico, $observaciones ?: null, currentUserId());

        // Recalcular diferencia
        $diferencia = $stockFisico - $item->stock_sistema;

        // Obtener summary actualizado
        $summary = $this->conteoModel->getSummary($item->conteo_id);

        echo json_encode([
            'success'    => true,
            'diferencia' => $diferencia,
            'summary'    => $summary,
        ]);
    }

    /**
     * Cerrar sesión de conteo.
     */
    public function close(string $id): void
    {
        if (!$this->validateCSRF()) {
            $this->redirect("conteos/{$id}");
            return;
        }

        $conteoId = (int) $id;
        $conteo = $this->conteoModel->findById($conteoId);

        if (!$conteo || $conteo->estado !== 'abierto') {
            $this->setFlash('error', 'No se puede cerrar esta sesión.');
            $this->redirect('conteos');
            return;
        }

        $this->conteoModel->close($conteoId, currentUserId());

        $this->securityService->logAction(
            currentUserId(), 'cerrar_conteo', 'conteos',
            "Sesión #{$conteoId} cerrada"
        );

        $this->setFlash('success', 'Sesión cerrada. Ahora puede revisar las diferencias y aplicar ajustes.');
        $this->redirect("conteos/{$conteoId}");
    }

    /**
     * Aplicar ajustes automáticos basados en las diferencias.
     */
    public function apply(string $id): void
    {
        if (!$this->validateCSRF()) {
            $this->redirect("conteos/{$id}");
            return;
        }

        $conteoId = (int) $id;
        $conteo = $this->conteoModel->findWithMeta($conteoId);

        if (!$conteo || $conteo->estado !== 'cerrado') {
            $this->setFlash('error', 'Solo se pueden aplicar ajustes a sesiones cerradas.');
            $this->redirect('conteos');
            return;
        }

        $diferencias = $this->detalleModel->getDiferencias($conteoId);

        if (empty($diferencias)) {
            $this->setFlash('info', 'No hay diferencias que ajustar. El inventario coincide.');
            $this->redirect("conteos/{$conteoId}");
            return;
        }

        $this->movimientoModel->beginTransaction();

        try {
            $ajustes = 0;

            foreach ($diferencias as $item) {
                // Bloquear producto para evitar race condition
                $producto = $this->productoModel->findByIdForUpdate($item->producto_id);
                if (!$producto) continue;

                $stockNuevo = $item->stock_fisico;

                $this->movimientoModel->create([
                    'producto_id'    => $item->producto_id,
                    'usuario_id'     => currentUserId(),
                    'lote_id'        => null,
                    'proveedor_id'   => null,
                    'destino'        => null,
                    'tipo'           => 'ajuste',
                    'cantidad'       => abs($item->diferencia),
                    'stock_anterior' => $producto->stock,
                    'stock_nuevo'    => $stockNuevo,
                    'referencia'     => "CONTEO-{$conteoId}",
                    'observaciones'  => "Ajuste automático por conteo físico: {$conteo->nombre}",
                ]);

                $this->productoModel->updateStock($item->producto_id, $stockNuevo);
                $ajustes++;
            }

            $this->conteoModel->markAdjusted($conteoId);
            $this->movimientoModel->commit();

            // Verificar alertas
            foreach ($diferencias as $item) {
                $this->alertService->checkStock($item->producto_id);
            }

            $this->securityService->logAction(
                currentUserId(), 'aplicar_ajustes', 'conteos',
                "Aplicados {$ajustes} ajustes del conteo #{$conteoId}"
            );

            $this->setFlash('success', "Se aplicaron {$ajustes} ajustes de inventario correctamente.");
        } catch (\Exception $e) {
            $this->movimientoModel->rollback();
            $this->setFlash('error', 'Error al aplicar ajustes: ' . $e->getMessage());
        }

        $this->redirect("conteos/{$conteoId}");
    }

    /**
     * Eliminar sesión (solo si está abierta).
     */
    public function destroy(string $id): void
    {
        if (!$this->validateCSRF()) {
            $this->redirect('conteos');
            return;
        }

        $conteoId = (int) $id;

        if ($this->conteoModel->deleteIfOpen($conteoId)) {
            $this->securityService->logAction(
                currentUserId(), 'eliminar_conteo', 'conteos',
                "Sesión #{$conteoId} eliminada"
            );
            $this->setFlash('success', 'Sesión de conteo eliminada.');
        } else {
            $this->setFlash('error', 'No se puede eliminar. Solo sesiones abiertas pueden eliminarse.');
        }

        $this->redirect('conteos');
    }

    // =========================================================
    // EXPORTACIÓN
    // =========================================================

    /**
     * Exportar sesión de conteo a PDF.
     */
    public function exportPDF(string $id): void
    {
        $conteoId = (int) $id;
        $conteo = $this->conteoModel->findWithMeta($conteoId);

        if (!$conteo) {
            $this->setFlash('error', 'Sesión de conteo no encontrada.');
            $this->redirect('conteos');
            return;
        }

        $items = $this->detalleModel->getByConteo($conteoId);
        $summary = $this->conteoModel->getSummary($conteoId);
        $export = new ExportService();
        $currentUser = currentUser();

        // Nombre del filtro
        $filtroNombre = 'Todos los productos';
        if ($conteo->filtro_tipo === 'categoria' && $conteo->filtro_id) {
            $cat = $this->categoriaModel->findById($conteo->filtro_id);
            $filtroNombre = 'Categoría: ' . ($cat->nombre ?? 'N/A');
        } elseif ($conteo->filtro_tipo === 'ubicacion' && $conteo->filtro_id) {
            $ub = $this->ubicacionModel->findById($conteo->filtro_id);
            $filtroNombre = 'Ubicación: ' . ($ub->nombre ?? 'N/A');
        }

        $estadoLabel = match($conteo->estado) {
            'abierto'  => 'Abierto',
            'cerrado'  => 'Cerrado',
            'ajustado' => 'Ajustado',
            default    => ucfirst($conteo->estado),
        };

        // Formatear filas
        $rows = array_map(function ($item) {
            $hasCount = $item->stock_fisico !== null;
            $diff = $hasCount ? ($item->stock_fisico - $item->stock_sistema) : null;

            return [
                'sku'             => $item->sku,
                'nombre'          => $item->producto_nombre,
                'categoria'       => $item->categoria_nombre ?? '—',
                'unidad'          => $item->unidad_medida ?? 'Und',
                'stock_sistema'   => $item->stock_sistema,
                'stock_fisico'    => $hasCount ? $item->stock_fisico : '—',
                'diferencia'      => $hasCount ? ($diff >= 0 ? ($diff > 0 ? "+{$diff}" : '0') : (string) $diff) : '—',
                'observaciones'   => $item->observaciones ?? '',
            ];
        }, $items);

        $this->securityService->logAction(
            currentUserId(), 'exportar_conteo_pdf', 'conteos',
            "Exportación PDF del conteo #{$conteoId} ({$conteo->nombre})"
        );

        $pct = $summary->total > 0 ? round(($summary->contados / $summary->total) * 100) : 0;

        $export->exportPDF(
            "Conteo Físico: {$conteo->nombre}",
            "conteo_fisico_{$conteoId}",
            [
                [
                    'title'   => "Detalle del Conteo — {$filtroNombre} ({$summary->total} productos)",
                    'headers' => ['SKU', 'Producto', 'Categoría', 'Unidad', 'Stock Sistema', 'Stock Físico', 'Diferencia', 'Observaciones'],
                    'rows'    => $rows,
                    'keys'    => ['sku', 'nombre', 'categoria', 'unidad', 'stock_sistema', 'stock_fisico', 'diferencia', 'observaciones'],
                    'formatters' => [
                        'sku' => fn($v) => '<span class="text-mono">' . htmlspecialchars($v) . '</span>',
                        'nombre' => fn($v) => '<span class="text-bold">' . htmlspecialchars($v) . '</span>',
                        'diferencia' => function ($v) {
                            if ($v === '—') return '<span style="color:#94a3b8;">—</span>';
                            if ($v === '0') return '<span class="badge-ok">✓ 0</span>';
                            if (str_starts_with($v, '+')) return '<span class="badge-warn">' . $v . '</span>';
                            return '<span class="badge-danger">' . $v . '</span>';
                        },
                        'stock_fisico' => function ($v) {
                            if ($v === '—') return '<span style="color:#94a3b8;">Pendiente</span>';
                            return '<span class="text-bold">' . $v . '</span>';
                        },
                    ],
                ],
            ],
            [
                'usuario' => $currentUser['nombre'] ?? 'Sistema',
                'summary' => [
                    ['label' => 'Estado', 'value' => $estadoLabel],
                    ['label' => 'Progreso', 'value' => "{$summary->contados}/{$summary->total} ({$pct}%)"],
                    ['label' => 'Coinciden', 'value' => $summary->iguales],
                    ['label' => 'Con Diferencia', 'value' => ($summary->sobrantes + $summary->faltantes)],
                ],
            ]
        );
    }

    /**
     * Exportar sesión de conteo a CSV (compatible con Excel).
     */
    public function exportCSV(string $id): void
    {
        $conteoId = (int) $id;
        $conteo = $this->conteoModel->findWithMeta($conteoId);

        if (!$conteo) {
            $this->setFlash('error', 'Sesión de conteo no encontrada.');
            $this->redirect('conteos');
            return;
        }

        $items = $this->detalleModel->getByConteo($conteoId);

        // Formatear filas
        $rows = array_map(function ($item) {
            $hasCount = $item->stock_fisico !== null;
            $diff = $hasCount ? ($item->stock_fisico - $item->stock_sistema) : null;

            return [
                'sku'             => $item->sku,
                'nombre'          => $item->producto_nombre,
                'categoria'       => $item->categoria_nombre ?? 'Sin categoría',
                'ubicacion'       => $item->ubicacion_nombre ?? 'Sin ubicación',
                'unidad'          => $item->unidad_medida ?? 'Und',
                'stock_sistema'   => $item->stock_sistema,
                'stock_fisico'    => $hasCount ? $item->stock_fisico : '',
                'diferencia'      => $hasCount ? $diff : '',
                'estado'          => $hasCount ? ($diff === 0 ? 'Coincide' : ($diff > 0 ? 'Sobrante' : 'Faltante')) : 'Pendiente',
                'observaciones'   => $item->observaciones ?? '',
            ];
        }, $items);

        $this->securityService->logAction(
            currentUserId(), 'exportar_conteo_csv', 'conteos',
            "Exportación CSV del conteo #{$conteoId} ({$conteo->nombre})"
        );

        $export = new ExportService();
        $export->exportCSV(
            "conteo_fisico_{$conteoId}",
            ['SKU', 'Producto', 'Categoría', 'Ubicación', 'Unidad', 'Stock Sistema', 'Stock Físico', 'Diferencia', 'Estado', 'Observaciones'],
            $rows,
            ['sku', 'nombre', 'categoria', 'ubicacion', 'unidad', 'stock_sistema', 'stock_fisico', 'diferencia', 'estado', 'observaciones']
        );
    }

    // =========================================================
    // PRIVADOS
    // =========================================================

    /**
     * Obtener productos filtrados para la sesión.
     */
    private function getFilteredProducts(string $tipo, ?int $filtroId): array
    {
        if ($tipo === 'categoria' && $filtroId) {
            $sql = "SELECT id, stock FROM productos WHERE activo = 1 AND categoria_id = :id ORDER BY nombre";
            return $this->productoModel->rawQuery($sql, ['id' => $filtroId]);
        }

        if ($tipo === 'ubicacion' && $filtroId) {
            $sql = "SELECT id, stock FROM productos WHERE activo = 1 AND ubicacion_id = :id ORDER BY nombre";
            return $this->productoModel->rawQuery($sql, ['id' => $filtroId]);
        }

        // Todos los productos activos
        return $this->productoModel->findAllActive();
    }
}

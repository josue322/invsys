<?php
/**
 * InvSys - ReporteController
 * 
 * Controlador de reportes con funcionalidad de exportación
 * a CSV y PDF. Soporta múltiples tipos de reportes.
 */

class ReporteController extends Controller
{
    private SecurityService $securityService;

    public function __construct()
    {
        $this->securityService = SecurityService::getInstance();
    }
    /**
     * Vista principal de reportes con gráficos y tablas.
     */
    public function index(): void
    {
        $productoModel = new Producto();
        $movimientoModel = new Movimiento();

        $productosStockBajo = $productoModel->getLowStock();
        $topProductos = $movimientoModel->getTopProducts(10);
        $productosPorCategoria = $productoModel->getCountByCategory();
        $flash = $this->getFlash();

        $this->view('reportes/index', [
            'titulo' => 'Reportes',
            'productosStockBajo' => $productosStockBajo,
            'topProductos' => $topProductos,
            'productosPorCategoria' => $productosPorCategoria,
            'flash' => $flash,
            'loadChartJS' => true,
        ]);
    }

    // =================================================================
    // EXPORTACIÓN CSV
    // =================================================================

    /**
     * Exportar inventario general a CSV.
     */
    public function exportInventarioCSV(): void
    {
        $productoModel = new Producto();
        $export = new ExportService();

        $productos = $this->getAllProductsForExport($productoModel);

        $this->securityService->logAction(
            currentUserId(),
            'exportar_inventario_csv',
            'reportes',
            'Exportación CSV de inventario general (' . count($productos) . ' productos)'
        );

        $export->exportCSV(
            'inventario_general',
            ['SKU', 'Producto', 'Categoría', 'Precio', 'Stock', 'Stock Mínimo', 'Estado', 'Perecedero', 'Vencimiento'],
            $productos,
            ['sku', 'nombre', 'categoria_nombre', 'precio_fmt', 'stock', 'stock_minimo', 'estado', 'perecedero', 'vencimiento']
        );
    }

    /**
     * Exportar stock bajo a CSV.
     */
    public function exportStockBajoCSV(): void
    {
        $productoModel = new Producto();
        $export = new ExportService();

        $productos = $productoModel->getLowStock();

        $rows = array_map(function ($p) {
            return [
                'sku' => $p->sku,
                'nombre' => $p->nombre,
                'categoria_nombre' => $p->categoria_nombre ?? 'Sin categoría',
                'stock' => $p->stock,
                'stock_minimo' => $p->stock_minimo,
                'estado' => $p->stock <= 0 ? 'Agotado' : 'Bajo',
                'valor' => number_format($p->precio * $p->stock, 2),
            ];
        }, $productos);

        $this->securityService->logAction(
            currentUserId(),
            'exportar_stock_bajo_csv',
            'reportes',
            'Exportación CSV de stock bajo/agotado (' . count($productos) . ' productos)'
        );

        $export->exportCSV(
            'stock_bajo',
            ['SKU', 'Producto', 'Categoría', 'Stock', 'Mínimo', 'Estado', 'Valor Inventario'],
            $rows,
            ['sku', 'nombre', 'categoria_nombre', 'stock', 'stock_minimo', 'estado', 'valor']
        );
    }

    /**
     * Exportar top productos a CSV.
     */
    public function exportTopProductosCSV(): void
    {
        $movimientoModel = new Movimiento();
        $export = new ExportService();

        $topProductos = $movimientoModel->getTopProducts(50);

        $rows = [];
        foreach ($topProductos as $i => $tp) {
            $rows[] = [
                'ranking' => $i + 1,
                'nombre' => $tp->nombre,
                'sku' => $tp->sku,
                'total_entradas' => $tp->total_entradas,
                'total_salidas' => $tp->total_salidas,
                'total_movimientos' => $tp->total_movimientos,
            ];
        }

        $this->securityService->logAction(
            currentUserId(),
            'exportar_top_productos_csv',
            'reportes',
            'Exportación CSV de top productos movidos (' . count($rows) . ' productos)'
        );

        $export->exportCSV(
            'top_productos_movidos',
            ['#', 'Producto', 'SKU', 'Entradas', 'Salidas', 'Total Movimientos'],
            $rows,
            ['ranking', 'nombre', 'sku', 'total_entradas', 'total_salidas', 'total_movimientos']
        );
    }

    /**
     * Exportar distribución por categoría a CSV.
     */
    public function exportCategoriasCSV(): void
    {
        $productoModel = new Producto();
        $export = new ExportService();

        $categorias = $productoModel->getCountByCategory();

        $this->securityService->logAction(
            currentUserId(),
            'exportar_categorias_csv',
            'reportes',
            'Exportación CSV de distribución por categoría (' . count($categorias) . ' categorías)'
        );

        $export->exportCSV(
            'distribucion_categorias',
            ['Categoría', 'Cantidad de Productos'],
            $categorias,
            ['categoria', 'total']
        );
    }

    /**
     * Exportar movimientos a CSV (con filtro de fechas opcional).
     * Params GET: ?fecha (exacta), ?fecha_desde, ?fecha_hasta
     */
    public function exportMovimientosCSV(): void
    {
        $movimientoModel = new Movimiento();
        $export = new ExportService();

        // Parsear filtros de fecha
        $fechas = $this->parseDateFilters();
        $movimientos = $this->getMovimientosForExport($movimientoModel, $fechas['desde'], $fechas['hasta']);

        // Si no hay movimientos, redirigir con aviso
        if (empty($movimientos)) {
            $this->setFlash('warning', $this->buildEmptyMessage($fechas));
            $this->redirect('reportes');
            return;
        }

        $fechaLabel = $this->buildDateLabel($fechas);

        $this->securityService->logAction(
            currentUserId(),
            'exportar_movimientos_csv',
            'reportes',
            "Exportación CSV de movimientos{$fechaLabel} (" . count($movimientos) . ' registros)'
        );

        $export->exportCSV(
            'movimientos' . ($fechas['desde'] ? '_' . $fechas['desde'] : '') . ($fechas['hasta'] && $fechas['hasta'] !== $fechas['desde'] ? '_a_' . $fechas['hasta'] : ''),
            ['ID', 'Fecha', 'Tipo', 'Producto', 'SKU', 'Cantidad', 'Motivo', 'Usuario'],
            $movimientos,
            ['id', 'fecha', 'tipo', 'producto_nombre', 'producto_sku', 'cantidad', 'motivo', 'usuario_nombre']
        );
    }

    // =================================================================
    // EXPORTACIÓN PDF (HTML imprimible)
    // =================================================================

    /**
     * Exportar inventario general a PDF.
     */
    public function exportInventarioPDF(): void
    {
        $productoModel = new Producto();
        $export = new ExportService();

        $productos = $this->getAllProductsForExport($productoModel);

        // Calcular resumen
        $totalProductos = count($productos);
        $valorTotal = 0;
        $stockBajo = 0;
        $agotados = 0;
        foreach ($productos as $p) {
            $valorTotal += ($p['precio_raw'] ?? 0) * ($p['stock'] ?? 0);
            if (($p['stock'] ?? 0) <= 0)
                $agotados++;
            elseif (($p['stock'] ?? 0) <= ($p['stock_minimo'] ?? 0))
                $stockBajo++;
        }

        $currentUser = currentUser();

        $this->securityService->logAction(
            currentUserId(),
            'exportar_inventario_pdf',
            'reportes',
            'Exportación PDF de inventario general (' . $totalProductos . ' productos, valor: ' . formatMoney($valorTotal) . ')'
        );

        $export->exportPDF(
            'Reporte de Inventario General',
            'inventario_general',
            [
                [
                    'title' => 'Inventario Completo de Productos',
                    'headers' => ['SKU', 'Producto', 'Categoría', 'Precio', 'Stock', 'Mín.', 'Estado'],
                    'rows' => $productos,
                    'keys' => ['sku', 'nombre', 'categoria_nombre', 'precio_fmt', 'stock', 'stock_minimo', 'estado'],
                    'formatters' => [
                        'sku' => fn($v) => '<span class="text-mono">' . htmlspecialchars($v) . '</span>',
                        'nombre' => fn($v) => '<span class="text-bold">' . htmlspecialchars($v) . '</span>',
                        'estado' => function ($v) {
                            if ($v === 'Agotado')
                                return '<span class="badge-danger">' . $v . '</span>';
                            if ($v === 'Bajo')
                                return '<span class="badge-warn">' . $v . '</span>';
                            return '<span class="badge-ok">' . $v . '</span>';
                        },
                    ],
                ],
            ],
            [
                'usuario' => $currentUser['nombre'] ?? 'Sistema',
                'summary' => [
                    ['label' => 'Total Productos', 'value' => $totalProductos],
                    ['label' => 'Valor Inventario', 'value' => formatMoney($valorTotal)],
                    ['label' => 'Stock Bajo', 'value' => $stockBajo],
                    ['label' => 'Agotados', 'value' => $agotados],
                ],
            ]
        );
    }

    /**
     * Exportar stock bajo a PDF.
     */
    public function exportStockBajoPDF(): void
    {
        $productoModel = new Producto();
        $export = new ExportService();

        $productos = $productoModel->getLowStock();

        $rows = array_map(function ($p) {
            return [
                'sku' => $p->sku,
                'nombre' => $p->nombre,
                'categoria_nombre' => $p->categoria_nombre ?? 'Sin categoría',
                'stock' => $p->stock,
                'stock_minimo' => $p->stock_minimo,
                'estado' => $p->stock <= 0 ? 'Agotado' : 'Bajo',
                'valor' => formatMoney($p->precio * $p->stock),
            ];
        }, $productos);

        $currentUser = currentUser();

        $this->securityService->logAction(
            currentUserId(),
            'exportar_stock_bajo_pdf',
            'reportes',
            'Exportación PDF de stock bajo/agotado (' . count($productos) . ' productos)'
        );

        $export->exportPDF(
            'Reporte de Stock Bajo y Agotado',
            'stock_bajo',
            [
                [
                    'title' => 'Productos con Stock Bajo o Agotado (' . count($productos) . ' productos)',
                    'headers' => ['SKU', 'Producto', 'Categoría', 'Stock', 'Mínimo', 'Estado', 'Valor'],
                    'rows' => $rows,
                    'keys' => ['sku', 'nombre', 'categoria_nombre', 'stock', 'stock_minimo', 'estado', 'valor'],
                    'formatters' => [
                        'sku' => fn($v) => '<span class="text-mono">' . htmlspecialchars($v) . '</span>',
                        'nombre' => fn($v) => '<span class="text-bold">' . htmlspecialchars($v) . '</span>',
                        'stock' => fn($v) => '<span class="text-danger">' . $v . '</span>',
                        'estado' => fn($v) => $v === 'Agotado'
                            ? '<span class="badge-danger">' . $v . '</span>'
                            : '<span class="badge-warn">' . $v . '</span>',
                    ],
                ],
            ],
            [
                'usuario' => $currentUser['nombre'] ?? 'Sistema',
                'summary' => [
                    ['label' => 'Productos Afectados', 'value' => count($productos)],
                    ['label' => 'Agotados', 'value' => count(array_filter($productos, fn($p) => $p->stock <= 0))],
                    ['label' => 'Stock Bajo', 'value' => count(array_filter($productos, fn($p) => $p->stock > 0))],
                ],
            ]
        );
    }

    /**
     * Exportar reporte completo (todas las secciones) a PDF.
     */
    public function exportCompletoPDF(): void
    {
        $productoModel = new Producto();
        $movimientoModel = new Movimiento();
        $export = new ExportService();

        // --- Sección 1: Inventario completo ---
        $productos = $this->getAllProductsForExport($productoModel);
        $totalProductos = count($productos);
        $valorTotal = 0;
        $stockBajo = 0;
        $agotados = 0;
        foreach ($productos as $p) {
            $valorTotal += ($p['precio_raw'] ?? 0) * ($p['stock'] ?? 0);
            if (($p['stock'] ?? 0) <= 0)
                $agotados++;
            elseif (($p['stock'] ?? 0) <= ($p['stock_minimo'] ?? 0))
                $stockBajo++;
        }

        // --- Sección 2: Stock bajo ---
        $productosStockBajo = $productoModel->getLowStock();
        $stockBajoRows = array_map(function ($p) {
            return [
                'sku' => $p->sku,
                'nombre' => $p->nombre,
                'categoria_nombre' => $p->categoria_nombre ?? '-',
                'stock' => $p->stock,
                'stock_minimo' => $p->stock_minimo,
                'estado' => $p->stock <= 0 ? 'Agotado' : 'Bajo',
                'valor' => formatMoney($p->precio * $p->stock),
            ];
        }, $productosStockBajo);

        // --- Sección 3: Top productos ---
        $topProductos = $movimientoModel->getTopProducts(10);
        $topRows = [];
        foreach ($topProductos as $i => $tp) {
            $topRows[] = [
                'ranking' => $i + 1,
                'nombre' => $tp->nombre,
                'sku' => $tp->sku,
                'total_entradas' => $tp->total_entradas,
                'total_salidas' => $tp->total_salidas,
                'total_movimientos' => $tp->total_movimientos,
            ];
        }

        // --- Sección 4: Distribución por categoría ---
        $categorias = $productoModel->getCountByCategory();
        $catRows = array_map(fn($c) => ['categoria' => $c->categoria, 'total' => $c->total], $categorias);

        $currentUser = currentUser();

        $this->securityService->logAction(
            currentUserId(),
            'exportar_reporte_completo_pdf',
            'reportes',
            'Exportación PDF de reporte completo (' . $totalProductos . ' productos, valor: ' . formatMoney($valorTotal) . ')'
        );

        $export->exportPDF(
            'Reporte Completo de Inventario',
            'reporte_completo',
            [
                [
                    'title' => 'Inventario General (' . $totalProductos . ' productos)',
                    'headers' => ['SKU', 'Producto', 'Categoría', 'Precio', 'Stock', 'Mín.', 'Estado'],
                    'rows' => $productos,
                    'keys' => ['sku', 'nombre', 'categoria_nombre', 'precio_fmt', 'stock', 'stock_minimo', 'estado'],
                    'formatters' => [
                        'sku' => fn($v) => '<span class="text-mono">' . htmlspecialchars($v) . '</span>',
                        'nombre' => fn($v) => '<span class="text-bold">' . htmlspecialchars($v) . '</span>',
                        'estado' => function ($v) {
                            if ($v === 'Agotado')
                                return '<span class="badge-danger">' . $v . '</span>';
                            if ($v === 'Bajo')
                                return '<span class="badge-warn">' . $v . '</span>';
                            return '<span class="badge-ok">' . $v . '</span>';
                        },
                    ],
                ],
                [
                    'title' => 'Productos con Stock Bajo o Agotado (' . count($productosStockBajo) . ')',
                    'headers' => ['SKU', 'Producto', 'Categoría', 'Stock', 'Mínimo', 'Estado', 'Valor'],
                    'rows' => $stockBajoRows,
                    'keys' => ['sku', 'nombre', 'categoria_nombre', 'stock', 'stock_minimo', 'estado', 'valor'],
                    'formatters' => [
                        'sku' => fn($v) => '<span class="text-mono">' . htmlspecialchars($v) . '</span>',
                        'stock' => fn($v) => '<span class="text-danger">' . $v . '</span>',
                        'estado' => fn($v) => $v === 'Agotado'
                            ? '<span class="badge-danger">' . $v . '</span>' : '<span class="badge-warn">' . $v . '</span>',
                    ],
                ],
                [
                    'title' => 'Top 10 Productos Más Movidos',
                    'headers' => ['#', 'Producto', 'SKU', 'Entradas', 'Salidas', 'Total'],
                    'rows' => $topRows,
                    'keys' => ['ranking', 'nombre', 'sku', 'total_entradas', 'total_salidas', 'total_movimientos'],
                    'formatters' => [
                        'total_entradas' => fn($v) => '<span class="text-success">+' . $v . '</span>',
                        'total_salidas' => fn($v) => '<span class="text-danger">-' . $v . '</span>',
                        'total_movimientos' => fn($v) => '<span class="text-bold">' . $v . '</span>',
                    ],
                ],
                [
                    'title' => 'Distribución por Categoría',
                    'headers' => ['Categoría', 'Cantidad de Productos'],
                    'rows' => $catRows,
                    'keys' => ['categoria', 'total'],
                    'formatters' => [
                        'categoria' => fn($v) => '<span class="text-bold">' . htmlspecialchars($v) . '</span>',
                    ],
                ],
            ],
            [
                'usuario' => $currentUser['nombre'] ?? 'Sistema',
                'summary' => [
                    ['label' => 'Total Productos', 'value' => $totalProductos],
                    ['label' => 'Valor Inventario', 'value' => formatMoney($valorTotal)],
                    ['label' => 'Stock Bajo', 'value' => $stockBajo],
                    ['label' => 'Agotados', 'value' => $agotados],
                ],
            ]
        );
    }

    /**
     * Exportar movimientos a PDF (con filtro de fechas opcional).
     * Params GET: ?fecha (exacta), ?fecha_desde, ?fecha_hasta
     */
    public function exportMovimientosPDF(): void
    {
        $movimientoModel = new Movimiento();
        $export = new ExportService();

        $fechas = $this->parseDateFilters();
        $movimientos = $this->getMovimientosForExport($movimientoModel, $fechas['desde'], $fechas['hasta']);

        // Si no hay movimientos, redirigir con aviso
        if (empty($movimientos)) {
            $this->setFlash('warning', $this->buildEmptyMessage($fechas));
            $this->redirect('reportes');
            return;
        }

        $fechaLabel = $this->buildDateLabel($fechas);
        $currentUser = currentUser();

        // Calcular resumen
        $totalEntradas = 0;
        $totalSalidas = 0;
        $totalAjustes = 0;
        foreach ($movimientos as $m) {
            match (strtolower($m['tipo'])) {
                'entrada' => $totalEntradas += (int) $m['cantidad'],
                'salida' => $totalSalidas += (int) $m['cantidad'],
                'ajuste' => $totalAjustes += (int) $m['cantidad'],
                default => null,
            };
        }

        $this->securityService->logAction(
            currentUserId(),
            'exportar_movimientos_pdf',
            'reportes',
            "Exportación PDF de movimientos{$fechaLabel} (" . count($movimientos) . ' registros)'
        );

        $export->exportPDF(
            'Reporte de Movimientos' . $fechaLabel,
            'movimientos' . ($fechas['desde'] ? '_' . $fechas['desde'] : ''),
            [
                [
                    'title' => 'Historial de Movimientos (' . count($movimientos) . ' registros)' . $fechaLabel,
                    'headers' => ['ID', 'Fecha', 'Tipo', 'Producto', 'SKU', 'Cantidad', 'Motivo', 'Usuario'],
                    'rows' => $movimientos,
                    'keys' => ['id', 'fecha', 'tipo', 'producto_nombre', 'producto_sku', 'cantidad', 'motivo', 'usuario_nombre'],
                    'formatters' => [
                        'tipo' => function ($v) {
                            return match (strtolower($v)) {
                                'entrada' => '<span class="badge-ok">' . $v . '</span>',
                                'salida' => '<span class="badge-danger">' . $v . '</span>',
                                'ajuste' => '<span class="badge-warn">' . $v . '</span>',
                                default => htmlspecialchars($v),
                            };
                        },
                        'producto_nombre' => fn($v) => '<span class="text-bold">' . htmlspecialchars($v) . '</span>',
                        'producto_sku' => fn($v) => '<span class="text-mono">' . htmlspecialchars($v) . '</span>',
                    ],
                ],
            ],
            [
                'usuario' => $currentUser['nombre'] ?? 'Sistema',
                'summary' => [
                    ['label' => 'Total Movimientos', 'value' => count($movimientos)],
                    ['label' => 'Entradas', 'value' => '+' . $totalEntradas],
                    ['label' => 'Salidas', 'value' => '-' . $totalSalidas],
                    ['label' => 'Ajustes', 'value' => $totalAjustes],
                ],
            ]
        );
    }

    // =================================================================
    // HELPERS PRIVADOS
    // =================================================================

    /**
     * Parsear filtros de fecha desde los query params.
     * Soporta: ?fecha=2026-04-13 (exacta) o ?fecha_desde=...&fecha_hasta=...
     *
     * @return array ['desde' => string, 'hasta' => string, 'exacta' => bool]
     */
    private function parseDateFilters(): array
    {
        $fecha = $this->query('fecha', '');
        $fechaDesde = $this->query('fecha_desde', '');
        $fechaHasta = $this->query('fecha_hasta', '');

        // Si se proporcionó fecha exacta, usarla como desde y hasta
        if (!empty($fecha)) {
            // Validar formato
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha) || !strtotime($fecha)) {
                return ['desde' => '', 'hasta' => '', 'exacta' => false];
            }
            return ['desde' => $fecha, 'hasta' => $fecha, 'exacta' => true];
        }

        // Validar formatos de rango
        if (!empty($fechaDesde) && (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaDesde) || !strtotime($fechaDesde))) {
            $fechaDesde = '';
        }
        if (!empty($fechaHasta) && (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaHasta) || !strtotime($fechaHasta))) {
            $fechaHasta = '';
        }

        return ['desde' => $fechaDesde, 'hasta' => $fechaHasta, 'exacta' => false];
    }

    /**
     * Obtener movimientos formateados para exportación con filtros de fecha.
     *
     * @param Movimiento $model
     * @param string $fechaDesde
     * @param string $fechaHasta
     * @return array
     */
    private function getMovimientosForExport(Movimiento $model, string $fechaDesde = '', string $fechaHasta = ''): array
    {
        $movimientos = $model->getAllForExport(5000, $fechaDesde, $fechaHasta);

        return array_map(function ($m) {
            return [
                'id' => $m->id,
                'fecha' => formatDate($m->created_at, 'd/m/Y H:i'),
                'tipo' => ucfirst($m->tipo),
                'producto_nombre' => $m->producto_nombre,
                'producto_sku' => $m->producto_sku,
                'cantidad' => $m->cantidad,
                'motivo' => $m->observaciones ?? '',
                'usuario_nombre' => $m->usuario_nombre,
            ];
        }, $movimientos);
    }

    /**
     * Construir mensaje de "sin resultados" según los filtros de fecha.
     *
     * @param array $fechas
     * @return string
     */
    private function buildEmptyMessage(array $fechas): string
    {
        if ($fechas['exacta']) {
            $fechaFmt = date('d/m/Y', strtotime($fechas['desde']));
            return "No se encontraron movimientos para el día <strong>{$fechaFmt}</strong>. No hubo actividad registrada en esa fecha.";
        }

        if (!empty($fechas['desde']) && !empty($fechas['hasta'])) {
            $desdeFmt = date('d/m/Y', strtotime($fechas['desde']));
            $hastaFmt = date('d/m/Y', strtotime($fechas['hasta']));
            return "No se encontraron movimientos entre <strong>{$desdeFmt}</strong> y <strong>{$hastaFmt}</strong>. No hubo actividad registrada en ese período.";
        }

        if (!empty($fechas['desde'])) {
            $desdeFmt = date('d/m/Y', strtotime($fechas['desde']));
            return "No se encontraron movimientos desde <strong>{$desdeFmt}</strong>.";
        }

        if (!empty($fechas['hasta'])) {
            $hastaFmt = date('d/m/Y', strtotime($fechas['hasta']));
            return "No se encontraron movimientos hasta <strong>{$hastaFmt}</strong>.";
        }

        return 'No se encontraron movimientos para exportar.';
    }

    /**
     * Construir etiqueta de fecha para logs y nombres de archivo.
     *
     * @param array $fechas
     * @return string
     */
    private function buildDateLabel(array $fechas): string
    {
        if ($fechas['exacta']) {
            return ' — ' . date('d/m/Y', strtotime($fechas['desde']));
        }
        if (!empty($fechas['desde']) && !empty($fechas['hasta'])) {
            return ' — ' . date('d/m/Y', strtotime($fechas['desde'])) . ' a ' . date('d/m/Y', strtotime($fechas['hasta']));
        }
        if (!empty($fechas['desde'])) {
            return ' — desde ' . date('d/m/Y', strtotime($fechas['desde']));
        }
        if (!empty($fechas['hasta'])) {
            return ' — hasta ' . date('d/m/Y', strtotime($fechas['hasta']));
        }
        return '';
    }

    /**
     * Obtener todos los productos formateados para exportación.
     */
    private function getAllProductsForExport(Producto $model): array
    {
        $productos = $model->getAllActiveWithCategory();

        return array_map(function ($p) {
            $estado = 'Normal';
            if ($p->stock <= 0)
                $estado = 'Agotado';
            elseif ($p->stock <= $p->stock_minimo)
                $estado = 'Bajo';

            return [
                'sku' => $p->sku,
                'nombre' => $p->nombre,
                'categoria_nombre' => $p->categoria_nombre ?? 'Sin categoría',
                'precio_fmt' => formatMoney($p->precio),
                'precio_raw' => $p->precio,
                'stock' => $p->stock,
                'stock_minimo' => $p->stock_minimo,
                'estado' => $estado,
                'perecedero' => !empty($p->es_perecedero) ? 'Sí' : 'No',
                'vencimiento' => !empty($p->fecha_vencimiento) ? formatDate($p->fecha_vencimiento, 'd/m/Y') : '—',
            ];
        }, $productos);
    }
}

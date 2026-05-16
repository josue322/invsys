<?php
/**
 * InvSys - ProductoController

 */

class ProductoController extends Controller
{
    private Producto $productoModel;
    private Categoria $categoriaModel;
    private Ubicacion $ubicacionModel;
    private Proveedor $proveedorModel;
    private ProductoProveedor $ppModel;
    private SecurityService $securityService;

    /** @var string Directorio de imágenes de productos */
    private string $imageDir;

    /** @var array Tipos MIME permitidos para imágenes */
    private array $allowedMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/gif',
    ];

    /** @var int Tamaño máximo de imagen en bytes (2MB) */
    private int $maxImageSize = 2097152;

    public function __construct()
    {
        $this->productoModel = new Producto();
        $this->categoriaModel = new Categoria();
        $this->ubicacionModel = new Ubicacion();
        $this->proveedorModel = new Proveedor();
        $this->ppModel = new ProductoProveedor();
        $this->securityService = SecurityService::getInstance();
        $this->imageDir = PUBLIC_PATH . '/assets/img/productos';
    }

    /**
     * Listado de productos con filtros y paginación.
     */
    public function index(): void
    {
        $page = (int) $this->query('page', 1);
        $search = $this->query('search', '');
        $categoriaId = (int) $this->query('categoria', 0);
        $stockFilter = $this->query('stock', '');

        $productos = $this->productoModel->getAllWithCategory($page, $this->getPerPage(), $search, $categoriaId, $stockFilter);
        $categorias = $this->categoriaModel->getAllActive();

        $csrfToken = $this->generateCSRF();
        $flash = $this->getFlash();

        $this->view('productos/index', [
            'titulo' => 'Productos',
            'productos' => $productos,
            'categorias' => $categorias,
            'search' => $search,
            'categoriaId' => $categoriaId,
            'stockFilter' => $stockFilter,
            'csrfToken' => $csrfToken,
            'flash' => $flash,
        ]);
    }

    /**
     * Formulario de creación.
     * Acepta query params opcionales para pre-llenar desde el escáner:
     * ?sku=XXX&nombre=XXX&descripcion=XXX&from_scanner=1
     */
    public function create(): void
    {
        $categorias = $this->categoriaModel->getAllActive();
        $ubicaciones = $this->ubicacionModel->findAllActive();
        $csrfToken = $this->generateCSRF();

        // Datos pre-llenados (desde el escáner o enlace directo)
        $prefill = [
            'sku'         => $this->query('sku', ''),
            'nombre'      => $this->query('nombre', ''),
            'descripcion' => $this->query('descripcion', ''),
            'imagen_url'  => $this->query('imagen_url', ''),
        ];
        $fromScanner = (bool) $this->query('from_scanner', '');

        $this->view('productos/create', [
            'titulo'      => 'Nuevo Producto',
            'categorias'  => $categorias,
            'ubicaciones' => $ubicaciones,
            'csrfToken'   => $csrfToken,
            'prefill'     => $prefill,
            'fromScanner' => $fromScanner,
        ]);
    }

    /**
     * Guardar nuevo producto.
     */
    public function store(): void
    {
        if (!$this->validateCSRF()) {
            $this->redirect('productos/crear');
            return;
        }

        $esPerecedero = $this->input('es_perecedero') ? 1 : 0;

        $data = [
            'nombre' => $this->input('nombre'),
            'descripcion' => $this->input('descripcion'),
            'sku' => strtoupper($this->input('sku')),
            'codigo_barras' => $this->input('codigo_barras') ?: null,
            'categoria_id' => (int) $this->input('categoria_id') ?: null,
            'ubicacion_id' => (int) $this->input('ubicacion_id') ?: null,
            'unidad_medida' => $this->input('unidad_medida', 'Unidad'),
            'costo' => (float) $this->input('costo', 0),
            'stock' => (int) $this->input('stock', 0),
            'stock_minimo' => (int) $this->input('stock_minimo', 5),
            'es_perecedero' => $esPerecedero,
            'activo' => 1,
        ];

        // Validaciones
        $errors = $this->validateProducto($data);
        if (!empty($errors)) {
            $this->setFlash('error', implode('<br>', $errors));
            $this->redirect('productos/crear');
            return;
        }

        // Verificar SKU único
        if ($this->productoModel->skuExists($data['sku'])) {
            $this->setFlash('error', 'El SKU ya existe. Use uno diferente.');
            $this->redirect('productos/crear');
            return;
        }

        // Verificar código de barras único
        if (!empty($data['codigo_barras']) && $this->productoModel->barcodeExists($data['codigo_barras'])) {
            $this->setFlash('error', 'El código de barras ya existe. Use uno diferente.');
            $this->redirect('productos/crear');
            return;
        }

        // Procesar imagen
        $imageName = $this->handleImageUpload();
        if ($imageName === false) {

            $this->redirect('productos/crear');
            return;
        }
        if ($imageName !== null) {
            $data['imagen'] = $imageName;
        }

        // Si no se subió imagen manualmente, intentar descargar la imagen externa (desde escáner)
        if ($imageName === null && !empty($this->input('imagen_url_externa'))) {
            $externalImage = $this->downloadExternalImage($this->input('imagen_url_externa'));
            if ($externalImage) {
                $data['imagen'] = $externalImage;
            }
        }

        $id = $this->productoModel->create($data);

        // Si hay stock inicial, registrar el movimiento de Ajuste
        if ($data['stock'] > 0) {
            require_once APP_PATH . '/models/Movimiento.php';
            $movimientoModel = new Movimiento();
            
            $loteId = null;
            
            // Si es perecedero, crear el lote inicial
            if ($esPerecedero) {
                $loteInicial = $this->input('lote_inicial');
                $fechaVencimiento = $this->input('fecha_vencimiento_inicial');
                
                if (!empty($loteInicial) && !empty($fechaVencimiento)) {
                    require_once APP_PATH . '/models/Lote.php';
                    $loteModel = new Lote();
                    $loteId = $loteModel->create([
                        'producto_id' => $id,
                        'numero_lote' => strtoupper($loteInicial),
                        'fecha_vencimiento' => $fechaVencimiento,
                        'cantidad_inicial' => $data['stock'],
                        'stock_actual' => $data['stock'],
                        'estado' => 'disponible',
                    ]);
                }
            }

            // Registrar el movimiento de entrada por stock inicial
            $movimientoModel->create([
                'producto_id' => $id,
                'usuario_id' => currentUserId(),
                'lote_id' => $loteId,
                'tipo' => 'ajuste',
                'cantidad' => $data['stock'],
                'stock_anterior' => 0,
                'stock_nuevo' => $data['stock'],
                'referencia' => 'Stock Inicial',
                'observaciones' => 'Ajuste automático por stock inicial al crear producto.'
            ]);
        }

        // Log de auditoría
        $this->securityService->logAction(
            currentUserId(),
            'crear_producto',
            'productos',
            "Producto creado: {$data['nombre']} (SKU: {$data['sku']}, ID: {$id})"
        );

        // Verificar alertas de stock
        $alertService = new AlertService();
        $alertService->checkStock($id);

        $this->setFlash('success', 'Producto creado exitosamente.');
        $this->redirect('productos');
    }

    /**
     * Formulario de edición.
     */
    public function edit(string $id): void
    {
        $producto = $this->productoModel->findById((int) $id);
        if (!$producto) {
            $this->setFlash('error', 'Producto no encontrado.');
            $this->redirect('productos');
            return;
        }

        $categorias = $this->categoriaModel->getAllActive();
        $ubicaciones = $this->ubicacionModel->findAllActive();
        $csrfToken = $this->generateCSRF();

        $this->view('productos/edit', [
            'titulo' => 'Editar Producto',
            'producto' => $producto,
            'categorias' => $categorias,
            'ubicaciones' => $ubicaciones,
            'csrfToken' => $csrfToken,
        ]);
    }

    /**
     * Actualizar producto existente.
     */
    public function update(string $id): void
    {
        $id = (int) $id;

        if (!$this->validateCSRF()) {
            $this->redirect("productos/editar/{$id}");
            return;
        }

        $producto = $this->productoModel->findById($id);
        if (!$producto) {
            $this->setFlash('error', 'Producto no encontrado.');
            $this->redirect('productos');
            return;
        }

        $esPerecedero = $this->input('es_perecedero') ? 1 : 0;

        $data = [
            'nombre' => $this->input('nombre'),
            'descripcion' => $this->input('descripcion'),
            'sku' => strtoupper($this->input('sku')),
            'codigo_barras' => $this->input('codigo_barras') ?: null,
            'categoria_id' => (int) $this->input('categoria_id') ?: null,
            'ubicacion_id' => (int) $this->input('ubicacion_id') ?: null,
            'unidad_medida' => $this->input('unidad_medida', 'Unidad'),
            'costo' => (float) $this->input('costo', 0),
            'stock_minimo' => (int) $this->input('stock_minimo', 5),
            'activo' => $this->input('activo', 1) ? 1 : 0,
            'es_perecedero' => $esPerecedero,
        ];

        // Validaciones
        $errors = $this->validateProducto($data, false);
        if (!empty($errors)) {
            $this->setFlash('error', implode('<br>', $errors));
            $this->redirect("productos/editar/{$id}");
            return;
        }

        // Verificar SKU único (excluyendo el actual)
        if ($this->productoModel->skuExists($data['sku'], $id)) {
            $this->setFlash('error', 'El SKU ya existe. Use uno diferente.');
            $this->redirect("productos/editar/{$id}");
            return;
        }

        // Verificar código de barras único (excluyendo el actual)
        if (!empty($data['codigo_barras']) && $this->productoModel->barcodeExists($data['codigo_barras'], $id)) {
            $this->setFlash('error', 'El código de barras ya existe. Use uno diferente.');
            $this->redirect("productos/editar/{$id}");
            return;
        }

        // ¿Se quiere eliminar la imagen actual?
        if ($this->input('eliminar_imagen') === '1') {
            $this->deleteProductImage($producto->imagen ?? null);
            $data['imagen'] = null;
        } else {
            // Procesar nueva imagen (si se subió)
            $imageName = $this->handleImageUpload();
            if ($imageName === false) {
                $this->redirect("productos/editar/{$id}");
                return;
            }
            if ($imageName !== null) {
                // Eliminar imagen anterior
                $this->deleteProductImage($producto->imagen ?? null);
                $data['imagen'] = $imageName;
            }
        }

        // Registrar cambio de costo si hubo modificación real
        // No registrar cuando el costo anterior era 0 (precio inicial, no un cambio real)
        $costoAnterior = (float) $producto->costo;
        if ($costoAnterior !== $data['costo'] && $costoAnterior > 0) {
            $costoHistorial = new CostoHistorial();
            $costoHistorial->registrar(
                $id,
                $costoAnterior,
                $data['costo'],
                currentUserId()
            );
        }

        $this->productoModel->update($id, $data);

        $this->securityService->logAction(
            currentUserId(),
            'editar_producto',
            'productos',
            "Producto editado: {$data['nombre']} (SKU: {$data['sku']}, ID: {$id})"
        );

        // Verificar alertas de stock
        $alertService = new AlertService();
        $alertService->checkStock($id);

        $this->setFlash('success', 'Producto actualizado exitosamente.');
        $this->redirect('productos');
    }

    /**
     * Eliminar producto (soft delete - desactivar).
     */
    public function destroy(string $id): void
    {
        $id = (int) $id;

        if (!$this->validateCSRF()) {
            $this->redirect('productos');
            return;
        }

        $producto = $this->productoModel->findById($id);
        if (!$producto) {
            $this->setFlash('error', 'Producto no encontrado.');
            $this->redirect('productos');
            return;
        }

        $this->productoModel->softDelete($id);

        $this->securityService->logAction(
            currentUserId(),
            'eliminar_producto',
            'productos',
            "Producto desactivado: {$producto->nombre} (SKU: {$producto->sku}, ID: {$id})"
        );

        $this->setFlash('success', 'Producto eliminado exitosamente.');
        $this->redirect('productos');
    }

    /**
     * Alternar estado activo/inactivo del producto.
     */
    public function toggle(string $id): void
    {
        $id = (int) $id;

        if (!$this->validateCSRF()) {
            $this->redirect('productos');
            return;
        }

        $producto = $this->productoModel->findById($id);
        if (!$producto) {
            $this->setFlash('error', 'Producto no encontrado.');
            $this->redirect('productos');
            return;
        }

        $nuevoEstado = $producto->activo ? 0 : 1;
        $this->productoModel->update($id, ['activo' => $nuevoEstado]);

        $estadoStr = $nuevoEstado ? 'activado' : 'desactivado';
        $this->securityService->logAction(
            currentUserId(),
            'toggle_producto',
            'productos',
            "Producto {$estadoStr}: {$producto->nombre} (ID: {$id})"
        );

        $this->setFlash('success', "Producto {$estadoStr} exitosamente.");
        $this->redirect('productos');
    }

    // =========================================================
    // MÉTODOS PRIVADOS
    // =========================================================

    /**
     * Manejar la subida de imagen del producto.
     *
     * @return string|null|false Nombre del archivo, null si no se subió, false si hubo error
     */
    private function handleImageUpload(): string|null|false
    {
        if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] === UPLOAD_ERR_NO_FILE) {
            return null; // No se subió ningún archivo
        }

        $file = $_FILES['imagen'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->setFlash('error', 'Error al subir la imagen. Código: ' . $file['error']);
            return false;
        }

        // Validar tipo MIME real
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $this->allowedMimeTypes)) {
            $this->setFlash('error', 'Formato de imagen no válido. Use JPG, PNG, WebP o GIF.');
            return false;
        }

        // Validar tamaño
        if ($file['size'] > $this->maxImageSize) {
            $this->setFlash('error', 'La imagen es demasiado grande. Máximo: 2MB.');
            return false;
        }

        // Generar nombre único
        $extension = match ($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            default => 'jpg',
        };
        $filename = 'prod_' . uniqid() . '_' . time() . '.' . $extension;
        $destination = $this->imageDir . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            $this->setFlash('error', 'Error al guardar la imagen. Verifique los permisos del directorio.');
            return false;
        }

        return $filename;
    }

    /**
     * Eliminar imagen de producto del sistema de archivos.
     *
     * @param string|null $filename Nombre del archivo a eliminar
     */
    private function deleteProductImage(?string $filename): void
    {
        if ($filename && file_exists($this->imageDir . '/' . $filename)) {
            @unlink($this->imageDir . '/' . $filename);
        }
    }

    /**
     * Descargar imagen desde URL externa (escáner/lookup) y guardarla localmente.
     *
     * @param string $url URL de la imagen externa
     * @return string|null Nombre del archivo guardado o null si falla
     */
    private function downloadExternalImage(string $url): ?string
    {
        if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        try {
            $ctx = stream_context_create([
                'http' => [
                    'timeout' => 5,
                    'header' => "User-Agent: InvSys/1.0\r\n",
                ],
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
            ]);

            $imageData = @file_get_contents($url, false, $ctx);
            if ($imageData === false || strlen($imageData) < 100) {
                return null;
            }

            // Detectar tipo MIME
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->buffer($imageData);

            if (!in_array($mimeType, $this->allowedMimeTypes)) {
                return null;
            }

            // Limitar tamaño (2MB)
            if (strlen($imageData) > $this->maxImageSize) {
                return null;
            }

            $extension = match ($mimeType) {
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp',
                'image/gif' => 'gif',
                default => 'jpg',
            };

            $filename = 'prod_' . uniqid() . '_' . time() . '.' . $extension;
            $destination = $this->imageDir . '/' . $filename;

            if (file_put_contents($destination, $imageData) === false) {
                return null;
            }

            return $filename;
        } catch (\Throwable $e) {
            error_log("downloadExternalImage error: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Validar datos del producto.
     */
    private function validateProducto(array $data, bool $validateStock = true): array
    {
        $errors = [];

        if (empty($data['nombre'])) {
            $errors[] = 'El nombre es obligatorio.';
        } elseif (mb_strlen($data['nombre']) > 200) {
            $errors[] = 'El nombre no puede exceder 200 caracteres.';
        }

        if (empty($data['sku'])) {
            $errors[] = 'El SKU es obligatorio.';
        } elseif (mb_strlen($data['sku']) > 16) {
            $errors[] = 'El SKU no puede exceder 16 caracteres.';
        }

        if ($data['costo'] < 0) {
            $errors[] = 'El costo no puede ser negativo.';
        }

        if ($validateStock && $data['stock'] < 0) {
            $errors[] = 'El stock no puede ser negativo.';
        }

        if ($data['stock_minimo'] < 0) {
            $errors[] = 'El stock mínimo no puede ser negativo.';
        }

        // Validar formato de código de barras (si se proporcionó)
        if (!empty($data['codigo_barras']) && mb_strlen($data['codigo_barras']) > 50) {
            $errors[] = 'El código de barras no puede exceder 50 caracteres.';
        }

        // Removida la validación de fecha_vencimiento aquí porque ahora es manejada por el Lote.

        return $errors;
    }

    /**
     * Vista detalle de un producto con historial de movimientos y precios.
     */
    public function show(string $id): void
    {
        $producto = $this->productoModel->findWithCategory((int) $id);

        if (!$producto) {
            $this->setFlash('error', 'Producto no encontrado.');
            $this->redirect('productos');
            return;
        }

        $movimientos = $this->productoModel->getMovimientos((int) $id, 25);

        // Historial de costos
        $costoHistorialModel = new CostoHistorial();
        $costoHistorial = $costoHistorialModel->getByProducto((int) $id, 15);
        $costoChartData = $costoHistorialModel->getChartData((int) $id, 30);

        // Proveedores vinculados
        $proveedores = $this->ppModel->findByProducto((int) $id);

        // Todos los proveedores (para el modal de vincular)
        $todosProveedores = $this->proveedorModel->findAll('nombre', 'ASC');

        // Lotes del producto si es perecedero
        $lotes = [];
        if ($producto->es_perecedero) {
            require_once APP_PATH . '/models/Lote.php';
            $loteModel = new Lote();
            $lotes = $loteModel->rawQuery("SELECT * FROM lotes WHERE producto_id = ? ORDER BY fecha_vencimiento ASC", [(int)$id]);
        }

        $flash = $this->getFlash();
        $csrfToken = $this->generateCSRF();

        $this->view('productos/show', [
            'titulo' => $producto->nombre,
            'producto' => $producto,
            'movimientos' => $movimientos,
            'costoHistorial' => $costoHistorial,
            'costoChartData' => $costoChartData,
            'proveedores' => $proveedores,
            'todosProveedores' => $todosProveedores,
            'lotes' => $lotes,
            'csrfToken' => $csrfToken,
            'flash' => $flash,
            'loadChartJS' => true,
        ]);
    }

    /**
     * Actualizar la fecha de vencimiento de un lote.
     */
    public function updateLote(): void
    {
        if (!$this->validateCSRF()) {
            $this->redirect('productos');
            return;
        }

        $productoId = (int) $this->input('producto_id');
        $loteId = (int) $this->input('lote_id');
        $fechaVencimiento = $this->input('fecha_vencimiento');

        if (empty($loteId) || empty($fechaVencimiento)) {
            $this->setFlash('error', 'Datos del lote incompletos.');
            $this->redirect('productos/ver/' . $productoId);
            return;
        }

        require_once APP_PATH . '/models/Lote.php';
        $loteModel = new Lote();
        
        $lote = $loteModel->findById($loteId);
        if (!$lote) {
            $this->setFlash('error', 'Lote no encontrado.');
            $this->redirect('productos/ver/' . $productoId);
            return;
        }

        $loteModel->update($loteId, ['fecha_vencimiento' => $fechaVencimiento]);
        
        $this->securityService->logAction(
            currentUserId(),
            'update_lote',
            'productos',
            "Fecha de vencimiento actualizada para Lote #{$lote->numero_lote} (ID: {$loteId})"
        );

        $this->setFlash('success', 'Fecha de vencimiento actualizada correctamente.');
        $this->redirect('productos/ver/' . $productoId);
    }

    /**
     * Formulario de importación CSV.
     */
    public function importForm(): void
    {
        $csrfToken = $this->generateCSRF();
        $flash = $this->getFlash();

        $this->view('productos/import', [
            'titulo' => 'Importar Productos',
            'csrfToken' => $csrfToken,
            'flash' => $flash,
        ]);
    }

    /**
     * Procesar importación de productos desde CSV.
     */
    public function import(): void
    {
        if (!$this->validateCSRF()) {
            $this->redirect('productos/importar');
            return;
        }

        if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            $this->setFlash('error', 'Error al subir el archivo. Asegúrate de seleccionar un archivo CSV válido.');
            $this->redirect('productos/importar');
            return;
        }

        $file = $_FILES['csv_file'];

        // Validar extensión
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($ext !== 'csv') {
            $this->setFlash('error', 'Solo se permiten archivos CSV.');
            $this->redirect('productos/importar');
            return;
        }

        // Validar tamaño (5MB max)
        if ($file['size'] > 5 * 1024 * 1024) {
            $this->setFlash('error', 'El archivo no puede superar 5MB.');
            $this->redirect('productos/importar');
            return;
        }

        $handle = fopen($file['tmp_name'], 'r');
        if (!$handle) {
            $this->setFlash('error', 'No se pudo leer el archivo.');
            $this->redirect('productos/importar');
            return;
        }

        // Leer header
        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            $this->setFlash('error', 'El archivo CSV está vacío.');
            $this->redirect('productos/importar');
            return;
        }

        // Normalizar headers (quitar BOM, trim, lowercase)
        $header = array_map(function ($h) {
            return strtolower(trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $h)));
        }, $header);

        // Columnas requeridas
        $required = ['nombre', 'sku', 'costo', 'stock'];
        $missing = array_diff($required, $header);
        if (!empty($missing)) {
            fclose($handle);
            $this->setFlash('error', 'Faltan columnas requeridas: ' . implode(', ', $missing) . '. El CSV debe tener: nombre, sku, costo, stock.');
            $this->redirect('productos/importar');
            return;
        }

        $imported = 0;
        $skipped = 0;
        $errors = [];
        $row = 1;

        while (($data = fgetcsv($handle)) !== false) {
            $row++;

            if (count($data) < count($header)) {
                $errors[] = "Fila {$row}: número de columnas incorrecto.";
                $skipped++;
                continue;
            }

            $rowData = array_combine($header, $data);

            $nombre = trim($rowData['nombre'] ?? '');
            $sku = trim($rowData['sku'] ?? '');
            $costo = (float) ($rowData['costo'] ?? 0);
            $stock = (int) ($rowData['stock'] ?? 0);
            $stockMinimo = (int) ($rowData['stock_minimo'] ?? 5);
            $categoriaId = null;
            $descripcion = trim($rowData['descripcion'] ?? '');

            if (empty($nombre) || empty($sku)) {
                $errors[] = "Fila {$row}: nombre y SKU son obligatorios.";
                $skipped++;
                continue;
            }

            if ($this->productoModel->skuExists($sku)) {
                $errors[] = "Fila {$row}: SKU '{$sku}' ya existe.";
                $skipped++;
                continue;
            }

            // Buscar categoría por nombre si se proporcionó
            if (!empty($rowData['categoria'])) {
                $cat = $this->categoriaModel->findOneBy('nombre', trim($rowData['categoria']));
                $categoriaId = $cat ? $cat->id : null;
            }

            try {
                $this->productoModel->create([
                    'nombre' => $nombre,
                    'sku' => $sku,
                    'descripcion' => $descripcion,
                    'costo' => $costo,
                    'stock' => $stock,
                    'stock_minimo' => $stockMinimo,
                    'categoria_id' => $categoriaId,
                    'activo' => 1,
                ]);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Fila {$row}: error al insertar - " . $e->getMessage();
                $skipped++;
            }
        }

        fclose($handle);

        $this->securityService->logAction(
            currentUserId(),
            'importar_csv',
            'productos',
            "Importación CSV: {$imported} importados, {$skipped} omitidos"
        );

        $message = "✅ Importación completada: {$imported} productos importados.";
        if ($skipped > 0) {
            $message .= " {$skipped} filas omitidas.";
        }
        if (!empty($errors)) {
            $message .= '<br><small>' . implode('<br>', array_slice($errors, 0, 5)) . '</small>';
            if (count($errors) > 5) {
                $message .= '<br><small>...y ' . (count($errors) - 5) . ' errores más.</small>';
            }
        }

        $this->setFlash($imported > 0 ? 'success' : 'warning', $message);
        $this->redirect('productos');
    }

    /**
     * Búsqueda AJAX de productos (devuelve JSON).
     */
    public function search(): void
    {
        $q = $this->query('q', '');

        if (strlen($q) < 2) {
            $this->json([]);
            return;
        }

        $productos = $this->productoModel->searchQuick($q, 10);

        $results = array_map(function ($p) {
            return [
                'id' => $p->id,
                'nombre' => $p->nombre,
                'sku' => $p->sku,
                'categoria' => $p->categoria_nombre ?? 'Sin categoría',
                'costo' => formatMoney($p->costo),
                'stock' => (int) $p->stock,
                'imagen' => productImage($p->imagen ?? null),
                'url' => url("productos/ver/{$p->id}"),
            ];
        }, $productos);

        $this->json($results);
    }

    /**
     * AJAX: Vincular un proveedor a un producto.
     */
    public function addProveedor(): void
    {
        if (!$this->validateCSRF()) {
            $this->json(['success' => false, 'error' => 'Token inválido'], 403);
            return;
        }

        $productoId = (int) $this->input('producto_id');
        $proveedorId = (int) $this->input('proveedor_id');
        $costo = $this->input('costo') !== null ? (float) $this->input('costo') : null;
        $codigoProveedor = $this->input('codigo_proveedor', '');
        $tiempoEntrega = $this->input('tiempo_entrega_dias') !== null ? (int) $this->input('tiempo_entrega_dias') : null;
        $esPreferido = $this->input('es_preferido') ? 1 : 0;
        $notas = $this->input('notas', '');

        if ($productoId <= 0 || $proveedorId <= 0) {
            $this->json(['success' => false, 'error' => 'Datos inválidos'], 400);
            return;
        }

        // Verificar que producto y proveedor existen
        $producto = $this->productoModel->findById($productoId);
        $proveedor = $this->proveedorModel->findById($proveedorId);

        if (!$producto || !$proveedor) {
            $this->json(['success' => false, 'error' => 'Producto o proveedor no encontrado'], 404);
            return;
        }

        $data = [
            'codigo_proveedor' => $codigoProveedor ?: null,
            'costo' => $costo,
            'tiempo_entrega_dias' => $tiempoEntrega,
            'es_preferido' => $esPreferido,
            'notas' => $notas ?: null,
        ];

        $id = $this->ppModel->vincular($productoId, $proveedorId, $data);

        // Si es preferido, quitar preferido de los demás
        if ($esPreferido) {
            $this->ppModel->setPreferido($productoId, $id);
        }

        $this->securityService->logAction(
            currentUserId(),
            'vincular_proveedor',
            'productos',
            "Proveedor {$proveedor->nombre} vinculado a producto {$producto->nombre} (ID: {$productoId})"
        );

        $this->json(['success' => true, 'id' => $id]);
    }

    /**
     * AJAX: Desvincular un proveedor de un producto.
     */
    public function removeProveedor(): void
    {
        if (!$this->validateCSRF()) {
            $this->json(['success' => false, 'error' => 'Token inválido'], 403);
            return;
        }

        $vinculoId = (int) $this->input('vinculo_id');

        if ($vinculoId <= 0) {
            $this->json(['success' => false, 'error' => 'ID inválido'], 400);
            return;
        }

        $vinculo = $this->ppModel->findById($vinculoId);
        if (!$vinculo) {
            $this->json(['success' => false, 'error' => 'Vínculo no encontrado'], 404);
            return;
        }

        $this->ppModel->desvincular($vinculoId);

        $this->securityService->logAction(
            currentUserId(),
            'desvincular_proveedor',
            'productos',
            "Proveedor desvinculado del producto (vínculo ID: {$vinculoId})"
        );

        $this->json(['success' => true]);
    }
    /**
     * Imprimir etiquetas de múltiples productos.
     */
    public function imprimirMasivo(): void
    {
        $idsParam = $this->query('ids', '');
        if (empty($idsParam)) {
            die('No se seleccionaron productos.');
        }

        $ids = array_map('intval', explode(',', $idsParam));
        $ids = array_filter($ids, fn($id) => $id > 0);

        if (empty($ids)) {
            die('IDs inválidos.');
        }

        // Fetch products
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT id, sku, nombre, codigo_barras FROM productos WHERE id IN ($placeholders)";
        $productos = $this->productoModel->rawQuery($sql, $ids);

        if (empty($productos)) {
            die('Productos no encontrados.');
        }

        $this->view('productos/imprimir_masivo', [
            'productos' => $productos,
            'titulo' => 'Imprimir Etiquetas'
        ], false); // false to not include the default layout
    }
}

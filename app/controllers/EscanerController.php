<?php
/**
 * InvSys - EscanerController
 * 
 * Escáner de códigos de barras/QR por cámara.
 * Permite buscar productos escaneando su SKU, y si no existe,
 * consulta APIs externas para obtener información del producto.
 */

class EscanerController extends Controller
{
    private Producto $productoModel;

    public function __construct()
    {
        $this->productoModel = new Producto();
    }

    /**
     * Vista del escáner de cámara.
     */
    public function index(): void
    {
        $this->view('escaner/index', [
            'titulo'      => 'Escáner',
            'loadScanner' => true,
            'flash'       => $this->getFlash(),
        ]);
    }

    /**
     * AJAX: Buscar producto por código (SKU).
     * Si no se encuentra, intenta consultar APIs externas para datos del producto.
     */
    public function buscar(string $codigo): void
    {
        header('Content-Type: application/json');

        $codigo = trim(urldecode($codigo));

        if (empty($codigo)) {
            echo json_encode(['found' => false, 'error' => 'Código vacío']);
            return;
        }

        // 1. Buscar por código de barras (exacto)
        $producto = $this->productoModel->findByBarcode($codigo);

        // 2. Si no se encontró, buscar por SKU (exacto)
        if (!$producto) {
            $producto = $this->productoModel->findBySku($codigo);
        }

        if (!$producto) {
            // 3. Intentar búsqueda parcial por SKU o código de barras
            $productos = $this->productoModel->rawQuery(
                "SELECT id, nombre, sku, codigo_barras, stock, precio, unidad_medida, activo 
                 FROM productos WHERE sku LIKE :sku OR codigo_barras LIKE :barcode LIMIT 5",
                ['sku' => "%{$codigo}%", 'barcode' => "%{$codigo}%"]
            );

            if (empty($productos)) {
                // Producto no encontrado — ofrecer creación
                $this->respondNotFound($codigo);
                return;
            }

            // Si hay exactamente uno, usarlo
            if (count($productos) === 1) {
                $producto = $productos[0];
            } else {
                echo json_encode([
                    'found'    => false,
                    'multiple' => true,
                    'results'  => array_map(fn($p) => [
                        'id'     => $p->id,
                        'nombre' => $p->nombre,
                        'sku'    => $p->sku,
                        'barcode' => $p->codigo_barras ?? null,
                        'stock'  => $p->stock,
                        'url'    => url("productos/editar/{$p->id}"),
                    ], $productos),
                ]);
                return;
            }
        }

        echo json_encode([
            'found'   => true,
            'product' => [
                'id'            => $producto->id,
                'nombre'        => $producto->nombre,
                'sku'           => $producto->sku,
                'stock'         => $producto->stock,
                'precio'        => $producto->precio,
                'unidad_medida' => $producto->unidad_medida ?? 'Unidad',
                'activo'        => $producto->activo ?? 1,
                'urlEditar'     => url("productos/editar/{$producto->id}"),
                'urlMovimiento' => url("movimientos/crear"),
            ],
        ]);
    }

    /**
     * AJAX: Consultar APIs externas para obtener información de un código de barras.
     */
    public function lookupExterno(string $codigo): void
    {
        header('Content-Type: application/json');

        $codigo = trim(urldecode($codigo));

        if (empty($codigo)) {
            echo json_encode(['found' => false, 'error' => 'Código vacío']);
            return;
        }

        $lookupService = new BarcodeLookupService();
        $info = $lookupService->lookup($codigo);

        if ($info) {
            echo json_encode([
                'found'  => true,
                'lookup' => $info,
            ]);
        } else {
            echo json_encode([
                'found' => false,
                'error' => 'No se encontró información externa para este código.',
            ]);
        }
    }

    /**
     * Responder con datos de lookup externo cuando el producto no existe en el sistema.
     */
    private function respondNotFound(string $codigo): void
    {
        $response = [
            'found'     => false,
            'notInSystem' => true,
            'codigo'    => $codigo,
            'canCreate' => $this->userCanCreate(),
            'createUrl' => url("productos/crear"),
        ];

        // Intentar consultar APIs externas para obtener info del producto
        try {
            $lookupService = new BarcodeLookupService();
            $info = $lookupService->lookup($codigo);

            if ($info) {
                $response['lookup'] = $info;
            }
        } catch (\Throwable $e) {
            error_log("BarcodeLookup error: {$e->getMessage()}");
        }

        echo json_encode($response);
    }

    /**
     * Verificar si el usuario actual tiene permiso para crear productos.
     */
    private function userCanCreate(): bool
    {
        return hasPermission('productos.crear');
    }
}

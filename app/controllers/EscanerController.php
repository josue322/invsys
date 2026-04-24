<?php
/**
 * InvSys - EscanerController
 * 
 * Escáner de códigos de barras/QR por cámara.
 * Permite buscar productos escaneando su SKU.
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
     */
    public function buscar(string $codigo): void
    {
        header('Content-Type: application/json');

        $codigo = trim(urldecode($codigo));

        if (empty($codigo)) {
            echo json_encode(['found' => false, 'error' => 'Código vacío']);
            return;
        }

        // Buscar por SKU (exacto)
        $producto = $this->productoModel->findBySku($codigo);

        if (!$producto) {
            // Intentar búsqueda parcial
            $productos = $this->productoModel->rawQuery(
                "SELECT id, nombre, sku, stock, precio, unidad_medida, activo 
                 FROM productos WHERE sku LIKE :sku LIMIT 5",
                ['sku' => "%{$codigo}%"]
            );

            if (empty($productos)) {
                echo json_encode(['found' => false, 'error' => "No se encontró producto con código: {$codigo}"]);
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
}

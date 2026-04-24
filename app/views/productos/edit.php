<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card animate-fadeIn">
            <div class="card-header">
                <h6 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2"></i>Editar Producto</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= url("productos/editar/{$producto->id}") ?>" id="formEditarProducto" enctype="multipart/form-data">
                    <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">

                    <div class="row g-3">
                        <!-- Imagen del producto -->
                        <div class="col-md-4">
                            <label class="form-label">Imagen del Producto</label>
                            <div class="product-image-upload" id="imageUploadZone">
                                <div class="image-preview" id="imagePreview">
                                    <?php if (!empty($producto->imagen)): ?>
                                        <img src="<?= productImage($producto->imagen) ?>" alt="<?= htmlspecialchars($producto->nombre) ?>" 
                                             style="max-width:100%;max-height:200px;border-radius:8px;object-fit:cover;">
                                        <small class="text-muted mt-1"><?= htmlspecialchars($producto->imagen) ?></small>
                                    <?php else: ?>
                                        <i class="bi bi-cloud-arrow-up"></i>
                                        <span>Haga clic o arrastre una imagen</span>
                                        <small class="text-muted">JPG, PNG, WebP o GIF — Máx. 2MB</small>
                                    <?php endif; ?>
                                </div>
                                <input type="file" name="imagen" id="inputImagen" accept="image/jpeg,image/png,image/webp,image/gif" class="d-none">
                            </div>
                            <?php if (!empty($producto->imagen)): ?>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" id="eliminar_imagen" name="eliminar_imagen" value="1">
                                <label class="form-check-label text-danger" for="eliminar_imagen" style="font-size:0.85rem">
                                    <i class="bi bi-trash me-1"></i>Eliminar imagen actual
                                </label>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Datos principales -->
                        <div class="col-md-8">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label for="nombre" class="form-label">Nombre del Producto *</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" required maxlength="200"
                                           value="<?= htmlspecialchars($producto->nombre) ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="sku" class="form-label">SKU *</label>
                                    <input type="text" class="form-control" id="sku" name="sku" required maxlength="16"
                                           style="text-transform:uppercase" value="<?= htmlspecialchars($producto->sku) ?>">
                                </div>
                                <div class="col-12">
                                    <label for="descripcion" class="form-label">Descripción</label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?= htmlspecialchars($producto->descripcion ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Clasificación -->
                        <div class="col-md-4">
                            <label for="categoria_id" class="form-label">Categoría</label>
                            <select class="form-select" id="categoria_id" name="categoria_id">
                                <option value="">Sin categoría</option>
                                <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat->id ?>" <?= $producto->categoria_id == $cat->id ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat->nombre) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="ubicacion_id" class="form-label">Ubicación</label>
                            <select class="form-select" id="ubicacion_id" name="ubicacion_id">
                                <option value="">No asignada</option>
                                <?php foreach ($ubicaciones as $ub): ?>
                                <option value="<?= $ub->id ?>" <?= ($producto->ubicacion_id ?? '') == $ub->id ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($ub->nombre) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="unidad_medida" class="form-label">Unidad de Medida</label>
                            <select class="form-select" id="unidad_medida" name="unidad_medida">
                                <?php 
                                    $unidades = ['Unidad' => 'Unidad (Und)', 'Kilogramo' => 'Kilogramo (Kg)', 'Litro' => 'Litro (L)', 'Caja' => 'Caja', 'Paquete' => 'Paquete', 'Galon' => 'Galón'];
                                    foreach ($unidades as $val => $label):
                                ?>
                                <option value="<?= $val ?>" <?= ($producto->unidad_medida ?? 'Unidad') == $val ? 'selected' : '' ?>><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Financiero y Stock -->
                        <div class="col-md-3">
                            <label for="precio" class="form-label">Precio Referencial *</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="precio" name="precio" 
                                       step="0.01" min="0" value="<?= $producto->precio ?>" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Stock Actual Global</label>
                            <input type="text" class="form-control" value="<?= $producto->stock ?>" disabled>
                        </div>
                        <div class="col-md-3">
                            <label for="stock_minimo" class="form-label">Alerta Stock Mínimo</label>
                            <input type="number" class="form-control" id="stock_minimo" name="stock_minimo" 
                                   min="0" value="<?= $producto->stock_minimo ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="activo" class="form-label">Estado</label>
                            <select class="form-select" id="activo" name="activo">
                                <option value="1" <?= $producto->activo ? 'selected' : '' ?>>Activo</option>
                                <option value="0" <?= !$producto->activo ? 'selected' : '' ?>>Inactivo</option>
                            </select>
                        </div>

                        <!-- Sección: Producto Perecedero / Lotes -->
                        <div class="col-12">
                            <hr class="my-3">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <h6 class="fw-bold mb-3"><i class="bi bi-box-seam me-2 text-primary"></i>Configuración de Lotes (WMS)</h6>
                                    <div class="form-check form-switch fs-5 mb-2">
                                        <input class="form-check-input mt-1" type="checkbox" role="switch" id="es_perecedero" name="es_perecedero" value="1" <?= !empty($producto->es_perecedero) ? 'checked' : '' ?>>
                                        <label class="form-check-label fw-bold d-flex align-items-center" for="es_perecedero">
                                            Requiere Gestión por Lotes y Vencimientos
                                            <i class="bi bi-info-circle text-muted ms-2" data-bs-toggle="tooltip" title="Activa esta opción para obligar al ingreso de un Número de Lote y Fecha de Vencimiento al registrar entradas. Útil para alimentos o fármacos."></i>
                                        </label>
                                    </div>
                                    
                                    <div id="perecederoAlert" class="alert alert-success mt-3 mb-0 py-2 d-flex align-items-center fade show" role="alert" style="display: <?= !empty($producto->es_perecedero) ? 'block' : 'none' ?> !important;">
                                        <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                                        <div><small><strong>Configurado:</strong> Los lotes y fechas de vencimiento se solicitarán al registrar una <strong>Entrada</strong> en Movimientos.</small></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="d-flex justify-content-between">
                        <a href="<?= url('productos') ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary" id="btnActualizarProducto">
                            <i class="bi bi-check-lg me-1"></i>Actualizar Producto
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Barcode Section -->
        <div class="card animate-fadeIn mt-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-upc me-2"></i>Código de Barras</h6>
                <button class="btn btn-sm btn-outline-primary" id="btnPrintBarcode">
                    <i class="bi bi-printer me-1"></i>Imprimir Etiqueta
                </button>
            </div>
            <div class="card-body text-center">
                <svg id="barcode"></svg>
                <div class="mt-2">
                    <strong><?= htmlspecialchars($producto->sku) ?></strong>
                    <br><small class="text-muted"><?= htmlspecialchars($producto->nombre) ?></small>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Lotes toggle hint
    const perecederoSwitch = document.getElementById('es_perecedero');
    const perecederoAlert = document.getElementById('perecederoAlert');
    if(perecederoSwitch) {
        perecederoSwitch.addEventListener('change', function() {
            if (this.checked) {
                perecederoAlert.style.setProperty('display', 'block', 'important');
            } else {
                perecederoAlert.style.setProperty('display', 'none', 'important');
            }
        });
    }    // === Image Upload ===
    const zone = document.getElementById('imageUploadZone');
    const input = document.getElementById('inputImagen');
    const preview = document.getElementById('imagePreview');
    const deleteCheck = document.getElementById('eliminar_imagen');

    zone.addEventListener('click', (e) => {
        // No abrir selector si se clickea el checkbox de eliminar
        if (e.target.closest('.form-check')) return;
        input.click();
    });

    // Drag & Drop
    zone.addEventListener('dragover', (e) => {
        e.preventDefault();
        zone.classList.add('dragover');
    });
    zone.addEventListener('dragleave', () => zone.classList.remove('dragover'));
    zone.addEventListener('drop', (e) => {
        e.preventDefault();
        zone.classList.remove('dragover');
        if (e.dataTransfer.files.length) {
            input.files = e.dataTransfer.files;
            showPreview(e.dataTransfer.files[0]);
        }
    });

    input.addEventListener('change', function() {
        if (this.files.length) {
            showPreview(this.files[0]);
            // Si se sube una nueva imagen, desmarcar "eliminar"
            if (deleteCheck) deleteCheck.checked = false;
        }
    });

    function showPreview(file) {
        if (!file.type.startsWith('image/')) return;
        if (file.size > 2 * 1024 * 1024) {
            alert('La imagen excede el tamaño máximo de 2MB.');
            return;
        }
        const reader = new FileReader();
        reader.onload = (e) => {
            preview.innerHTML = `
                <img src="${e.target.result}" alt="Vista previa" style="max-width:100%;max-height:200px;border-radius:8px;object-fit:cover;">
                <small class="text-muted mt-2"><i class="bi bi-arrow-repeat me-1"></i>Nueva imagen: ${file.name} (${(file.size/1024).toFixed(0)} KB)</small>
            `;
        };
        reader.readAsDataURL(file);
    }

    // === Form Validation ===
    FormValidator.init('#formEditarProducto', {
        nombre: { required: true, maxlength: 200, messages: { required: 'El nombre del producto es obligatorio' } },
        sku:    { required: true, maxlength: 16, pattern: '^[A-Za-z0-9\\-_]+$',
                  messages: { required: 'El SKU es obligatorio', pattern: 'Solo letras, números, guiones y guiones bajos' } },
        precio: { required: true, min: 0, messages: { required: 'El precio es obligatorio' } },
        stock_minimo: { min: 0 }
    });

    // === Barcode Generation ===
    try {
        JsBarcode("#barcode", "<?= htmlspecialchars($producto->sku) ?>", {
            format: "CODE128",
            width: 2,
            height: 60,
            displayValue: false,
            margin: 10,
        });
    } catch(e) {
        console.warn('Barcode error:', e);
    }

    // === Print Barcode ===
    document.getElementById('btnPrintBarcode')?.addEventListener('click', function() {
        const svg = document.getElementById('barcode');
        const nombre = <?= json_encode($producto->nombre) ?>;
        const sku = <?= json_encode($producto->sku) ?>;
        const w = window.open('', '_blank', 'width=400,height=300');
        w.document.write(`
            <html><head><title>Etiqueta - ${sku}</title>
            <style>
                body { font-family: Arial, sans-serif; text-align: center; margin: 20px; }
                .label { border: 1px dashed #ccc; padding: 15px; display: inline-block; }
                .name { font-size: 12px; margin-top: 5px; }
                .sku { font-size: 14px; font-weight: bold; margin-top: 3px; }
                @media print { .label { border: none; } }
            </style></head><body>
            <div class="label">
                ${svg.outerHTML}
                <div class="sku">${sku}</div>
                <div class="name">${nombre}</div>
            </div>
            <script>window.onload = function() { window.print(); }<\/script>
            </body></html>`);
        w.document.close();
    });
});
</script>

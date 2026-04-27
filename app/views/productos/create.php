<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card animate-fadeIn">
            <div class="card-header">
                <h6 class="mb-0 fw-bold"><i class="bi bi-plus-circle me-2"></i>Nuevo Producto</h6>
            </div>
            <?php if (!empty($fromScanner)): ?>
            <div class="alert alert-info mb-0 rounded-0 border-start-0 border-end-0 d-flex align-items-center gap-2 py-2">
                <i class="bi bi-upc-scan fs-5"></i>
                <div>
                    <strong>Registro desde Escáner</strong>
                    <small class="d-block text-body-secondary">Los datos fueron capturados automáticamente. Complete los campos restantes.</small>
                </div>
                <a href="<?= url('escaner') ?>" class="btn btn-sm btn-outline-info ms-auto">
                    <i class="bi bi-arrow-left me-1"></i>Volver al Escáner
                </a>
            </div>
            <?php endif; ?>
            <div class="card-body">
                <form method="POST" action="<?= url('productos/crear') ?>" id="formCrearProducto" enctype="multipart/form-data">
                    <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">

                    <div class="row g-3">
                        <!-- Imagen del producto -->
                        <div class="col-md-4">
                            <label class="form-label">Imagen del Producto</label>
                            <div class="product-image-upload" id="imageUploadZone">
                                <div class="image-preview" id="imagePreview">
                                    <i class="bi bi-cloud-arrow-up"></i>
                                    <span>Haga clic o arrastre una imagen</span>
                                    <small class="text-muted">JPG, PNG, WebP o GIF — Máx. 2MB</small>
                                </div>
                                <input type="file" name="imagen" id="inputImagen" accept="image/jpeg,image/png,image/webp,image/gif" class="d-none">
                            </div>
                        </div>

                        <!-- Datos principales -->
                        <div class="col-md-8">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label for="nombre" class="form-label">Nombre del Producto *</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" required maxlength="200"
                                           value="<?= htmlspecialchars($prefill['nombre'] ?? '') ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="sku" class="form-label">SKU *</label>
                                    <input type="text" class="form-control" id="sku" name="sku" required maxlength="16" 
                                           style="text-transform:uppercase" placeholder="ELEC-001"
                                           value="<?= htmlspecialchars($prefill['sku'] ?? '') ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="codigo_barras" class="form-label"><i class="bi bi-upc-scan me-1"></i>Código de Barras</label>
                                    <input type="text" class="form-control" id="codigo_barras" name="codigo_barras" maxlength="50" 
                                           placeholder="EAN-13, UPC, etc.">
                                </div>
                                <div class="col-12">
                                    <label for="descripcion" class="form-label">Descripción</label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?= htmlspecialchars($prefill['descripcion'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Clasificación -->
                        <div class="col-md-4">
                            <label for="categoria_id" class="form-label">Categoría</label>
                            <select class="form-select" id="categoria_id" name="categoria_id">
                                <option value="">Sin categoría</option>
                                <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat->id ?>"><?= htmlspecialchars($cat->nombre) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="ubicacion_id" class="form-label">Ubicación</label>
                            <select class="form-select" id="ubicacion_id" name="ubicacion_id">
                                <option value="">No asignada</option>
                                <?php foreach ($ubicaciones as $ub): ?>
                                <option value="<?= $ub->id ?>"><?= htmlspecialchars($ub->nombre) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="unidad_medida" class="form-label">Unidad de Medida</label>
                            <select class="form-select" id="unidad_medida" name="unidad_medida">
                                <option value="Unidad">Unidad (Und)</option>
                                <option value="Kilogramo">Kilogramo (Kg)</option>
                                <option value="Litro">Litro (L)</option>
                                <option value="Caja">Caja</option>
                                <option value="Paquete">Paquete</option>
                                <option value="Galon">Galón</option>
                            </select>
                        </div>

                        <!-- Financiero y Stock -->
                        <div class="col-md-3">
                            <label for="precio" class="form-label">Precio Referencial *</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="precio" name="precio" step="0.01" min="0" value="0.00" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="precio_compra" class="form-label"><i class="bi bi-tag me-1"></i>Precio de Compra</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="precio_compra" name="precio_compra" step="0.01" min="0" value="0.00">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="stock" class="form-label">Stock Inicial Global</label>
                            <input type="number" class="form-control" id="stock" name="stock" min="0" value="0">
                        </div>
                        <div class="col-md-3">
                            <label for="stock_minimo" class="form-label">Alerta Stock Mínimo</label>
                            <input type="number" class="form-control" id="stock_minimo" name="stock_minimo" min="0" value="5">
                        </div>

                        <!-- Sección: Producto Perecedero / Lotes -->
                        <div class="col-12">
                            <hr class="my-3">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <h6 class="fw-bold mb-3"><i class="bi bi-box-seam me-2 text-primary"></i>Configuración de Lotes (WMS)</h6>
                                    <div class="form-check form-switch fs-5 mb-2">
                                        <input class="form-check-input mt-1" type="checkbox" role="switch" id="es_perecedero" name="es_perecedero" value="1">
                                        <label class="form-check-label fw-bold d-flex align-items-center" for="es_perecedero">
                                            Requiere Gestión por Lotes y Vencimientos
                                            <i class="bi bi-info-circle text-muted ms-2" data-bs-toggle="tooltip" title="Activa esta opción para obligar al ingreso de un Número de Lote y Fecha de Vencimiento al registrar entradas. Útil para alimentos o fármacos."></i>
                                        </label>
                                    </div>
                                    
                                    <div id="perecederoAlert" class="alert alert-success d-none mb-0 mt-3 py-2 d-flex align-items-center fade show" role="alert">
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
                        <button type="submit" class="btn btn-primary" id="btnGuardarProducto">
                            <i class="bi bi-check-lg me-1"></i>Guardar Producto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script id="page-data" type="application/json">{}</script>
<script src="<?= asset('js/productos.js') ?>?v=<?= ASSET_VERSION ?>"></script>


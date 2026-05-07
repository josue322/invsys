<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card animate-fadeIn">
            <div class="card-header">
                <h6 class="mb-0 fw-bold"><i class="bi bi-clipboard-plus me-2"></i>Nueva Sesión de Conteo</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= url('conteos/crear') ?>" id="formCrearConteo">
                    <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">

                    <div class="row g-3">
                        <div class="col-12">
                            <label for="nombre" class="form-label">Nombre de la Sesión *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required 
                                   maxlength="150" placeholder="Ej: Conteo Mensual Abril 2026">
                        </div>

                        <div class="col-12">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="2" 
                                      placeholder="Notas adicionales sobre esta auditoría..."></textarea>
                        </div>

                        <div class="col-12">
                            <hr class="my-2">
                            <h6 class="fw-bold"><i class="bi bi-funnel-fill text-primary me-2"></i>Filtrar Productos</h6>
                            <small class="text-muted d-block mb-3">Seleccione qué productos incluir en esta sesión de conteo.</small>
                        </div>

                        <div class="col-md-5">
                            <label class="form-label">Filtrar por</label>
                            <select class="form-select" id="filtro_tipo" name="filtro_tipo">
                                <option value="todos">Todos los productos</option>
                                <option value="categoria">Por Categoría</option>
                                <option value="ubicacion">Por Ubicación</option>
                            </select>
                        </div>

                        <div class="col-md-7 d-none" id="filtroCategoria">
                            <label class="form-label">Categoría</label>
                            <select class="form-select" name="filtro_id_categoria">
                                <option value="">Seleccione...</option>
                                <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat->id ?>"><?= htmlspecialchars($cat->nombre) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-7 d-none" id="filtroUbicacion">
                            <label class="form-label">Ubicación</label>
                            <select class="form-select" name="filtro_id_ubicacion">
                                <option value="">Seleccione...</option>
                                <?php foreach ($ubicaciones as $ub): ?>
                                <option value="<?= $ub->id ?>"><?= htmlspecialchars($ub->nombre) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Hidden field to send the actual filtro_id -->
                    <input type="hidden" name="filtro_id" id="filtro_id_hidden" value="">

                    <hr class="my-4">
                    <div class="d-flex justify-content-between">
                        <a href="<?= url('conteos') ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary" id="btnCrearConteo">
                            <i class="bi bi-clipboard-check me-1"></i>Crear Sesión
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formCrearConteo');
    if (!form) return;

    const STORAGE_KEY = 'invsys_draft_conteo';
    const inputsToSave = ['nombre', 'descripcion', 'filtro_tipo', 'filtro_id_categoria', 'filtro_id_ubicacion'];
    
    // Función para guardar el estado
    function saveFormState() {
        const state = {};
        inputsToSave.forEach(id => {
            const el = document.getElementById(id) || document.querySelector(`[name="${id}"]`);
            if (el) state[id] = el.value;
        });
        sessionStorage.setItem(STORAGE_KEY, JSON.stringify(state));
    }

    // Restaurar estado guardado
    const savedStateStr = sessionStorage.getItem(STORAGE_KEY);
    if (savedStateStr) {
        try {
            const state = JSON.parse(savedStateStr);
            inputsToSave.forEach(id => {
                if (state[id]) {
                    const el = document.getElementById(id) || document.querySelector(`[name="${id}"]`);
                    if (el) {
                        el.value = state[id];
                        // Disparar evento change para que otros scripts se actualicen (ej: mostrar/ocultar los div de filtros)
                        el.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                }
            });
        } catch (e) {}
    }

    // Escuchar cambios para auto-guardar
    form.addEventListener('input', saveFormState);
    form.addEventListener('change', saveFormState);

    // Limpiar storage al enviar exitosamente
    form.addEventListener('submit', function() {
        sessionStorage.removeItem(STORAGE_KEY);
    });
});
</script>




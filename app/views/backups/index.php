<!-- Toolbar -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <div>
        <h5 class="fw-800 mb-0"><i class="bi bi-database-fill-gear me-2"></i>Copias de Seguridad</h5>
        <small class="text-muted"><?= count($backups) ?> respaldo(s) disponible(s)</small>
    </div>
    <form method="POST" action="<?= url('backups/crear') ?>">
        <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">
        <button type="submit" class="btn btn-primary" id="btnCrearBackup">
            <i class="bi bi-plus-lg me-1"></i>Generar Backup
        </button>
    </form>
</div>

<!-- Info card -->
<div class="card mb-3 border-0" style="background: var(--bs-tertiary-bg);">
    <div class="card-body py-3">
        <div class="row align-items-center">
            <div class="col-auto">
                <i class="bi bi-info-circle-fill text-primary fs-4"></i>
            </div>
            <div class="col">
                <strong>¿Qué incluye el backup?</strong><br>
                <small class="text-muted">
                    Cada respaldo contiene la estructura completa y todos los datos de la base de datos 
                    (productos, movimientos, usuarios, configuraciones, logs, etc.).
                    Se recomienda generar respaldos antes de actualizaciones importantes.
                </small>
            </div>
            <div class="col-auto d-none d-md-block">
                <small class="text-muted">
                    <i class="bi bi-folder2-open me-1"></i>
                    Almacenados en: <code>storage/backups/</code>
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Backups Table -->
<div class="card">
    <div class="card-body p-0">
        <?php if (empty($backups)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg viewBox="0 0 100 100">
                        <circle class="ring-outer" cx="50" cy="50" r="46"></circle>
                        <circle class="ring-inner" cx="50" cy="50" r="38"></circle>
                    </svg>
                    <i class="bi bi-database"></i>
                </div>
                <h5>No hay respaldos</h5>
                <p class="text-muted">Genera tu primer backup para proteger tus datos.</p>
            </div>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width:50px">#</th>
                            <th>Archivo</th>
                            <th>Tamaño</th>
                            <th>Fecha de Creación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($backups as $i => $backup): ?>
                        <tr>
                            <td class="text-muted"><?= $i + 1 ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-file-earmark-code-fill text-primary fs-5"></i>
                                    <div>
                                        <span class="fw-bold"><?= htmlspecialchars($backup->filename) ?></span>
                                        <?php if ($i === 0): ?>
                                            <span class="badge bg-success ms-2" style="font-size:0.65rem">Más reciente</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-body-secondary text-body"><?= $backup->size ?></span>
                            </td>
                            <td><?= $backup->date ?></td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="<?= url("backups/descargar/{$backup->id}") ?>"
                                       class="btn-action btn-edit" title="Descargar">
                                        <i class="bi bi-download"></i>
                                    </a>

                                    <form method="POST" action="<?= url("backups/eliminar/{$backup->id}") ?>" style="display:inline"
                                          data-confirm='<?= json_encode([
                                              "title" => "¿Eliminar backup?",
                                              "message" => "Se eliminará <strong>" . htmlspecialchars($backup->filename) . "</strong> permanentemente.<br><br>Esta acción <strong>no se puede deshacer</strong>.",
                                              "type" => "danger",
                                              "confirmText" => "Sí, eliminar",
                                              "icon" => "bi-trash-fill"
                                          ], JSON_HEX_APOS | JSON_HEX_QUOT) ?>'>
                                        <?= csrfField() ?>
                                        <button type="submit" class="btn-action btn-delete" title="Eliminar">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Recommended section -->
<div class="card mt-3 border-0" style="background: var(--bs-tertiary-bg);">
    <div class="card-body py-3">
        <h6 class="fw-bold mb-2"><i class="bi bi-lightbulb-fill text-warning me-2"></i>Recomendaciones</h6>
        <div class="row g-3">
            <div class="col-md-4">
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-calendar-check text-success"></i>
                    <small>Genera un backup <strong>al menos una vez por semana</strong>.</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-cloud-arrow-up text-primary"></i>
                    <small><strong>Descarga</strong> y guarda copias en un lugar externo (USB, nube).</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-arrow-repeat text-info"></i>
                    <small>Genera un backup <strong>antes de actualizar</strong> el sistema.</small>
                </div>
            </div>
        </div>
    </div>
</div>

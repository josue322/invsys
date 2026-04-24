<!-- Toolbar -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <div>
        <h5 class="fw-800 mb-0"><i class="bi bi-clipboard-check me-2"></i>Conteo Físico</h5>
        <small class="text-muted">Auditorías de inventario — <?= count($conteos) ?> sesión(es)</small>
    </div>
    <a href="<?= url('conteos/crear') ?>" class="btn btn-primary" id="btnNuevoConteo">
        <i class="bi bi-plus-lg me-1"></i>Nuevo Conteo
    </a>
</div>

<!-- Info -->
<div class="card mb-3 border-0" style="background: var(--bs-tertiary-bg);">
    <div class="card-body py-3">
        <div class="row align-items-center">
            <div class="col-auto">
                <i class="bi bi-info-circle-fill text-primary fs-4"></i>
            </div>
            <div class="col">
                <strong>¿Cómo funciona?</strong><br>
                <small class="text-muted">
                    Cree una sesión → Registre el conteo físico de cada producto → 
                    Cierre la sesión → Aplique los ajustes automáticos para corregir diferencias.
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Table -->
<div class="card">
    <div class="card-body p-0">
        <?php if (empty($conteos)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg viewBox="0 0 100 100">
                        <circle class="ring-outer" cx="50" cy="50" r="46"></circle>
                        <circle class="ring-inner" cx="50" cy="50" r="38"></circle>
                    </svg>
                    <i class="bi bi-clipboard-check"></i>
                </div>
                <h5>No hay sesiones de conteo</h5>
                <p class="text-muted">Cree su primera auditoría de inventario físico.</p>
            </div>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width:50px">#</th>
                            <th>Sesión</th>
                            <th>Estado</th>
                            <th>Progreso</th>
                            <th>Diferencias</th>
                            <th>Creado por</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($conteos as $c): ?>
                        <?php
                            $pct = $c->total_productos > 0 
                                ? round(($c->productos_contados / $c->total_productos) * 100) 
                                : 0;
                            $estadoBadge = match($c->estado) {
                                'abierto'  => 'bg-success',
                                'cerrado'  => 'bg-warning text-dark',
                                'ajustado' => 'bg-secondary',
                                default    => 'bg-light text-dark',
                            };
                            $estadoLabel = match($c->estado) {
                                'abierto'  => 'Abierto',
                                'cerrado'  => 'Cerrado',
                                'ajustado' => 'Ajustado',
                                default => $c->estado,
                            };
                        ?>
                        <tr>
                            <td class="text-muted"><?= $c->id ?></td>
                            <td>
                                <a href="<?= url("conteos/{$c->id}") ?>" class="fw-bold text-decoration-none">
                                    <?= htmlspecialchars($c->nombre) ?>
                                </a>
                                <?php if ($c->descripcion): ?>
                                    <br><small class="text-muted"><?= htmlspecialchars(mb_substr($c->descripcion, 0, 60)) ?></small>
                                <?php endif; ?>
                            </td>
                            <td><span class="badge <?= $estadoBadge ?>"><?= $estadoLabel ?></span></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress" style="width:80px;height:6px;">
                                        <div class="progress-bar <?= $pct === 100 ? 'bg-success' : 'bg-primary' ?>" 
                                             style="width:<?= $pct ?>%"></div>
                                    </div>
                                    <small class="text-muted"><?= $c->productos_contados ?>/<?= $c->total_productos ?></small>
                                </div>
                            </td>
                            <td>
                                <?php if ($c->productos_con_diferencia > 0): ?>
                                    <span class="badge bg-danger"><?= $c->productos_con_diferencia ?></span>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td><small><?= htmlspecialchars($c->usuario_nombre ?? '') ?></small></td>
                            <td><small class="text-muted"><?= date('d/m/Y H:i', strtotime($c->created_at)) ?></small></td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="<?= url("conteos/{$c->id}") ?>" class="btn-action btn-edit" title="Ver / Editar">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    <?php if ($c->estado === 'abierto'): ?>
                                    <form method="POST" action="<?= url("conteos/eliminar/{$c->id}") ?>" style="display:inline"
                                          data-confirm='<?= json_encode([
                                              "title" => "¿Eliminar sesión?",
                                              "message" => "Se eliminará <strong>" . htmlspecialchars($c->nombre) . "</strong> y todos sus conteos.",
                                              "type" => "danger",
                                              "confirmText" => "Sí, eliminar",
                                              "icon" => "bi-trash-fill"
                                          ], JSON_HEX_APOS | JSON_HEX_QUOT) ?>'>
                                        <?= csrfField() ?>
                                        <button type="submit" class="btn-action btn-delete" title="Eliminar">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <!-- Pagination -->
        <?php
            $pg = $pagination;
            $baseUrl = 'conteos';
            include APP_PATH . '/views/layouts/_pagination.php';
        ?>
        <?php endif; ?>
    </div>
</div>

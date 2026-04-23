<!-- Toolbar -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div>
        <h5 class="fw-800 mb-0">Logs de Seguridad y Auditoría</h5>
        <small class="text-muted"><?= $logs['total'] ?> registros</small>
    </div>
</div>

<!-- Filters -->
<form method="GET" action="<?= url('seguridad') ?>" class="filter-bar" id="filter-logs">
    <div class="form-group">
        <label>Módulo</label>
        <select name="modulo" class="form-select">
            <option value="">Todos</option>
            <?php foreach ($modulos as $mod): ?>
            <option value="<?= $mod ?>" <?= $modulo === $mod ? 'selected' : '' ?>><?= ucfirst($mod) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label>Fecha</label>
        <input type="date" name="fecha" class="form-control" value="<?= $fecha ?>">
    </div>
    <div class="form-group" style="flex:0">
        <label>&nbsp;</label>
        <button type="submit" class="btn btn-primary d-block"><i class="bi bi-search"></i></button>
    </div>
</form>

<!-- Table -->
<div class="card">
    <div class="card-body p-0">
        <?php if (empty($logs['data'])): ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg viewBox="0 0 100 100">
                        <circle class="ring-outer" cx="50" cy="50" r="46"></circle>
                        <circle class="ring-inner" cx="50" cy="50" r="38"></circle>
                    </svg>
                    <i class="bi bi-shield-check"></i>
                </div>
                <h5>Sin registros</h5>
                <p class="text-muted">No hay logs de auditoría para mostrar</p>
            </div>
        <?php else: ?>
        <div class="table-wrapper">
            <table class="table" id="tabla-logs">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Módulo</th>
                        <th>Acción</th>
                        <th>Detalles</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs['data'] as $log): ?>
                    <tr>
                        <td><small><?= formatDate($log->created_at) ?></small></td>
                        <td>
                            <?php if ($log->usuario_nombre): ?>
                                <strong><?= htmlspecialchars($log->usuario_nombre) ?></strong>
                            <?php else: ?>
                                <span class="text-muted">Sistema</span>
                            <?php endif; ?>
                        </td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary"><?= ucfirst($log->modulo) ?></span></td>
                        <td><code><?= htmlspecialchars($log->accion) ?></code></td>
                        <td><small class="text-muted"><?= truncate(htmlspecialchars($log->detalles ?? ''), 80) ?></small></td>
                        <td><small class="text-muted"><?= $log->ip ?></small></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($logs['pages'] > 1): ?>
        <?php
            $pg = $logs;
            $baseUrl = 'seguridad?modulo=' . urlencode($modulo) . '&fecha=' . urlencode($fecha);
            $range = 2;
            $startPage = max(1, $pg['current'] - $range);
            $endPage = min($pg['pages'], $pg['current'] + $range);
        ?>
        <div class="d-flex justify-content-between align-items-center px-3 py-3">
            <small class="text-muted">
                Mostrando <?= (($pg['current'] - 1) * $pg['perPage']) + 1 ?>–<?= min($pg['current'] * $pg['perPage'], $pg['total']) ?> de <?= $pg['total'] ?> registros
            </small>
            <nav>
                <ul class="pagination mb-0">
                    <li class="page-item <?= $pg['current'] <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= url($baseUrl . '&page=' . ($pg['current'] - 1)) ?>" aria-label="Anterior"><i class="bi bi-chevron-left"></i></a>
                    </li>
                    <?php if ($startPage > 1): ?>
                        <li class="page-item"><a class="page-link" href="<?= url($baseUrl . '&page=1') ?>">1</a></li>
                        <?php if ($startPage > 2): ?><li class="page-item disabled"><span class="page-link">…</span></li><?php endif; ?>
                    <?php endif; ?>
                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <li class="page-item <?= $i == $pg['current'] ? 'active' : '' ?>">
                        <a class="page-link" href="<?= url($baseUrl . '&page=' . $i) ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>
                    <?php if ($endPage < $pg['pages']): ?>
                        <?php if ($endPage < $pg['pages'] - 1): ?><li class="page-item disabled"><span class="page-link">…</span></li><?php endif; ?>
                        <li class="page-item"><a class="page-link" href="<?= url($baseUrl . '&page=' . $pg['pages']) ?>"><?= $pg['pages'] ?></a></li>
                    <?php endif; ?>
                    <li class="page-item <?= $pg['current'] >= $pg['pages'] ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= url($baseUrl . '&page=' . ($pg['current'] + 1)) ?>" aria-label="Siguiente"><i class="bi bi-chevron-right"></i></a>
                    </li>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

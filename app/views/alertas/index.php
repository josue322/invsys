<!-- Toolbar -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div>
        <h5 class="fw-800 mb-0">Alertas del Sistema</h5>
        <small class="text-muted"><?= $alertas['total'] ?> alertas</small>
    </div>
    <?php if (hasPermission('alertas.gestionar')): ?>
    <form method="POST" action="<?= url('alertas/leer-todas') ?>">
        <?= csrfField() ?>
        <button type="submit" class="btn btn-outline-primary" id="btn-leer-todas">
            <i class="bi bi-check-all me-1"></i>Marcar todas como leídas
        </button>
    </form>
    <?php endif; ?>
</div>

<!-- Filter tabs -->
<ul class="nav nav-pills mb-3" id="alert-tabs">
    <li class="nav-item">
        <a class="nav-link <?= $filter === 'todas' ? 'active' : '' ?>" href="<?= url('alertas?filter=todas') ?>">Todas</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $filter === 'no_leidas' ? 'active' : '' ?>" href="<?= url('alertas?filter=no_leidas') ?>">No leídas</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $filter === 'leidas' ? 'active' : '' ?>" href="<?= url('alertas?filter=leidas') ?>">Leídas</a>
    </li>
</ul>

<!-- Alerts List -->
<div class="card">
    <div class="card-body p-0">
        <?php if (empty($alertas['data'])): ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg viewBox="0 0 100 100">
                        <circle class="ring-outer" cx="50" cy="50" r="46"></circle>
                        <circle class="ring-inner" cx="50" cy="50" r="38"></circle>
                    </svg>
                    <i class="bi bi-bell-slash"></i>
                </div>
                <h5>Sin alertas</h5>
                <p class="text-muted">No hay alertas para mostrar</p>
            </div>
        <?php else: ?>
            <?php foreach ($alertas['data'] as $alerta): ?>
            <div class="d-flex align-items-start gap-3 px-4 py-3 border-bottom <?= !$alerta->leida ? 'bg-primary bg-opacity-10' : '' ?>">
                <div class="flex-shrink-0 mt-1">
                    <?php if ($alerta->tipo === 'stock_agotado'): ?>
                        <span class="badge badge-stock-out p-2"><i class="bi bi-exclamation-triangle-fill"></i></span>
                    <?php elseif ($alerta->tipo === 'stock_minimo'): ?>
                        <span class="badge badge-stock-low p-2"><i class="bi bi-exclamation-circle-fill"></i></span>
                    <?php else: ?>
                        <span class="badge bg-secondary p-2"><i class="bi bi-info-circle-fill"></i></span>
                    <?php endif; ?>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between">
                        <strong><?= htmlspecialchars($alerta->producto_nombre) ?></strong>
                        <small class="text-muted"><?= formatDate($alerta->created_at) ?></small>
                    </div>
                    <p class="mb-1 text-muted small"><?= htmlspecialchars($alerta->mensaje) ?></p>
                    <small class="text-muted">SKU: <?= $alerta->producto_sku ?> | Stock actual: <strong><?= $alerta->stock ?></strong></small>
                </div>
                <?php if (!$alerta->leida && hasPermission('alertas.gestionar')): ?>
                <div class="flex-shrink-0">
                    <form method="POST" action="<?= url("alertas/leer/{$alerta->id}") ?>">
                        <?= csrfField() ?>
                        <button type="submit" class="btn btn-sm btn-outline-success" title="Marcar como leída">
                            <i class="bi bi-check-lg"></i>
                        </button>
                    </form>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>

            <?php if ($alertas['pages'] > 1): ?>
            <?php
                $pg = $alertas;
                $baseUrl = 'alertas?filter=' . urlencode($filter);
                $range = 2;
                $startPage = max(1, $pg['current'] - $range);
                $endPage = min($pg['pages'], $pg['current'] + $range);
            ?>
            <div class="d-flex justify-content-between align-items-center px-3 py-3">
                <small class="text-muted">
                    Mostrando <?= (($pg['current'] - 1) * $pg['perPage']) + 1 ?>–<?= min($pg['current'] * $pg['perPage'], $pg['total']) ?> de <?= $pg['total'] ?> alertas
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

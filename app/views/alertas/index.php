<!-- Toolbar -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h2 class="h3 mb-0 text-body"><i class="bi bi-bell-fill me-2" style="color: var(--primary);"></i>Alertas del Sistema</h2>
        <p class="text-muted mb-0">Gestión de notificaciones e incidencias de inventario</p>
    </div>
    <div class="d-flex gap-2">
        <span class="badge border text-body-secondary d-flex align-items-center px-3 py-2">
            <span class="fs-6"><?= $alertas['total'] ?> alertas registradas</span>
        </span>
        <?php if (hasPermission('alertas.gestionar')): ?>
        <form method="POST" action="<?= url('alertas/leer-todas') ?>" class="m-0">
            <?= csrfField() ?>
            <button type="submit" class="btn btn-outline-primary shadow-sm" id="btn-leer-todas">
                <i class="bi bi-check-all me-1"></i>Marcar todas como leídas
            </button>
        </form>
        <?php endif; ?>
    </div>
</div>

<!-- Filter tabs -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-2">
        <ul class="nav nav-pills nav-fill" id="alert-tabs">
            <li class="nav-item">
                <a class="nav-link <?= $filter === 'todas' ? 'active fw-medium' : 'text-muted' ?>" href="<?= url('alertas?filter=todas') ?>">
                    <i class="bi bi-collection me-1"></i> Todas
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $filter === 'no_leidas' ? 'active fw-medium' : 'text-muted' ?>" href="<?= url('alertas?filter=no_leidas') ?>">
                    <i class="bi bi-envelope-exclamation me-1"></i> No leídas
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $filter === 'leidas' ? 'active fw-medium' : 'text-muted' ?>" href="<?= url('alertas?filter=leidas') ?>">
                    <i class="bi bi-envelope-open me-1"></i> Leídas
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- Alerts List -->
<?php if (empty($alertas['data'])): ?>
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body text-center py-5">
            <div class="empty-state-icon mx-auto mb-4" style="width: 80px; height: 80px; background: rgba(100,116,139,0.05); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="bi bi-bell text-muted" style="font-size: 2.5rem;"></i>
            </div>
            <h5 class="fw-bold text-body">Todo está bajo control</h5>
            <p class="text-muted mb-0">No hay alertas que coincidan con tu búsqueda actual.</p>
        </div>
    </div>
<?php else: ?>
    <div class="d-flex flex-column gap-3">
        <?php foreach ($alertas['data'] as $alerta): 
            $isUnread = !$alerta->leida;
            
            $theme = match($alerta->tipo) {
                'stock_agotado' => ['color' => 'danger', 'icon' => 'bi-x-circle', 'bg' => 'bg-danger-subtle'],
                'stock_minimo' => ['color' => 'warning', 'icon' => 'bi-exclamation-triangle', 'bg' => 'bg-warning-subtle'],
                default => ['color' => 'primary', 'icon' => 'bi-clock-history', 'bg' => 'bg-primary-subtle'] // Vencimientos
            };

            $porcentaje = 100;
            if (in_array($alerta->tipo, ['stock_minimo', 'stock_agotado']) && $alerta->stock_minimo > 0) {
                $porcentaje = min(100, max(0, ($alerta->stock / $alerta->stock_minimo) * 100));
            }
        ?>
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden <?= $isUnread ? '' : 'opacity-75' ?>" style="transition: transform 0.2s ease, box-shadow 0.2s ease;">
            <!-- Fina línea indicadora de estado -->
            <div class="position-absolute top-0 start-0 bottom-0 bg-<?= $isUnread ? $theme['color'] : 'secondary' ?>" style="width: 4px;"></div>
            
            <div class="card-body p-3 ps-4 d-flex flex-column flex-md-row align-items-md-center gap-3">
                
                <!-- Icono -->
                <div class="flex-shrink-0">
                    <div class="<?= $isUnread ? $theme['bg'] : 'bg-secondary-subtle' ?> text-<?= $isUnread ? $theme['color'] : 'secondary' ?> rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                        <i class="<?= $theme['icon'] ?> fs-5"></i>
                    </div>
                </div>

                <!-- Información Principal -->
                <div class="flex-grow-1 min-w-0">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                        <h6 class="mb-0 fw-bold text-truncate <?= $isUnread ? 'text-body' : 'text-muted' ?>" style="max-width: 100%;">
                            <?= htmlspecialchars($alerta->producto_nombre) ?>
                        </h6>
                        <?php if ($isUnread): ?>
                            <span class="badge bg-danger rounded-pill px-2 py-0" style="font-size: 0.65rem; height: 16px; line-height: 16px;">Nueva</span>
                        <?php endif; ?>
                    </div>
                    
                    <p class="mb-1 text-muted small text-truncate">
                        <?= htmlspecialchars($alerta->mensaje) ?>
                    </p>

                    <!-- Indicadores de Stock (Burn-down) y Metadatos -->
                    <div class="d-flex flex-wrap align-items-center gap-3 mt-2">
                        <span class="text-muted font-monospace" style="font-size: 0.75rem;">
                            <i class="bi bi-upc-scan me-1"></i><?= $alerta->producto_sku ?>
                        </span>
                        <span class="text-muted" style="font-size: 0.75rem;">
                            <i class="bi bi-clock me-1"></i><?= date('d/m/Y H:i', strtotime($alerta->created_at)) ?>
                        </span>

                        <?php if (in_array($alerta->tipo, ['stock_minimo', 'stock_agotado'])): ?>
                            <div class="d-flex align-items-center gap-2 ms-md-auto" style="min-width: 150px;">
                                <div class="progress flex-grow-1 bg-light" style="height: 5px;">
                                    <div class="progress-bar bg-<?= $theme['color'] ?>" role="progressbar" style="width: <?= $porcentaje ?>%"></div>
                                </div>
                                <span class="small fw-medium text-muted" style="font-size: 0.75rem;">
                                    <?= $alerta->stock ?> / <?= $alerta->stock_minimo ?>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Acciones -->
                <div class="flex-shrink-0 d-flex flex-row flex-md-column gap-2 mt-3 mt-md-0 border-top border-md-top-0 pt-3 pt-md-0 ps-md-3 ms-md-2" style="border-left: 1px solid rgba(0,0,0,0.05);">
                    <a href="<?= url('productos/ver/' . $alerta->producto_id) ?>" class="btn btn-sm btn-light text-secondary border hover-bg-secondary w-100 px-3" title="Ver producto">
                        <i class="bi bi-box-arrow-up-right d-md-none me-1"></i>
                        <span class="d-none d-md-inline">Detalles</span>
                    </a>
                    
                    <?php if (in_array($alerta->tipo, ['stock_minimo', 'stock_agotado']) && hasPermission('requisiciones.crear')): ?>
                    <a href="<?= url('requisiciones/crear') ?>" class="btn btn-sm btn-primary-subtle text-primary border border-primary-subtle hover-bg-primary w-100 px-3" title="Solicitar stock">
                        <i class="bi bi-cart-plus d-md-none me-1"></i>
                        <span class="d-none d-md-inline">Solicitar</span>
                    </a>
                    <?php endif; ?>

                    <?php if ($isUnread && hasPermission('alertas.gestionar')): ?>
                    <form method="POST" action="<?= url("alertas/leer/{$alerta->id}") ?>" class="m-0 w-100">
                        <?= csrfField() ?>
                        <button type="submit" class="btn btn-sm btn-success-subtle text-success border border-success-subtle hover-bg-success w-100 px-3" title="Marcar leída">
                            <i class="bi bi-check2 d-md-none me-1"></i>
                            <span class="d-none d-md-inline">Leída</span>
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="mt-4">
        <?php
            $pg = $alertas;
            $baseUrl = 'alertas?filter=' . urlencode($filter);
            include APP_PATH . '/views/layouts/_pagination.php';
        ?>
    </div>
<?php endif; ?>

<style>
.card:hover { transform: translateY(-1px); box-shadow: 0 .25rem .75rem rgba(0,0,0,.05) !important; }
.bg-danger-subtle { background-color: rgba(239, 68, 68, 0.1) !important; color: #dc2626 !important; }
.bg-warning-subtle { background-color: rgba(245, 158, 11, 0.15) !important; color: #b45309 !important; }
.bg-primary-subtle { background-color: rgba(99, 102, 241, 0.1) !important; color: #4f46e5 !important; }
.bg-success-subtle { background-color: rgba(34, 197, 94, 0.1) !important; color: #16a34a !important; }
.bg-secondary-subtle { background-color: rgba(100, 116, 139, 0.1) !important; color: #64748b !important; }

/* Custom button hover states to match subtle backgrounds */
.btn-light:hover { background-color: #f1f5f9; border-color: #cbd5e1; }
.btn-primary-subtle:hover { background-color: var(--primary); color: white !important; }
.btn-success-subtle:hover { background-color: #16a34a; color: white !important; }

/* Force primary color for active tabs to match system theme */
.nav-pills .nav-link.active {
    background-color: var(--primary) !important;
    color: white !important;
}
.nav-pills .nav-link:not(.active):hover {
    background-color: rgba(0,0,0,0.03);
}

/* Force primary color for outline buttons to match system theme */
.btn-outline-primary {
    color: var(--primary) !important;
    border-color: var(--primary) !important;
}
.btn-outline-primary:hover {
    background-color: var(--primary) !important;
    color: #ffffff !important;
}

@media (min-width: 768px) {
    .border-start-md {
        border-left: 1px solid var(--border-color) !important;
    }
}
</style>

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
            
            // Usar colores nativos del sistema InvSys
            $theme = match($alerta->tipo) {
                'stock_agotado' => ['color' => '#ef4444', 'icon' => 'bi-x-octagon-fill', 'bg' => 'rgba(239, 68, 68, 0.1)', 'border' => 'rgba(239, 68, 68, 0.2)'],
                'stock_minimo' => ['color' => '#f59e0b', 'icon' => 'bi-exclamation-triangle-fill', 'bg' => 'rgba(245, 158, 11, 0.1)', 'border' => 'rgba(245, 158, 11, 0.2)'],
                default => ['color' => 'var(--primary)', 'icon' => 'bi-info-circle-fill', 'bg' => 'rgba(99, 102, 241, 0.1)', 'border' => 'rgba(99, 102, 241, 0.2)']
            };

            $porcentaje = 100;
            if (in_array($alerta->tipo, ['stock_minimo', 'stock_agotado']) && $alerta->stock_minimo > 0) {
                $porcentaje = min(100, max(0, ($alerta->stock / $alerta->stock_minimo) * 100));
            }
        ?>
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden <?= $isUnread ? '' : 'opacity-75' ?>" style="transition: all 0.2s ease; border: 1px solid <?= $isUnread ? $theme['border'] : 'var(--border-color)' ?> !important;">
            <!-- Fina línea indicadora de estado -->
            <div class="position-absolute top-0 start-0 bottom-0" style="width: 4px; background-color: <?= $isUnread ? $theme['color'] : 'var(--text-muted)' ?>;"></div>
            
            <div class="card-body p-3 ps-4 d-flex flex-column flex-md-row align-items-md-center gap-3">
                
                <!-- Icono -->
                <div class="flex-shrink-0">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 46px; height: 46px; background-color: <?= $isUnread ? $theme['bg'] : 'rgba(100,116,139,0.1)' ?>; color: <?= $isUnread ? $theme['color'] : 'var(--text-muted)' ?>;">
                        <i class="<?= $theme['icon'] ?> fs-5"></i>
                    </div>
                </div>

                <!-- Información Principal -->
                <div class="flex-grow-1 min-w-0">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                        <h6 class="mb-0 fw-bold text-truncate <?= $isUnread ? 'text-body' : 'text-muted' ?>" style="max-width: 100%; font-size: 1rem;">
                            <?= htmlspecialchars($alerta->producto_nombre) ?>
                        </h6>
                        <?php if ($isUnread): ?>
                            <span class="badge rounded-pill px-2 py-0" style="font-size: 0.65rem; height: 18px; line-height: 18px; background-color: <?= $theme['color'] ?>;">Nueva</span>
                        <?php endif; ?>
                    </div>
                    
                    <p class="mb-1 text-muted text-truncate" style="font-size: 0.9rem;">
                        <?= htmlspecialchars($alerta->mensaje) ?>
                    </p>

                    <!-- Indicadores de Stock (Burn-down) y Metadatos -->
                    <div class="d-flex flex-wrap align-items-center gap-3 mt-2">
                        <span class="text-muted font-monospace" style="font-size: 0.8rem; background: var(--bg-color); padding: 2px 6px; border-radius: 4px;">
                            <i class="bi bi-upc-scan me-1"></i><?= $alerta->producto_sku ?>
                        </span>
                        <span class="text-muted" style="font-size: 0.8rem;">
                            <i class="bi bi-clock me-1"></i><?= date('d/m/Y H:i', strtotime($alerta->created_at)) ?>
                        </span>

                        <?php if (in_array($alerta->tipo, ['stock_minimo', 'stock_agotado'])): ?>
                            <div class="d-flex align-items-center gap-2 ms-md-auto" style="min-width: 150px; background: var(--bg-color); padding: 4px 10px; border-radius: 20px;">
                                <div class="progress flex-grow-1" style="height: 6px; background-color: rgba(100,116,139,0.1);">
                                    <div class="progress-bar" role="progressbar" style="width: <?= $porcentaje ?>%; background-color: <?= $theme['color'] ?>; border-radius: 6px;"></div>
                                </div>
                                <span class="fw-bold" style="font-size: 0.8rem; color: <?= $theme['color'] ?>;">
                                    <?= $alerta->stock ?> / <?= $alerta->stock_minimo ?>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Acciones -->
                <div class="flex-shrink-0 d-flex flex-row flex-md-column gap-2 mt-3 mt-md-0 border-top border-md-top-0 pt-3 pt-md-0 ps-md-3 ms-md-2" style="border-left: 1px solid var(--border-color);">
                    <a href="<?= url('productos/ver/' . $alerta->producto_id) ?>" class="btn btn-sm btn-light text-secondary border hover-bg-secondary w-100 px-3" title="Ver producto" style="border-radius: 6px;">
                        <i class="bi bi-box-arrow-up-right d-md-none me-1"></i>
                        <span class="d-none d-md-inline fw-medium"><i class="bi bi-eye me-1"></i>Detalles</span>
                    </a>
                    
                    <?php if (in_array($alerta->tipo, ['stock_minimo', 'stock_agotado']) && hasPermission('requisiciones.crear')): ?>
                    <a href="<?= url('requisiciones/crear') ?>" class="btn btn-sm w-100 px-3" title="Solicitar stock" style="border-radius: 6px; background-color: rgba(99, 102, 241, 0.1); color: var(--primary); border: 1px solid rgba(99, 102, 241, 0.2);">
                        <i class="bi bi-cart-plus d-md-none me-1"></i>
                        <span class="d-none d-md-inline fw-medium"><i class="bi bi-cart-plus me-1"></i>Solicitar</span>
                    </a>
                    <?php endif; ?>

                    <?php if ($isUnread && hasPermission('alertas.gestionar')): ?>
                    <form method="POST" action="<?= url("alertas/leer/{$alerta->id}") ?>" class="m-0 w-100">
                        <?= csrfField() ?>
                        <button type="submit" class="btn btn-sm w-100 px-3" title="Marcar leída" style="border-radius: 6px; background-color: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2);">
                            <i class="bi bi-check2 d-md-none me-1"></i>
                            <span class="d-none d-md-inline fw-medium"><i class="bi bi-check2-all me-1"></i>Leída</span>
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



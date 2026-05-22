<?php

/** @var array $alertas */
/** @var string $filter */
if (!isset($alertas)) {
    $alertas = ['total' => 0, 'data' => []];
}
if (!isset($filter)) {
    $filter = 'todas';
}
?>
<!-- Toolbar / Cabecera de Módulo -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h2 class="h3 mb-1 text-body fw-bold d-flex align-items-center gap-2">
            <span class="p-2 rounded-3 d-inline-flex" style="background: rgba(99, 102, 241, 0.1); color: var(--primary);">
                <i class="bi bi-bell-fill"></i>
            </span>
            Alertas del Sistema
        </h2>
        <p class="text-muted mb-0" style="font-size: 0.9rem;">Centro integrado de incidencias de stock y alertas operativas</p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <span class="badge border text-secondary px-3 py-2 rounded-pill" style="font-size: 0.82rem; font-weight: 500; background: var(--bg-body);">
            <i class="bi bi-inbox-fill me-1 text-primary"></i> <?= $alertas['total'] ?? 0 ?> alertas registradas
        </span>
        <?php if (hasPermission('alertas.gestionar') && ($alertas['total'] ?? 0) > 0): ?>
            <form method="POST" action="<?= url('alertas/leer-todas') ?>" class="m-0">
                <?= csrfField() ?>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2 px-3 py-2 shadow-sm rounded-pill" id="btn-leer-todas" style="font-size: 0.85rem; font-weight: 600;">
                    <i class="bi bi-check-all"></i> Marcar todas como leídas
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>

<!-- Filtros de Pestañas -->
<div class="card border-0 shadow-sm mb-4 rounded-4" style="background: var(--bg-card);">
    <div class="card-body p-2">
        <div class="d-flex flex-wrap gap-1">
            <a class="btn btn-sm px-3 py-2 rounded-pill border-0 d-flex align-items-center gap-1 <?= $filter === 'todas' ? 'btn-primary shadow-sm fw-semibold' : 'text-secondary' ?>"
                href="<?= url('alertas?filter=todas') ?>" style="font-size: 0.85rem;">
                <i class="bi bi-collection"></i> Todas
            </a>
            <a class="btn btn-sm px-3 py-2 rounded-pill border-0 d-flex align-items-center gap-1 <?= $filter === 'no_leidas' ? 'btn-primary shadow-sm fw-semibold' : 'text-secondary' ?>"
                href="<?= url('alertas?filter=no_leidas') ?>" style="font-size: 0.85rem;">
                <i class="bi bi-envelope-exclamation"></i> No leídas
            </a>
            <a class="btn btn-sm px-3 py-2 rounded-pill border-0 d-flex align-items-center gap-1 <?= $filter === 'leidas' ? 'btn-primary shadow-sm fw-semibold' : 'text-secondary' ?>"
                href="<?= url('alertas?filter=leidas') ?>" style="font-size: 0.85rem;">
                <i class="bi bi-envelope-open"></i> Leídas
            </a>
        </div>
    </div>
</div>

<!-- Listado Inbox Feed -->
<?php if (empty($alertas['data'])): ?>
    <div class="card border-0 shadow-sm rounded-4" style="background: var(--bg-card);">
        <div class="card-body text-center py-5">
            <div class="mx-auto mb-4" style="width: 80px; height: 80px; background: rgba(99,102,241,0.05); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="bi bi-bell-slash text-primary" style="font-size: 2.5rem;"></i>
            </div>
            <h5 class="fw-bold text-body mb-2">Todo está al día</h5>
            <p class="text-muted mx-auto mb-0" style="max-width: 380px; font-size: 0.9rem;">No tienes alertas pendientes de revisión que coincidan con este filtro.</p>
        </div>
    </div>
<?php else: ?>
    <div class="card alert-feed-card">
        <div class="list-group list-group-flush">
            <?php foreach ($alertas['data'] as $alerta):
                $isUnread = !($alerta->leida ?? false);

                // Configurar temas específicos con seguridad
                $tipoAlerta = $alerta->tipo ?? 'info';
                $theme = match ($tipoAlerta) {
                    'stock_agotado' => [
                        'color' => '#ef4444',
                        'icon' => 'bi-x-octagon-fill',
                        'bg' => 'rgba(239, 68, 68, 0.08)'
                    ],
                    'stock_minimo' => [
                        'color' => '#f59e0b',
                        'icon' => 'bi-exclamation-triangle-fill',
                        'bg' => 'rgba(245, 158, 11, 0.08)'
                    ],
                    default => [
                        'color' => '#6366f1',
                        'icon' => 'bi-info-circle-fill',
                        'bg' => 'rgba(99, 102, 241, 0.08)'
                    ]
                };

                $stockActual = (int) ($alerta->stock ?? 0);
                $stockMinimo = (int) ($alerta->stock_minimo ?? 0);
                $porcentaje = 100;
                if (in_array($tipoAlerta, ['stock_minimo', 'stock_agotado']) && $stockMinimo > 0) {
                    $porcentaje = min(100, max(0, ($stockActual / $stockMinimo) * 100));
                }
            ?>
                <div class="list-group-item position-relative <?= $isUnread ? '' : 'opacity-65' ?>">

                    <!-- Barra indicadora lateral izquierda -->
                    <div class="position-absolute top-0 start-0 bottom-0" style="width: 4px; background-color: <?= $isUnread ? $theme['color'] : '#94a3b8' ?>; border-radius: 4px 0 0 4px;"></div>

                    <!-- Layout Principal: Apila en móvil, horizontal en desktop -->
                    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-3 ps-3">

                        <!-- Icono e Indicador de novedad -->
                        <div class="d-flex align-items-center gap-2 flex-shrink-0">
                            <?php if ($isUnread): ?>
                                <span class="alert-unread-indicator"></span>
                            <?php else: ?>
                                <span style="width: 8px;"></span>
                            <?php endif; ?>

                            <div class="alert-type-icon-wrapper flex-shrink-0" style="background-color: <?= $theme['bg'] ?>; color: <?= $theme['color'] ?>;">
                                <i class="<?= $theme['icon'] ?>"></i>
                            </div>
                        </div>

                        <!-- Detalles e Información de la Alerta -->
                        <div class="flex-grow-1 min-w-0" style="min-width: 0;">
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                <a href="<?= url('productos/ver/' . ($alerta->producto_id ?? 0)) ?>" class="h6 mb-0 fw-bold text-decoration-none text-body text-truncate" style="max-width: 280px; font-size: 0.96rem;">
                                    <?= htmlspecialchars($alerta->producto_nombre ?? 'Producto') ?>
                                </a>
                                <span class="alert-sku-badge"><i class="bi bi-upc-scan me-1"></i><?= htmlspecialchars($alerta->producto_sku ?? '---') ?></span>
                            </div>

                            <p class="mb-1 text-muted" style="font-size: 0.86rem; line-height: 1.4;">
                                <?= htmlspecialchars($alerta->mensaje ?? '') ?>
                            </p>

                            <!-- Meta detalles (fecha/hora) -->
                            <div class="d-flex flex-wrap align-items-center gap-3 text-secondary" style="font-size: 0.78rem;">
                                <span><i class="bi bi-clock me-1"></i><?= date('d/m/Y H:i', strtotime($alerta->created_at ?? 'now')) ?></span>

                                <!-- Cápsula de stock (inline en móvil) -->
                                <?php if (in_array($tipoAlerta, ['stock_minimo', 'stock_agotado'])): ?>
                                    <div class="alert-stock-capsule">
                                        <div class="alert-progress-bar-wrap">
                                            <div style="width: <?= $porcentaje ?>%; height: 100%; background-color: <?= $theme['color'] ?>; border-radius: 10px;"></div>
                                        </div>
                                        <span class="fw-bold" style="font-size: 0.78rem; color: <?= $theme['color'] ?>; font-family: var(--bs-font-monospace);">
                                            <?= $stockActual ?> / <?= $stockMinimo ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Acciones minimalistas -->
                        <div class="d-flex gap-2 flex-shrink-0 mt-2 mt-md-0 ps-md-3 border-start-md" style="border-color: var(--border-color);">
                            <a href="<?= url('productos/ver/' . ($alerta->producto_id ?? 0)) ?>"
                                class="btn alert-action-btn btn-action-view"
                                data-bs-toggle="tooltip"
                                data-bs-placement="top"
                                title="Ver Detalles">
                                <i class="bi bi-eye"></i>
                            </a>

                            <?php if (in_array($tipoAlerta, ['stock_minimo', 'stock_agotado']) && hasPermission('requisiciones.crear')): ?>
                                <a href="<?= url('requisiciones/crear') ?>"
                                    class="btn alert-action-btn btn-action-request"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="Solicitar Reabastecimiento">
                                    <i class="bi bi-cart-plus"></i>
                                </a>
                            <?php endif; ?>

                            <?php if ($isUnread && hasPermission('alertas.gestionar')): ?>
                                <form method="POST" action="<?= url('alertas/leer/' . ($alerta->id ?? 0)) ?>" class="m-0">
                                    <?= csrfField() ?>
                                    <button type="submit"
                                        class="btn alert-action-btn btn-action-read"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        title="Marcar como leída">
                                        <i class="bi bi-check2"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Paginación -->
    <div class="mt-4">
        <?php
        $pg = $alertas;
        $baseUrl = 'alertas?filter=' . urlencode($filter);
        include APP_PATH . '/views/layouts/_pagination.php';
        ?>
    </div>
<?php endif; ?>

<!-- Inicialización de Bootstrap Tooltips -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function(el) {
            new bootstrap.Tooltip(el);
        });
    });
</script>
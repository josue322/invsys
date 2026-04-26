<!-- User Detail Header -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <a href="<?= url('usuarios') ?>" class="text-muted text-decoration-none mb-2 d-inline-block">
            <i class="bi bi-arrow-left me-1"></i>Volver a Usuarios
        </a>
        <h5 class="fw-800 mb-0"><?= htmlspecialchars($usuario->nombre) ?></h5>
        <small class="text-muted"><?= htmlspecialchars($usuario->email) ?></small>
    </div>
    <div class="d-flex gap-2">
        <?php if (hasPermission('usuarios.editar')): ?>
        <a href="<?= url("usuarios/editar/{$usuario->id}") ?>" class="btn btn-primary" id="btn-editar-usuario">
            <i class="bi bi-pencil me-1"></i>Editar
        </a>
        <?php endif; ?>
    </div>
</div>

<?php
/**
 * Mini-paginador inline para secciones de detalle.
 * Construye URLs que preservan ambos parámetros de paginación (pg_mov y pg_act).
 */
function buildDetailPagination(array $pg, string $paramName, int $userId, int $pgMov, int $pgAct): string {
    if ($pg['pages'] <= 1) return '';
    
    $html = '<div class="d-flex justify-content-between align-items-center px-3 py-2">';
    $html .= '<small class="text-muted">';
    $from = (($pg['current'] - 1) * $pg['perPage']) + 1;
    $to = min($pg['current'] * $pg['perPage'], $pg['total']);
    $html .= "Mostrando {$from}–{$to} de {$pg['total']}";
    $html .= '</small>';
    
    $html .= '<nav><ul class="pagination pagination-sm mb-0">';
    
    // Construir parámetros preservando el otro paginador
    $buildUrl = function(int $page) use ($paramName, $userId, $pgMov, $pgAct) {
        $params = ['pg_mov' => $pgMov, 'pg_act' => $pgAct];
        $params[$paramName] = $page;
        return url("usuarios/ver/{$userId}?" . http_build_query($params));
    };
    
    // Anterior
    $disabled = $pg['current'] <= 1 ? ' disabled' : '';
    $html .= "<li class=\"page-item{$disabled}\"><a class=\"page-link\" href=\"{$buildUrl($pg['current'] - 1)}\"><i class=\"bi bi-chevron-left\"></i></a></li>";
    
    // Páginas
    $start = max(1, $pg['current'] - 2);
    $end = min($pg['pages'], $pg['current'] + 2);
    
    if ($start > 1) {
        $html .= "<li class=\"page-item\"><a class=\"page-link\" href=\"{$buildUrl(1)}\">1</a></li>";
        if ($start > 2) $html .= '<li class="page-item disabled"><span class="page-link">…</span></li>';
    }
    
    for ($i = $start; $i <= $end; $i++) {
        $active = $i == $pg['current'] ? ' active' : '';
        $html .= "<li class=\"page-item{$active}\"><a class=\"page-link\" href=\"{$buildUrl($i)}\">{$i}</a></li>";
    }
    
    if ($end < $pg['pages']) {
        if ($end < $pg['pages'] - 1) $html .= '<li class="page-item disabled"><span class="page-link">…</span></li>';
        $html .= "<li class=\"page-item\"><a class=\"page-link\" href=\"{$buildUrl($pg['pages'])}\">{$pg['pages']}</a></li>";
    }
    
    // Siguiente
    $disabled = $pg['current'] >= $pg['pages'] ? ' disabled' : '';
    $html .= "<li class=\"page-item{$disabled}\"><a class=\"page-link\" href=\"{$buildUrl($pg['current'] + 1)}\"><i class=\"bi bi-chevron-right\"></i></a></li>";
    
    $html .= '</ul></nav></div>';
    return $html;
}
?>

<div class="row g-3">
    <!-- Columna Izquierda: Info del Usuario -->
    <div class="col-lg-4">
        <!-- Avatar & Estado -->
        <div class="card mb-3">
            <div class="card-body text-center py-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                     style="width: 80px; height: 80px; font-size: 2rem; font-weight: 700;
                            background: linear-gradient(135deg, var(--accent-color), var(--accent-secondary));
                            color: #fff;">
                    <?= userInitials($usuario->nombre) ?>
                </div>
                <h5 class="fw-bold mb-1"><?= htmlspecialchars($usuario->nombre) ?></h5>
                <p class="text-muted mb-2"><?= htmlspecialchars($usuario->email) ?></p>
                <div class="d-flex justify-content-center gap-2">
                    <span class="badge <?= roleBadgeClass($usuario->rol_nombre ?? '') ?>"><?= $usuario->rol_nombre ?? '-' ?></span>
                    <?php if ($usuario->activo): ?>
                        <span class="badge badge-stock-ok">Activo</span>
                    <?php else: ?>
                        <span class="badge badge-stock-out">Inactivo</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Información General -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0 fw-bold"><i class="bi bi-info-circle me-2"></i>Información General</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-borderless mb-0">
                    <tbody>
                        <tr>
                            <td class="text-muted" style="width:45%">ID</td>
                            <td><code>#<?= $usuario->id ?></code></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nombre</td>
                            <td><strong><?= htmlspecialchars($usuario->nombre) ?></strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Email</td>
                            <td><?= htmlspecialchars($usuario->email) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Rol</td>
                            <td><span class="badge <?= roleBadgeClass($usuario->rol_nombre ?? '') ?>"><?= $usuario->rol_nombre ?? '-' ?></span></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Estado</td>
                            <td>
                                <?php if ($usuario->activo): ?>
                                    <span class="badge badge-stock-ok">Activo</span>
                                <?php else: ?>
                                    <span class="badge badge-stock-out">Inactivo</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Último Login</td>
                            <td><?= $usuario->ultimo_login ? formatDate($usuario->ultimo_login) : '<span class="text-muted">Nunca</span>' ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Registrado</td>
                            <td><small><?= formatDate($usuario->created_at) ?></small></td>
                        </tr>
                        <?php if ($usuario->updated_at): ?>
                        <tr>
                            <td class="text-muted">Actualizado</td>
                            <td><small><?= formatDate($usuario->updated_at) ?></small></td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sesiones Activas -->
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-shield-lock me-2"></i>Sesiones Activas</h6>
                <span class="badge bg-primary rounded-pill"><?= count($sesiones) ?></span>
            </div>
            <div class="card-body p-0">
                <?php if (empty($sesiones)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-shield-slash" style="font-size: 1.5rem;"></i>
                        <p class="mb-0 mt-2"><small>Sin sesiones activas</small></p>
                    </div>
                <?php else: ?>
                    <div class="table-wrapper">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>IP</th>
                                <th>Navegador</th>
                                <th>Inicio</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sesiones as $s): ?>
                            <tr>
                                <td><code><?= htmlspecialchars($s->ip ?? '—') ?></code></td>
                                <td><small class="text-muted"><?= htmlspecialchars(truncate($s->user_agent ?? '—', 40)) ?></small></td>
                                <td><small><?= formatDate($s->inicio, 'short') ?></small></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Columna Derecha: Actividad y Movimientos -->
    <div class="col-lg-8">
        <!-- Movimientos del Inventario -->
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-arrow-left-right me-2"></i>Movimientos Realizados</h6>
                <span class="badge bg-primary rounded-pill"><?= $movimientos['total'] ?></span>
            </div>
            <div class="card-body p-0">
                <?php if (empty($movimientos['data'])): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-inbox" style="font-size: 1.5rem;"></i>
                        <p class="mb-0 mt-2"><small>Este usuario no ha registrado movimientos</small></p>
                    </div>
                <?php else: ?>
                    <div class="table-wrapper">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Producto</th>
                                <th class="text-end">Cantidad</th>
                                <th class="text-end">Stock</th>
                                <th>Referencia</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($movimientos['data'] as $m): ?>
                            <tr>
                                <td><small class="text-muted tabular-nums"><?= formatDate($m->created_at, 'short') ?></small></td>
                                <td>
                                    <?php
                                        $tipoClass = match($m->tipo) {
                                            'entrada' => 'badge-stock-ok',
                                            'salida'  => 'badge-stock-out',
                                            default   => 'bg-info',
                                        };
                                        $tipoIcon = match($m->tipo) {
                                            'entrada' => 'bi-box-arrow-in-down',
                                            'salida'  => 'bi-box-arrow-up',
                                            default   => 'bi-arrow-repeat',
                                        };
                                    ?>
                                    <span class="badge <?= $tipoClass ?>">
                                        <i class="bi <?= $tipoIcon ?> me-1"></i><?= ucfirst($m->tipo) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= url("productos/ver/{$m->producto_id}") ?>" class="text-decoration-none">
                                        <?= htmlspecialchars($m->producto_nombre) ?>
                                    </a>
                                    <br><small class="text-muted"><?= htmlspecialchars($m->producto_sku) ?></small>
                                </td>
                                <td class="text-end tabular-nums fw-bold"><?= number_format($m->cantidad) ?></td>
                                <td class="text-end tabular-nums">
                                    <small class="text-muted"><?= $m->stock_anterior ?></small>
                                    <i class="bi bi-arrow-right" style="font-size:0.65rem;"></i>
                                    <strong><?= $m->stock_nuevo ?></strong>
                                </td>
                                <td><small class="text-muted"><?= htmlspecialchars($m->referencia ?? '—') ?></small></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    </div>
                    <?= buildDetailPagination($movimientos, 'pg_mov', $usuario->id, $pgMov, $pgAct) ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Actividad Reciente (Logs) -->
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2"></i>Actividad Reciente</h6>
                <span class="badge bg-primary rounded-pill"><?= $actividad['total'] ?></span>
            </div>
            <div class="card-body p-0">
                <?php if (empty($actividad['data'])): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-journal" style="font-size: 1.5rem;"></i>
                        <p class="mb-0 mt-2"><small>Sin actividad registrada</small></p>
                    </div>
                <?php else: ?>
                    <div class="table-wrapper">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Acción</th>
                                <th>Módulo</th>
                                <th>Detalles</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($actividad['data'] as $log): ?>
                            <tr>
                                <td><small class="text-muted tabular-nums"><?= formatDate($log->created_at, 'short') ?></small></td>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($log->accion) ?></span></td>
                                <td><small><?= htmlspecialchars($log->modulo) ?></small></td>
                                <td><small class="text-muted"><?= htmlspecialchars(truncate($log->detalles ?? '—', 80)) ?></small></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    </div>
                    <?= buildDetailPagination($actividad, 'pg_act', $usuario->id, $pgMov, $pgAct) ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

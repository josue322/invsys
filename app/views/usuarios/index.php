<!-- Toolbar -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div>
        <h5 class="fw-800 mb-0">Gestión de Usuarios</h5>
        <small class="text-muted"><?= $usuarios['total'] ?> usuarios encontrados</small>
    </div>
    <?php if (hasPermission('usuarios.crear')): ?>
    <a href="<?= url('usuarios/crear') ?>" class="btn btn-primary" id="btn-nuevo-usuario">
        <i class="bi bi-person-plus me-1"></i>Nuevo Usuario
    </a>
    <?php endif; ?>
</div>

<!-- Filters -->
<form method="GET" action="<?= url('usuarios') ?>" class="filter-bar" id="filter-usuarios">
    <div class="form-group" style="flex:2">
        <label>Buscar</label>
        <input type="text" name="search" class="form-control" placeholder="Nombre o email..." value="<?= htmlspecialchars($search) ?>">
    </div>
    <div class="form-group">
        <label>Rol</label>
        <select name="rol" class="form-select">
            <option value="">Todos</option>
            <?php foreach ($roles as $rol): ?>
            <option value="<?= $rol->id ?>" <?= $rolFilter == $rol->id ? 'selected' : '' ?>><?= htmlspecialchars($rol->nombre) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label>Estado</label>
        <select name="estado" class="form-select">
            <option value="">Todos</option>
            <option value="activo" <?= $estadoFilter === 'activo' ? 'selected' : '' ?>>Activo</option>
            <option value="inactivo" <?= $estadoFilter === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
        </select>
    </div>
    <div class="form-group" style="flex:0">
        <label>&nbsp;</label>
        <button type="submit" class="btn btn-primary d-block"><i class="bi bi-search"></i></button>
    </div>
</form>

<!-- Table -->
<div class="card">
    <div class="card-body p-0">
        <?php if (empty($usuarios['data'])): ?>
            <div class="empty-state" style="padding: 3.5rem 1rem;">
                <div class="empty-state-icon" style="width:90px;height:90px;margin-bottom:1.5rem;">
                    <svg viewBox="0 0 100 100">
                        <circle class="ring-outer" cx="50" cy="50" r="46"></circle>
                        <circle class="ring-inner" cx="50" cy="50" r="38"></circle>
                    </svg>
                    <i class="bi bi-people" style="font-size:2rem;"></i>
                </div>
                <h5 class="fw-bold mb-2">Sin resultados</h5>
                <p class="text-muted mb-3" style="max-width:320px;">No encontramos usuarios con esos criterios. Intente con otro filtro o agregue uno nuevo.</p>
                <?php if (hasPermission('usuarios.crear')): ?>
                <a href="<?= url('usuarios/crear') ?>" class="btn btn-primary btn-sm">
                    <i class="bi bi-person-plus me-1"></i>Nuevo Usuario
                </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
        <div class="table-wrapper">
            <table class="table" id="tabla-usuarios">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Último Login</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios['data'] as $u): ?>
                    <tr class="<?= !$u->activo ? 'row-inactive' : '' ?>">
                        <td>#<?= $u->id ?></td>
                        <td><strong><?= htmlspecialchars($u->nombre) ?></strong></td>
                        <td><?= htmlspecialchars($u->email) ?></td>
                        <td><span class="badge <?= roleBadgeClass($u->rol_nombre ?? '') ?>"><?= $u->rol_nombre ?? '-' ?></span></td>
                        <td>
                            <?php if ($u->activo): ?>
                                <span class="badge badge-stock-ok">Activo</span>
                            <?php else: ?>
                                <span class="badge badge-stock-out">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td><small class="text-muted"><?= $u->ultimo_login ? formatDate($u->ultimo_login) : 'Nunca' ?></small></td>
                        <td>
                            <div class="d-flex gap-1">
                            <?php if (hasPermission('usuarios.editar')): ?>
                            <a href="<?= url("usuarios/editar/{$u->id}") ?>" class="btn-action btn-edit" title="Editar">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                            <?php if ($u->id !== currentUserId()): ?>
                            <form action="<?= url("usuarios/toggle/{$u->id}") ?>" method="POST" class="d-inline">
                                <?= csrfField() ?>
                                <?php if ($u->activo): ?>
                                <button type="submit" class="btn-action" title="Desactivar" style="color: #f59e0b; background: rgba(245, 158, 11, 0.1);"
                                        data-confirm='{"title":"¿Desactivar usuario?","message":"El usuario <?= htmlspecialchars($u->nombre) ?> no podrá iniciar sesión.","type":"warning","confirmText":"Desactivar","icon":"bi-person-dash"}'>
                                    <i class="bi bi-person-dash-fill"></i>
                                </button>
                                <?php else: ?>
                                <button type="submit" class="btn-action" title="Activar" style="color: #10b981; background: rgba(16, 185, 129, 0.1);"
                                        data-confirm='{"title":"¿Activar usuario?","message":"El usuario <?= htmlspecialchars($u->nombre) ?> podrá iniciar sesión nuevamente.","type":"info","confirmText":"Activar","icon":"bi-person-check"}'>
                                    <i class="bi bi-person-check-fill"></i>
                                </button>
                                <?php endif; ?>
                            </form>
                            <form action="<?= url("usuarios/eliminar/{$u->id}") ?>" method="POST" class="d-inline">
                                <?= csrfField() ?>
                                <button type="submit" class="btn-action btn-delete" title="Eliminar"
                                        data-confirm='{"title":"¿Eliminar usuario?","message":"El usuario <?= htmlspecialchars($u->nombre) ?> será desactivado permanentemente. Esta acción se registra en el log de seguridad.","type":"danger","confirmText":"Eliminar","icon":"bi-trash3"}'>
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </form>
                            <?php endif; ?>
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
            $pg = $usuarios;
            $baseUrl = 'usuarios?search=' . urlencode($search) . '&rol=' . $rolFilter . '&estado=' . $estadoFilter;
            include APP_PATH . '/views/layouts/_pagination.php';
        ?>
        <?php endif; ?>
    </div>
</div>

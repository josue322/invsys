<!-- Toolbar -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div>
        <h5 class="fw-800 mb-0">Gestión de Usuarios</h5>
        <small class="text-muted"><?= $usuarios['total'] ?> usuarios registrados</small>
    </div>
    <?php if (hasPermission('usuarios.crear')): ?>
    <a href="<?= url('usuarios/crear') ?>" class="btn btn-primary" id="btn-nuevo-usuario">
        <i class="bi bi-person-plus me-1"></i>Nuevo Usuario
    </a>
    <?php endif; ?>
</div>

<!-- Table -->
<div class="card">
    <div class="card-body p-0">
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
                    <tr>
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
                            <?php if (hasPermission('usuarios.editar')): ?>
                            <a href="<?= url("usuarios/editar/{$u->id}") ?>" class="btn-action btn-edit" title="Editar">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                            <?php if ($u->id !== currentUserId()): ?>
                            <form action="<?= url("usuarios/toggle/{$u->id}") ?>" method="POST" class="d-inline">
                                <?= csrfField() ?>
                                <?php if ($u->activo): ?>
                                <button type="submit" class="btn-action btn-delete" title="Desactivar" 
                                        data-confirm="¿Desactivar al usuario <?= htmlspecialchars($u->nombre) ?>?">
                                    <i class="bi bi-person-dash-fill"></i>
                                </button>
                                <?php else: ?>
                                <button type="submit" class="btn-action" title="Activar" 
                                        style="color: #10b981;"
                                        data-confirm="¿Activar al usuario <?= htmlspecialchars($u->nombre) ?>?">
                                    <i class="bi bi-person-check-fill"></i>
                                </button>
                                <?php endif; ?>
                            </form>
                            <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php
            $pg = $usuarios;
            $baseUrl = 'usuarios';
            include APP_PATH . '/views/layouts/_pagination.php';
        ?>
    </div>
</div>

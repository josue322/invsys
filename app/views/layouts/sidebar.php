<!-- Sidebar Navigation -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="<?= url('dashboard') ?>" class="sidebar-brand" style="text-decoration:none;color:inherit;">
            <?php $logo = systemLogo(); ?>
            <?php if ($logo): ?>
                <img src="<?= $logo ?>" alt="Logo" class="brand-logo-img">
            <?php else: ?>
                <div class="brand-icon">
                    <i class="bi bi-box-seam-fill"></i>
                </div>
            <?php endif; ?>
            <span class="brand-text"><?= htmlspecialchars(systemName()) ?></span>
        </a>
        <button class="sidebar-toggle d-lg-none" id="sidebarClose">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <div class="sidebar-user">
        <div class="user-avatar">
            <?= userInitials() ?>
        </div>
        <div class="user-info">
            <span class="user-name"><?= currentUser()['nombre'] ?? 'Usuario' ?></span>
            <span class="badge <?= roleBadgeClass(currentUserRole()) ?> user-role"><?= currentUserRole() ?></span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">
            <span class="nav-section-title">Principal</span>
            
            <a href="<?= url('dashboard') ?>" class="nav-link <?= isRoutePrefix('dashboard') || currentUrl() === url('') ? 'active' : '' ?>" id="nav-dashboard">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </a>

            <?php if (hasPermission('alertas.ver')): ?>
            <a href="<?= url('alertas') ?>" class="nav-link <?= isRoutePrefix('alertas') ? 'active' : '' ?>" id="nav-alertas">
                <i class="bi bi-bell-fill"></i>
                <span>Alertas</span>
                <?php if (($alertasNoLeidas ?? 0) > 0): ?>
                    <span class="badge bg-danger ms-auto"><?= $alertasNoLeidas ?></span>
                <?php endif; ?>
            </a>
            <?php endif; ?>
        </div>

        <?php if (hasPermission('productos.ver')): ?>
        <div class="nav-section">
            <span class="nav-section-title">Inventario</span>
            
            <a href="<?= url('productos') ?>" class="nav-link <?= isRoutePrefix('productos') ? 'active' : '' ?>" id="nav-productos">
                <i class="bi bi-box-fill"></i>
                <span>Productos</span>
            </a>

            <?php if (hasPermission('movimientos.ver')): ?>
            <a href="<?= url('movimientos') ?>" class="nav-link <?= isRoutePrefix('movimientos') ? 'active' : '' ?>" id="nav-movimientos">
                <i class="bi bi-arrow-left-right"></i>
                <span>Movimientos</span>
            </a>
            
            <a href="<?= url('transferencias') ?>" class="nav-link <?= isRoutePrefix('transferencias') ? 'active' : '' ?>" id="nav-transferencias">
                <i class="bi bi-box-arrow-right"></i>
                <span>Transferencias</span>
            </a>
            <?php endif; ?>



            <a href="<?= url('escaner') ?>" class="nav-link <?= isRoutePrefix('escaner') ? 'active' : '' ?>" id="nav-escaner">
                <i class="bi bi-upc-scan"></i>
                <span>Escáner</span>
            </a>

            <?php if (hasPermission('movimientos.ver')): ?>
            <a href="<?= url('conteos') ?>" class="nav-link <?= isRoutePrefix('conteos') ? 'active' : '' ?>" id="nav-conteos">
                <i class="bi bi-clipboard-check"></i>
                <span>Conteo Físico</span>
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if (hasPermission('compras.ver')): ?>
        <div class="nav-section">
            <span class="nav-section-title">Abastecimiento</span>
            
            <a href="<?= url('compras') ?>" class="nav-link <?= isRoutePrefix('compras') ? 'active' : '' ?>" id="nav-compras">
                <i class="bi bi-cart-check-fill"></i>
                <span>Órdenes de Compra</span>
            </a>
        </div>
        <?php endif; ?>
        <?php if (hasPermission('requisiciones.ver')): ?>
        <div class="nav-section">
            <span class="nav-section-title">Logística Interna</span>
            
            <a href="<?= url('requisiciones') ?>" class="nav-link <?= isRoutePrefix('requisiciones') ? 'active' : '' ?>" id="nav-requisiciones">
                <i class="bi bi-inbox-fill"></i>
                <span>Requisiciones</span>
            </a>

            <?php if (hasPermission('devoluciones.ver')): ?>
            <a href="<?= url('devoluciones') ?>" class="nav-link <?= isRoutePrefix('devoluciones') ? 'active' : '' ?>" id="nav-devoluciones">
                <i class="bi bi-arrow-return-left"></i>
                <span>Devoluciones</span>
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if (hasPermission('categorias.ver')): ?>
        <div class="nav-section">
            <span class="nav-section-title">Catálogos</span>
            
            <a href="<?= url('categorias') ?>" class="nav-link <?= isRoutePrefix('categorias') ? 'active' : '' ?>" id="nav-categorias">
                <i class="bi bi-tags-fill"></i>
                <span>Categorías</span>
            </a>

            <a href="<?= url('proveedores') ?>" class="nav-link <?= isRoutePrefix('proveedores') ? 'active' : '' ?>" id="nav-proveedores">
                <i class="bi bi-truck"></i>
                <span>Proveedores</span>
            </a>

            <a href="<?= url('ubicaciones') ?>" class="nav-link <?= isRoutePrefix('ubicaciones') ? 'active' : '' ?>" id="nav-ubicaciones">
                <i class="bi bi-geo-alt-fill"></i>
                <span>Ubicaciones</span>
            </a>

            <?php if (hasPermission('departamentos.ver')): ?>
            <a href="<?= url('departamentos') ?>" class="nav-link <?= isRoutePrefix('departamentos') ? 'active' : '' ?>" id="nav-departamentos">
                <i class="bi bi-building"></i>
                <span>Departamentos</span>
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if (hasPermission('reportes.ver')): ?>
        <div class="nav-section">
            <span class="nav-section-title">Análisis</span>
            
            <a href="<?= url('reportes') ?>" class="nav-link <?= isRoute('reportes') ? 'active' : '' ?>" id="nav-reportes">
                <i class="bi bi-graph-up-arrow"></i>
                <span>Reportes</span>
            </a>

            <a href="<?= url('reportes/kardex') ?>" class="nav-link <?= isRoute('reportes/kardex') ? 'active' : '' ?>" id="nav-kardex">
                <i class="bi bi-journal-text"></i>
                <span>Kardex General</span>
            </a>

            <a href="<?= url('reportes/kardex-lote') ?>" class="nav-link <?= isRoute('reportes/kardex-lote') ? 'active' : '' ?>" id="nav-kardex-lote">
                <i class="bi bi-clock-history"></i>
                <span>Kardex por Lote</span>
            </a>

            <a href="<?= url('reportes/analisis/abc') ?>" class="nav-link <?= isRoute('reportes/analisis/abc') ? 'active' : '' ?>" id="nav-abc">
                <i class="bi bi-bar-chart-steps"></i>
                <span>Análisis ABC</span>
            </a>

            <a href="<?= url('reportes/analisis/rotacion') ?>" class="nav-link <?= isRoute('reportes/analisis/rotacion') ? 'active' : '' ?>" id="nav-rotacion">
                <i class="bi bi-arrow-repeat"></i>
                <span>Rotación</span>
            </a>

            <a href="<?= url('reportes/analisis/muertos') ?>" class="nav-link <?= isRoute('reportes/analisis/muertos') ? 'active' : '' ?>" id="nav-muertos">
                <i class="bi bi-moon-stars"></i>
                <span>Inv. Muerto</span>
            </a>
        </div>
        <?php endif; ?>

        <?php if (hasPermission('usuarios.ver') || hasPermission('configuracion.ver') || hasPermission('seguridad.ver')): ?>
        <div class="nav-section">
            <span class="nav-section-title">Administración</span>

            <?php if (hasPermission('usuarios.ver')): ?>
            <a href="<?= url('usuarios') ?>" class="nav-link <?= isRoutePrefix('usuarios') ? 'active' : '' ?>" id="nav-usuarios">
                <i class="bi bi-people-fill"></i>
                <span>Usuarios</span>
            </a>
            <?php endif; ?>

            <?php if (hasPermission('configuracion.ver')): ?>
            <a href="<?= url('configuracion') ?>" class="nav-link <?= isRoutePrefix('configuracion') ? 'active' : '' ?>" id="nav-configuracion">
                <i class="bi bi-gear-fill"></i>
                <span>Configuración</span>
            </a>
            <?php endif; ?>

            <?php if (hasPermission('seguridad.ver')): ?>
            <a href="<?= url('seguridad') ?>" class="nav-link <?= isRoutePrefix('seguridad') ? 'active' : '' ?>" id="nav-seguridad">
                <i class="bi bi-shield-lock-fill"></i>
                <span>Seguridad</span>
            </a>
            <?php endif; ?>

            <?php if (hasPermission('configuracion.editar')): ?>
            <a href="<?= url('backups') ?>" class="nav-link <?= isRoutePrefix('backups') ? 'active' : '' ?>" id="nav-backups">
                <i class="bi bi-database-fill-gear"></i>
                <span>Backups</span>
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="nav-section">
            <span class="nav-section-title">Asistencia</span>
            
            <a href="<?= url('ayuda') ?>" class="nav-link <?= isRoute('ayuda') ? 'active' : '' ?>" id="nav-ayuda">
                <i class="bi bi-question-circle-fill"></i>
                <span>Centro de Ayuda</span>
            </a>

            <a href="<?= url('ayuda/soporte') ?>" class="nav-link <?= isRoute('ayuda/soporte') ? 'active' : '' ?>" id="nav-soporte">
                <i class="bi bi-headset"></i>
                <span>Soporte Técnico</span>
            </a>
        </div>
    </nav>

    <div class="sidebar-footer">
        <button class="theme-toggle" id="themeToggle" title="Cambiar tema">
            <i class="bi <?= ($temaActual ?? 'light') === 'dark' ? 'bi-sun-fill' : 'bi-moon-fill' ?>"></i>
            <span><?= ($temaActual ?? 'light') === 'dark' ? 'Modo Claro' : 'Modo Oscuro' ?></span>
        </button>
        <a href="<?= url('logout') ?>" class="logout-btn" id="btn-logout">
            <i class="bi bi-box-arrow-left"></i>
            <span>Cerrar Sesión</span>
        </a>
    </div>
</aside>

<!-- Main Content Area -->
<main class="main-content" id="mainContent">
    <!-- Top Navbar -->
    <nav class="top-navbar">
        <button class="sidebar-toggle d-lg-none" id="sidebarOpen">
            <i class="bi bi-list"></i>
        </button>
        
        <div class="navbar-breadcrumb">
            <h4 class="page-title mb-0"><?= $titulo ?? 'Dashboard' ?></h4>
            <?php
                // Auto-generate breadcrumbs from URL
                $urlPath = isset($_GET['url']) ? trim($_GET['url'], '/') : '';
                $segments = $urlPath ? explode('/', $urlPath) : [];
                if (count($segments) > 0):
            ?>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size:0.78rem;">
                    <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>"><i class="bi bi-house-door"></i></a></li>
                    <?php foreach ($segments as $i => $segment): 
                        $isLast = ($i === count($segments) - 1);
                        $segmentUrl = implode('/', array_slice($segments, 0, $i + 1));
                        $label = ucfirst(str_replace(['-', '_'], ' ', $segment));
                        // Skip numeric IDs in display
                        if (is_numeric($segment)) {
                            $label = '#' . $segment;
                        }
                    ?>
                    <?php if ($isLast): ?>
                        <li class="breadcrumb-item active"><?= $label ?></li>
                    <?php else: ?>
                        <li class="breadcrumb-item"><a href="<?= url($segmentUrl) ?>"><?= $label ?></a></li>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </ol>
            </nav>
            <?php endif; ?>
        </div>

        <div class="navbar-actions">
            <!-- Global Search -->
            <div class="global-search-wrapper d-none d-md-block" style="position:relative;">
                <div class="input-group input-group-sm" style="width:260px;">
                    <span class="input-group-text" style="border:none;background:transparent;"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" class="form-control" id="global-search" 
                           placeholder="Buscar productos..." autocomplete="off"
                           style="border:none;background:var(--bs-tertiary-bg);border-radius:8px;">
                </div>
                <div id="search-results" class="search-results-dropdown"></div>
            </div>
            <!-- Alertas dropdown -->
            <?php if (hasPermission('alertas.ver')): ?>
            <div class="dropdown">
                <button class="navbar-action-btn dropdown-toggle" data-bs-toggle="dropdown" id="alertDropdown">
                    <i class="bi bi-bell"></i>
                    <?php if (($alertasNoLeidas ?? 0) > 0): ?>
                        <span class="notification-badge"><?= $alertasNoLeidas ?></span>
                    <?php endif; ?>
                </button>
                <div class="dropdown-menu dropdown-menu-end notification-dropdown">
                    <div class="dropdown-header d-flex justify-content-between align-items-center">
                        <span>Alertas</span>
                        <?php if (($alertasNoLeidas ?? 0) > 0): ?>
                            <span class="badge bg-danger"><?= $alertasNoLeidas ?> nuevas</span>
                        <?php endif; ?>
                    </div>
                    <div class="dropdown-divider"></div>
                    <a href="<?= url('alertas') ?>" class="dropdown-item text-center">
                        <small>Ver todas las alertas</small>
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <!-- User dropdown -->
            <div class="dropdown">
                <button class="user-dropdown-btn dropdown-toggle" data-bs-toggle="dropdown" id="userDropdown">
                    <div class="user-avatar-sm"><?= userInitials() ?></div>
                    <span class="d-none d-md-inline"><?= currentUser()['nombre'] ?? '' ?></span>
                </button>
                <div class="dropdown-menu dropdown-menu-end user-mega-dropdown p-0">
                    <!-- User header -->
                    <div class="udd-header">
                        <div class="udd-avatar"><?= userInitials() ?></div>
                        <div class="udd-info">
                            <span class="udd-name"><?= currentUser()['nombre'] ?? '' ?></span>
                            <span class="udd-email"><?= currentUser()['email'] ?? '' ?></span>
                            <span class="badge <?= roleBadgeClass(currentUserRole()) ?> udd-role-badge"><?= currentUserRole() ?></span>
                        </div>
                    </div>
                    <!-- Quick links -->
                    <div class="udd-section">
                        <a href="<?= url('perfil') ?>" class="udd-item">
                            <div class="udd-item-icon" style="background:rgba(99,102,241,0.1);color:var(--primary);">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <div class="udd-item-text">
                                <span>Mi Perfil</span>
                                <small>Datos personales y contraseña</small>
                            </div>
                            <i class="bi bi-chevron-right udd-item-arrow"></i>
                        </a>
                        <?php if (hasPermission('alertas.ver')): ?>
                        <a href="<?= url('alertas') ?>" class="udd-item">
                            <div class="udd-item-icon" style="background:rgba(245,158,11,0.1);color:#d97706;">
                                <i class="bi bi-bell-fill"></i>
                            </div>
                            <div class="udd-item-text">
                                <span>Notificaciones</span>
                                <small><?= ($alertasNoLeidas ?? 0) > 0 ? ($alertasNoLeidas . ' sin leer') : 'Al día' ?></small>
                            </div>
                            <?php if (($alertasNoLeidas ?? 0) > 0): ?>
                                <span class="badge bg-danger rounded-pill" style="font-size:0.65rem;"><?= $alertasNoLeidas ?></span>
                            <?php else: ?>
                                <i class="bi bi-chevron-right udd-item-arrow"></i>
                            <?php endif; ?>
                        </a>
                        <?php endif; ?>
                        <?php if (hasPermission('seguridad.ver')): ?>
                        <a href="<?= url('seguridad') ?>" class="udd-item">
                            <div class="udd-item-icon" style="background:rgba(16,185,129,0.1);color:#059669;">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <div class="udd-item-text">
                                <span>Registro de Actividad</span>
                                <small>Auditoría y sesiones</small>
                            </div>
                            <i class="bi bi-chevron-right udd-item-arrow"></i>
                        </a>
                        <?php endif; ?>
                        <?php if (hasPermission('configuracion.ver')): ?>
                        <a href="<?= url('configuracion') ?>" class="udd-item">
                            <div class="udd-item-icon" style="background:rgba(139,92,246,0.1);color:#7c3aed;">
                                <i class="bi bi-gear-fill"></i>
                            </div>
                            <div class="udd-item-text">
                                <span>Configuración</span>
                                <small>Preferencias del sistema</small>
                            </div>
                            <i class="bi bi-chevron-right udd-item-arrow"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                    <!-- Footer -->
                    <div class="udd-footer">
                        <a href="<?= url('logout') ?>" class="udd-logout-btn">
                            <i class="bi bi-box-arrow-left me-2"></i>Cerrar Sesión
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <div class="content-wrapper"
         <?php if (!empty($flash)): ?>
         data-flash-type="<?= $flash['type'] === 'error' ? 'error' : $flash['type'] ?>"
         data-flash-message="<?= htmlspecialchars($flash['message']) ?>"
         <?php endif; ?>>

        <!-- Toast Container -->
        <div id="toast-container" class="toast-container"></div>

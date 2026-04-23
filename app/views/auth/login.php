<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | <?= htmlspecialchars(systemName()) ?></title>
    <link rel="icon" href="<?= asset('favicon.svg') ?>" type="image/svg+xml">
    <link rel="alternate icon" href="<?= asset('favicon.ico') ?>" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= asset('css/style.css') ?>" rel="stylesheet">
</head>
<body>
    <div class="login-wrapper">
        <!-- Left Panel -->
        <div class="login-left">
            <!-- Floating orb decorations -->
            <div class="login-orb"></div>
            <div class="login-orb"></div>
            <div class="login-orb"></div>
            
            <div class="brand-logo">
                <div class="logo-icon">
                    <i class="bi bi-box-seam-fill"></i>
                </div>
                <h1><?= htmlspecialchars(systemName()) ?></h1>
                <p>Sistema de Gestión de Inventario Empresarial</p>
            </div>
        </div>

        <!-- Right Panel - Form -->
        <div class="login-right">
            <div class="login-form-container">
                <h2>Bienvenido 👋</h2>
                <p class="subtitle">Ingrese sus credenciales para acceder al sistema</p>

                <?php if (!empty($flash)): ?>
                <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : $flash['type'] ?> alert-dismissible fade show" role="alert" id="login-flash">
                    <i class="bi <?= $flash['type'] === 'error' ? 'bi-exclamation-triangle-fill' : 'bi-check-circle-fill' ?> me-2"></i>
                    <?= $flash['message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <form method="POST" action="<?= url('login') ?>" class="login-form" id="loginForm">
                    <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">

                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email" 
                                   placeholder="correo@ejemplo.com" required autofocus>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="••••••••" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-login" id="btnLogin">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
                    </button>
                </form>

                <div class="login-credentials">
                    <strong><i class="bi bi-info-circle me-1"></i>Credenciales de prueba:</strong><br>
                    <code>admin@invsys.com</code> / <code>Admin123!</code>
                </div>

                <?php if (filter_var(sysConfig('permitir_registro', '0'), FILTER_VALIDATE_BOOLEAN)): ?>
                <div class="login-credentials" style="text-align: center; margin-top: 8px;">
                    <span>¿No tiene cuenta?</span>
                    <a href="<?= url('registro') ?>" class="fw-semibold" style="color: var(--primary-color, #6366f1); text-decoration: none;">
                        <i class="bi bi-person-plus me-1"></i>Crear Cuenta
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= asset('js/form-validator.js') ?>?v=<?= time() ?>"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        FormValidator.init('#loginForm', {
            email:    { required: true, email: true, messages: { required: 'Ingrese su correo electrónico' } },
            password: { required: true, messages: { required: 'Ingrese su contraseña' } }
        });
    });
    </script>
</body>
</html>

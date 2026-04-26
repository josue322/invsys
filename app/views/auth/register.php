<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta | <?= htmlspecialchars(systemName()) ?></title>
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
                <h2>Crear Cuenta ✨</h2>
                <p class="subtitle">Complete los campos para registrarse en el sistema</p>

                <?php if (!empty($flash)): ?>
                <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : $flash['type'] ?> alert-dismissible fade show" role="alert" id="register-flash">
                    <i class="bi <?= $flash['type'] === 'error' ? 'bi-exclamation-triangle-fill' : 'bi-check-circle-fill' ?> me-2"></i>
                    <?= $flash['message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <form method="POST" action="<?= url('registro') ?>" class="login-form" id="registerForm">
                    <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">

                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre Completo</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" class="form-control" id="nombre" name="nombre"
                                   placeholder="Ej: Juan Pérez" required autofocus
                                   minlength="3" maxlength="100">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email"
                                   placeholder="correo@ejemplo.com" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password"
                                   placeholder="Mínimo 8 caracteres" required minlength="8">
                        </div>
                        <div class="form-text text-muted" style="font-size: 0.78rem; margin-top: 4px;">
                            <i class="bi bi-info-circle me-1"></i>Mínimo 8 caracteres, una mayúscula y un número
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password_confirm" class="form-label">Confirmar Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm"
                                   placeholder="Repita su contraseña" required minlength="8">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-login" id="btnRegister">
                        <i class="bi bi-person-plus me-2"></i>Crear Cuenta
                    </button>
                </form>

                <div class="login-credentials" style="text-align: center;">
                    <span>¿Ya tiene una cuenta?</span>
                    <a href="<?= url('login') ?>" class="fw-semibold" style="color: var(--primary-color, #6366f1); text-decoration: none;">
                        <i class="bi bi-box-arrow-in-right me-1"></i>Iniciar Sesión
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= asset('js/form-validator.js') ?>?v=<?= time() ?>"></script>
    <script src="<?= asset('js/forms.js') ?>?v=<?= time() ?>"></script>
</body>
</html>

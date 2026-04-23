<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Acceso denegado | <?= systemName() ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #fff5f5 0%, #fff1f2 50%, #fef2f2 100%);
            color: #1e293b;
        }
        .error-container {
            text-align: center;
            padding: 3rem 2rem;
            max-width: 520px;
        }
        .error-code {
            font-size: 8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #ef4444, #f97316, #f59e0b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 0.5rem;
        }
        .error-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            animation: shake 2s ease-in-out infinite;
        }
        @keyframes shake {
            0%, 100% { transform: rotate(0deg); }
            10%, 30% { transform: rotate(-5deg); }
            20%, 40% { transform: rotate(5deg); }
            50% { transform: rotate(0deg); }
        }
        .error-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #334155;
            margin-bottom: 0.75rem;
        }
        .error-message {
            font-size: 1rem;
            color: #64748b;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        .error-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
            border: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff;
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.45);
        }
        .btn-secondary {
            background: #fff;
            color: #6366f1;
            border: 2px solid #e2e8f0;
        }
        .btn-secondary:hover {
            border-color: #6366f1;
            background: #f8faff;
        }
        .error-detail {
            margin-top: 2rem;
            padding: 1rem 1.25rem;
            background: rgba(239, 68, 68, 0.06);
            border-radius: 10px;
            border-left: 4px solid #ef4444;
            font-size: 0.85rem;
            color: #64748b;
            text-align: left;
        }
        .error-detail strong { color: #dc2626; }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">🔒</div>
        <div class="error-code">403</div>
        <h1 class="error-title">Acceso denegado</h1>
        <p class="error-message">
            No tienes permisos suficientes para acceder a esta página.
            Contacta al administrador si crees que esto es un error.
        </p>
        <div class="error-actions">
            <a href="<?= url('dashboard') ?>" class="btn btn-primary">
                🏠 Ir al Dashboard
            </a>
            <button onclick="history.back()" class="btn btn-secondary">
                ← Volver atrás
            </button>
        </div>
        <div class="error-detail">
            <strong>🛡️ Acceso restringido.</strong> Tu rol actual no tiene los permisos
            necesarios para esta sección. Si necesitas acceso, solicítalo a tu administrador.
        </div>
    </div>
</body>
</html>

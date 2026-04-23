<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Página no encontrada | <?= systemName() ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f0f2ff 0%, #e8eaff 50%, #f5f3ff 100%);
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
            background: linear-gradient(135deg, #6366f1, #8b5cf6, #a78bfa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 0.5rem;
        }
        .error-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
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
        .error-hint {
            margin-top: 2rem;
            padding: 1rem;
            background: rgba(99, 102, 241, 0.06);
            border-radius: 10px;
            font-size: 0.85rem;
            color: #64748b;
        }
        .error-hint code {
            background: rgba(99, 102, 241, 0.1);
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.8rem;
            color: #6366f1;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">🔍</div>
        <div class="error-code">404</div>
        <h1 class="error-title">Página no encontrada</h1>
        <p class="error-message">
            La página que buscas no existe o fue movida. Verifica la URL e intenta de nuevo.
        </p>
        <div class="error-actions">
            <a href="<?= url('dashboard') ?>" class="btn btn-primary">
                🏠 Ir al Dashboard
            </a>
            <button onclick="history.back()" class="btn btn-secondary">
                ← Volver atrás
            </button>
        </div>
        <div class="error-hint">
            URL solicitada: <code><?= htmlspecialchars($_SERVER['REQUEST_URI'] ?? '/') ?></code>
        </div>
    </div>
</body>
</html>

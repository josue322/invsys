<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 — Error del servidor | <?= systemName() ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #fef2f2 0%, #fef9ee 50%, #fffbeb 100%);
            color: #1e293b;
        }
        .error-container {
            text-align: center;
            padding: 3rem 2rem;
            max-width: 580px;
        }
        .error-code {
            font-size: 8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #dc2626, #ea580c, #d97706);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 0.5rem;
        }
        .error-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            animation: pulse 2s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
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
        .error-debug {
            margin-top: 2rem;
            padding: 1rem 1.25rem;
            background: #1e293b;
            border-radius: 10px;
            text-align: left;
            overflow-x: auto;
        }
        .error-debug summary {
            color: #94a3b8;
            font-size: 0.85rem;
            cursor: pointer;
            font-weight: 600;
        }
        .error-debug pre {
            color: #f87171;
            font-size: 0.8rem;
            font-family: 'Courier New', monospace;
            white-space: pre-wrap;
            word-break: break-word;
            margin-top: 0.75rem;
            line-height: 1.5;
        }
        .error-timestamp {
            margin-top: 1.5rem;
            font-size: 0.8rem;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">⚙️</div>
        <div class="error-code">500</div>
        <h1 class="error-title">Error interno del servidor</h1>
        <p class="error-message">
            Algo salió mal al procesar tu solicitud. El equipo técnico ha sido notificado.
            Por favor intenta de nuevo en unos momentos.
        </p>
        <div class="error-actions">
            <a href="<?= url('dashboard') ?>" class="btn btn-primary">
                🏠 Ir al Dashboard
            </a>
            <button onclick="location.reload()" class="btn btn-secondary">
                🔄 Reintentar
            </button>
        </div>

        <?php if (ini_get('display_errors') && isset($exception)): ?>
            <div class="error-debug">
                <details>
                    <summary>🐛 Información de depuración (solo desarrollo)</summary>
                    <pre><?= htmlspecialchars($exception->getMessage()) ?>

Archivo: <?= htmlspecialchars($exception->getFile()) ?>:<?= $exception->getLine() ?>

Stack Trace:
<?= htmlspecialchars($exception->getTraceAsString()) ?></pre>
                </details>
            </div>
        <?php endif; ?>

        <div class="error-timestamp">
            🕐 <?= date('d/m/Y H:i:s') ?> · Ref: <?= substr(md5(microtime()), 0, 8) ?>
        </div>
    </div>
</body>
</html>

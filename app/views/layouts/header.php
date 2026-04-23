<!DOCTYPE html>
<html lang="es" data-bs-theme="<?= $temaActual ?? 'light' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars(systemName()) ?> - Sistema de Gestión de Inventario Web Empresarial">
    <meta name="base-url" content="<?= BASE_URL ?>">
    <title><?= ($titulo ?? systemName()) . ' | ' . systemName() ?></title>
    <link rel="icon" href="<?= asset('favicon.svg') ?>" type="image/svg+xml">
    <link rel="alternate icon" href="<?= asset('favicon.ico') ?>" type="image/x-icon">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Chart.js (carga condicional: solo dashboard y vistas que lo necesitan) -->
    <?php if (!empty($loadChartJS)): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <?php endif; ?>

    <!-- Custom CSS -->
    <?php define('ASSET_VERSION', '1.0.0'); ?>
    <link href="<?= asset('css/style.css') ?>?v=<?= ASSET_VERSION ?>" rel="stylesheet">
    <link href="<?= asset('css/dark-mode.css') ?>?v=<?= ASSET_VERSION ?>" rel="stylesheet">
</head>
<body class="d-flex<?= (($uiSettings['densidad_compacta'] ?? '0') === '1') ? ' compact-mode' : '' ?><?= (($uiSettings['animaciones'] ?? '1') === '0') ? ' no-animations' : '' ?>">
    <!-- Main wrapper -->
    <div id="app-wrapper" class="d-flex w-100">

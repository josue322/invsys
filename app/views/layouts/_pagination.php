<?php
/**
 * InvSys - Componente de Paginación Reutilizable
 * 
 * Variables esperadas:
 *   $pg       - Array con: data, total, pages, current, perPage
 *   $baseUrl  - URL base SIN page= ni per_page= (ej: 'productos?search=test&categoria=1')
 * 
 * Uso:
 *   <?php $pg = $productos; $baseUrl = 'productos?search=' . urlencode($search); ?>
 *   <?php include APP_PATH . '/views/layouts/_pagination.php'; ?>
 */

if (!isset($pg) || !isset($baseUrl) || $pg['total'] <= 0) return;

$range = 2;
$startPage = max(1, $pg['current'] - $range);
$endPage = min($pg['pages'], $pg['current'] + $range);
$currentPerPage = $pg['perPage'] ?? 15;
$perPageOptions = [10, 15, 25, 50];

// Construir URL helper que preserva per_page
$separator = str_contains($baseUrl, '?') ? '&' : '?';
$buildUrl = function(int $page) use ($baseUrl, $separator, $currentPerPage) {
    return url($baseUrl . $separator . 'page=' . $page . '&per_page=' . $currentPerPage);
};
$buildPerPageUrl = function(int $perPage) use ($baseUrl, $separator) {
    return url($baseUrl . $separator . 'page=1&per_page=' . $perPage);
};

$from = (($pg['current'] - 1) * $currentPerPage) + 1;
$to = min($pg['current'] * $currentPerPage, $pg['total']);
?>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 px-3 py-3">
    <div class="d-flex align-items-center gap-3">
        <small class="text-muted">
            Mostrando <?= $from ?>–<?= $to ?> de <?= number_format($pg['total']) ?> registros
        </small>
        <div class="d-flex align-items-center gap-1">
            <small class="text-muted text-nowrap">Mostrar:</small>
            <select class="form-select form-select-sm" style="width:auto;padding:2px 28px 2px 8px;font-size:0.8rem;" 
                    onchange="window.location.href=this.value" aria-label="Registros por página">
                <?php foreach ($perPageOptions as $opt): ?>
                <option value="<?= $buildPerPageUrl($opt) ?>" <?= $currentPerPage == $opt ? 'selected' : '' ?>>
                    <?= $opt ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <?php if ($pg['pages'] > 1): ?>
    <nav aria-label="Paginación">
        <ul class="pagination mb-0">
            <li class="page-item <?= $pg['current'] <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= $buildUrl($pg['current'] - 1) ?>" aria-label="Anterior">
                    <i class="bi bi-chevron-left"></i>
                </a>
            </li>
            <?php if ($startPage > 1): ?>
                <li class="page-item"><a class="page-link" href="<?= $buildUrl(1) ?>">1</a></li>
                <?php if ($startPage > 2): ?><li class="page-item disabled"><span class="page-link">…</span></li><?php endif; ?>
            <?php endif; ?>
            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
            <li class="page-item <?= $i == $pg['current'] ? 'active' : '' ?>">
                <a class="page-link" href="<?= $buildUrl($i) ?>"><?= $i ?></a>
            </li>
            <?php endfor; ?>
            <?php if ($endPage < $pg['pages']): ?>
                <?php if ($endPage < $pg['pages'] - 1): ?><li class="page-item disabled"><span class="page-link">…</span></li><?php endif; ?>
                <li class="page-item"><a class="page-link" href="<?= $buildUrl($pg['pages']) ?>"><?= $pg['pages'] ?></a></li>
            <?php endif; ?>
            <li class="page-item <?= $pg['current'] >= $pg['pages'] ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= $buildUrl($pg['current'] + 1) ?>" aria-label="Siguiente">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
</div>

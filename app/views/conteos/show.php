<?php
    $isOpen = $conteo->estado === 'abierto';
    $isClosed = $conteo->estado === 'cerrado';
    $isAdjusted = $conteo->estado === 'ajustado';

    $estadoBadge = match($conteo->estado) {
        'abierto'  => 'bg-success',
        'cerrado'  => 'bg-warning text-dark',
        'ajustado' => 'bg-secondary',
        default    => 'bg-light',
    };
    $estadoLabel = match($conteo->estado) {
        'abierto'  => 'Abierto',
        'cerrado'  => 'Cerrado',
        'ajustado' => 'Ajustado',
        default    => $conteo->estado,
    };

    $pct = $summary->total > 0 ? round(($summary->contados / $summary->total) * 100) : 0;
?>

<!-- Header -->
<div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
    <div>
        <h5 class="fw-800 mb-1">
            <i class="bi bi-clipboard-data me-2"></i><?= htmlspecialchars($conteo->nombre) ?>
            <span class="badge <?= $estadoBadge ?> fs-6 ms-2"><?= $estadoLabel ?></span>
        </h5>
        <small class="text-muted">
            Creado por <?= htmlspecialchars($conteo->usuario_nombre ?? 'N/A') ?> 
            el <?= date('d/m/Y H:i', strtotime($conteo->created_at)) ?>
            · Filtro: <?= htmlspecialchars($filtroNombre) ?>
        </small>
    </div>
    <div class="d-flex gap-2">
        <?php if ($isOpen): ?>
        <form method="POST" action="<?= url("conteos/cerrar/{$conteo->id}") ?>"
              data-confirm='<?= json_encode([
                  "title" => "¿Cerrar sesión?",
                  "message" => "Una vez cerrada, no podrá modificar los conteos.<br>Podrá revisar diferencias y aplicar ajustes.",
                  "type" => "warning",
                  "confirmText" => "Sí, cerrar",
                  "icon" => "bi-lock-fill"
              ], JSON_HEX_APOS | JSON_HEX_QUOT) ?>'>
            <?= csrfField() ?>
            <button type="submit" class="btn btn-warning">
                <i class="bi bi-lock-fill me-1"></i>Cerrar Conteo
            </button>
        </form>
        <?php endif; ?>

        <?php if ($isClosed): ?>
        <form method="POST" action="<?= url("conteos/aplicar/{$conteo->id}") ?>"
              data-confirm='<?= json_encode([
                  "title" => "¿Aplicar ajustes?",
                  "message" => "Se generarán movimientos de tipo <strong>ajuste</strong> para corregir las diferencias.<br><br>Esta acción <strong>no se puede deshacer</strong>.",
                  "type" => "danger",
                  "confirmText" => "Sí, aplicar ajustes",
                  "icon" => "bi-check-circle-fill"
              ], JSON_HEX_APOS | JSON_HEX_QUOT) ?>'>
            <?= csrfField() ?>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle-fill me-1"></i>Aplicar Ajustes
            </button>
        </form>
        <?php endif; ?>

        <div class="dropdown">
            <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" 
                    aria-expanded="false" title="Exportar resultados">
                <i class="bi bi-download me-1"></i>Exportar
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="<?= url("conteos/exportar-pdf/{$conteo->id}") ?>" target="_blank">
                        <i class="bi bi-file-earmark-pdf text-danger me-2"></i>Exportar PDF
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="<?= url("conteos/exportar-csv/{$conteo->id}") ?>">
                        <i class="bi bi-file-earmark-spreadsheet text-success me-2"></i>Exportar Excel (CSV)
                    </a>
                </li>
            </ul>
        </div>

        <a href="<?= url('conteos') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-3">
    <div class="col-md-3 col-6">
        <div class="card border-0" style="background: var(--bs-tertiary-bg);">
            <div class="card-body py-3 text-center">
                <div class="fs-3 fw-800"><?= $summary->total ?></div>
                <small class="text-muted">Total Productos</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0" style="background: var(--bs-tertiary-bg);">
            <div class="card-body py-3 text-center">
                <div class="fs-3 fw-800 text-primary"><?= $summary->contados ?></div>
                <small class="text-muted">Contados</small>
                <div class="progress mt-2" style="height:4px;">
                    <div class="progress-bar <?= $pct === 100 ? 'bg-success' : 'bg-primary' ?>" style="width:<?= $pct ?>%"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0" style="background: var(--bs-tertiary-bg);">
            <div class="card-body py-3 text-center">
                <div class="fs-3 fw-800 text-success"><?= $summary->iguales ?></div>
                <small class="text-muted">Coinciden</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0" style="background: var(--bs-tertiary-bg);">
            <div class="card-body py-3 text-center">
                <div class="fs-3 fw-800 text-danger"><?= ($summary->sobrantes + $summary->faltantes) ?></div>
                <small class="text-muted">Con Diferencia</small>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-3">
    <div class="card-body py-2">
        <div class="d-flex gap-2 flex-wrap">
            <a href="<?= url("conteos/{$conteo->id}") ?>" 
               class="btn btn-sm <?= $currentFilter === 'todos' ? 'btn-primary' : 'btn-outline-secondary' ?>">
                Todos (<?= $summary->total ?>)
            </a>
            <a href="<?= url("conteos/{$conteo->id}") ?>?filter=pendientes" 
               class="btn btn-sm <?= $currentFilter === 'pendientes' ? 'btn-primary' : 'btn-outline-secondary' ?>">
                Pendientes (<?= $summary->total - $summary->contados ?>)
            </a>
            <a href="<?= url("conteos/{$conteo->id}") ?>?filter=contados" 
               class="btn btn-sm <?= $currentFilter === 'contados' ? 'btn-primary' : 'btn-outline-secondary' ?>">
                Contados (<?= $summary->contados ?>)
            </a>
            <a href="<?= url("conteos/{$conteo->id}") ?>?filter=diferencias" 
               class="btn btn-sm <?= $currentFilter === 'diferencias' ? 'btn-danger' : 'btn-outline-danger' ?>">
                <i class="bi bi-exclamation-triangle me-1"></i>Diferencias (<?= $summary->sobrantes + $summary->faltantes ?>)
            </a>
        </div>
    </div>
</div>

<!-- Items Table -->
<div class="card">
    <div class="card-body p-0">
        <?php if (empty($items)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-check-circle fs-1"></i>
                <p class="mt-2">No hay productos en este filtro.</p>
            </div>
        <?php else: ?>
        <div class="table-wrapper">
            <table class="table" id="conteoTable">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th class="text-center" style="width:100px">Stock<br>Sistema</th>
                        <th class="text-center" style="width:120px">Stock<br>Físico</th>
                        <th class="text-center" style="width:100px">Diferencia</th>
                        <th style="width:80px"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <?php
                        $hasCount = $item->stock_fisico !== null;
                        $diff = $hasCount ? ($item->stock_fisico - $item->stock_sistema) : null;
                        $rowClass = '';
                        if ($hasCount) {
                            if ($diff === 0) $rowClass = '';
                            elseif ($diff > 0) $rowClass = 'table-warning';
                            else $rowClass = 'table-danger bg-opacity-10';
                        }
                    ?>
                    <tr class="<?= $rowClass ?>" id="row-<?= $item->id ?>">
                        <td>
                            <div>
                                <strong><?= htmlspecialchars($item->producto_nombre) ?></strong>
                                <br><small class="text-muted"><?= htmlspecialchars($item->sku) ?> · <?= htmlspecialchars($item->unidad_medida ?? 'Und') ?></small>
                            </div>
                        </td>
                        <td><small class="text-muted"><?= htmlspecialchars($item->categoria_nombre ?? '—') ?></small></td>
                        <td class="text-center fw-bold"><?= $item->stock_sistema ?></td>
                        <td class="text-center">
                            <?php if ($isOpen): ?>
                            <input type="number" class="form-control form-control-sm text-center conteo-input" 
                                   data-item-id="<?= $item->id ?>"
                                   value="<?= $hasCount ? $item->stock_fisico : '' ?>"
                                   min="0" placeholder="—"
                                   style="width:90px;margin:0 auto;">
                            <?php else: ?>
                                <span class="fw-bold"><?= $hasCount ? $item->stock_fisico : '<span class="text-muted">—</span>' ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center diff-cell" id="diff-<?= $item->id ?>">
                            <?php if ($hasCount): ?>
                                <?php if ($diff === 0): ?>
                                    <span class="badge bg-success"><i class="bi bi-check-lg"></i> 0</span>
                                <?php elseif ($diff > 0): ?>
                                    <span class="badge bg-warning text-dark">+<?= $diff ?></span>
                                <?php else: ?>
                                    <span class="badge bg-danger"><?= $diff ?></span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($isOpen): ?>
                            <button class="btn btn-sm btn-outline-primary save-btn d-none" 
                                    data-item-id="<?= $item->id ?>" title="Guardar">
                                <i class="bi bi-check-lg"></i>
                            </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if ($isOpen): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const BASE = document.querySelector('meta[name="base-url"]')?.content || '/invsys/public';
    const csrfToken = '<?= $csrfToken ?>';

    // Show save button when input changes
    document.querySelectorAll('.conteo-input').forEach(input => {
        input.addEventListener('input', function() {
            const btn = document.querySelector(`.save-btn[data-item-id="${this.dataset.itemId}"]`);
            if (btn) btn.classList.remove('d-none');
        });

        // Enter = save
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                saveItem(this.dataset.itemId, this.value);
                // Move to next input
                const allInputs = [...document.querySelectorAll('.conteo-input')];
                const idx = allInputs.indexOf(this);
                if (idx < allInputs.length - 1) allInputs[idx + 1].focus();
            }
        });
    });

    // Save buttons
    document.querySelectorAll('.save-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const itemId = this.dataset.itemId;
            const input = document.querySelector(`.conteo-input[data-item-id="${itemId}"]`);
            saveItem(itemId, input.value);
        });
    });

    function saveItem(itemId, value) {
        if (value === '' || value === null) return;

        const formData = new FormData();
        formData.append('_csrf_token', csrfToken);
        formData.append('item_id', itemId);
        formData.append('stock_fisico', value);

        fetch(`${BASE}/conteos/item`, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData,
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                updateDiffCell(itemId, data.diferencia);
                const btn = document.querySelector(`.save-btn[data-item-id="${itemId}"]`);
                if (btn) btn.classList.add('d-none');

                // Quick flash effect
                const row = document.getElementById(`row-${itemId}`);
                row.style.transition = 'background 0.3s';
                row.style.background = 'rgba(25,135,84,0.1)';
                setTimeout(() => { row.style.background = ''; }, 800);
            } else {
                showToast(data.error || 'Error al guardar', 'error');
            }
        })
        .catch(() => showToast('Error de conexión', 'error'));
    }

    function updateDiffCell(itemId, diff) {
        const cell = document.getElementById(`diff-${itemId}`);
        if (!cell) return;

        if (diff === 0) {
            cell.innerHTML = '<span class="badge bg-success"><i class="bi bi-check-lg"></i> 0</span>';
        } else if (diff > 0) {
            cell.innerHTML = `<span class="badge bg-warning text-dark">+${diff}</span>`;
        } else {
            cell.innerHTML = `<span class="badge bg-danger">${diff}</span>`;
        }
    }
});
</script>
<?php endif; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($titulo ?? 'Etiquetas') ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #fff;
        }
        .label-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            justify-content: center;
        }
        .label {
            border: 1px solid #ccc;
            padding: 15px;
            text-align: center;
            border-radius: 8px;
            page-break-inside: avoid;
        }
        .label svg {
            max-width: 100%;
            height: auto;
        }
        .product-name {
            font-size: 12px;
            font-weight: bold;
            margin-top: 5px;
        }
        .product-sku {
            font-size: 10px;
            color: #555;
        }
        @media print {
            body { padding: 0; }
            .label { border: none; }
            /* Para impresoras de etiquetas (ej: Zebra) puedes usar media queries específicas */
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
</head>
<body>
    <div class="label-container">
        <?php foreach ($productos as $p): 
            $codigo = !empty($p->codigo_barras) ? $p->codigo_barras : $p->sku;
        ?>
        <div class="label">
            <svg class="barcode" data-code="<?= htmlspecialchars($codigo) ?>"></svg>
            <div class="product-name"><?= htmlspecialchars($p->nombre) ?></div>
            <div class="product-sku">SKU: <?= htmlspecialchars($p->sku) ?></div>
        </div>
        <?php endforeach; ?>
    </div>

    <script src="<?= asset('js/imprimir_masivo.js') ?>?v=<?= time() ?>"></script>
</body>
</html>

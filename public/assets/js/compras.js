document.addEventListener('DOMContentLoaded', function() {
    const btnAddRow = document.getElementById('btn-add-row');
    const tbody = document.getElementById('detalles-body');
    const emptyState = document.getElementById('empty-details-state');
    const form = document.getElementById('formOrdenCompra');
    const displayTotal = document.getElementById('total_orden_display');
    
    let productos = [];
    try {
        const prodData = document.getElementById('productos-data');
        if (prodData) {
            productos = JSON.parse(prodData.textContent);
        }
    } catch(e) {
        console.error('Error loading product data', e);
    }

    let rowCount = 0;

    function formatMoney(amount) {
        return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(amount);
    }

    function updateTotal() {
        let total = 0;
        const subtotals = document.querySelectorAll('.subtotal-value');
        subtotals.forEach(sub => {
            total += parseFloat(sub.value || 0);
        });
        displayTotal.textContent = formatMoney(total);
    }

    function addRow() {
        rowCount++;
        emptyState.style.display = 'none';

        const tr = document.createElement('tr');
        tr.id = `row-${rowCount}`;

        let options = '<option value="">— Seleccionar —</option>';
        productos.forEach(p => {
            options += `<option value="${p.id}" data-precio="${p.precio_compra || 0}">${p.sku} — ${p.nombre}</option>`;
        });

        tr.innerHTML = `
            <td class="ps-4">
                <select name="producto_id[]" class="form-select form-select-sm select-producto" required>
                    ${options}
                </select>
            </td>
            <td>
                <input type="number" name="cantidad[]" class="form-control form-control-sm input-cantidad" min="1" step="1" required placeholder="0">
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <span class="input-group-text">$</span>
                    <input type="number" name="precio_unitario[]" class="form-control input-precio" min="0" step="0.01" required placeholder="0.00">
                </div>
            </td>
            <td>
                <input type="text" class="form-control form-control-sm input-subtotal bg-light" readonly value="$0.00">
                <input type="hidden" class="subtotal-value" value="0">
            </td>
            <td class="text-end pe-4">
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;

        tbody.appendChild(tr);
        bindRowEvents(tr);
    }

    function bindRowEvents(tr) {
        const select = tr.querySelector('.select-producto');
        const inputCant = tr.querySelector('.input-cantidad');
        const inputPrecio = tr.querySelector('.input-precio');
        const inputSubtotal = tr.querySelector('.input-subtotal');
        const subtotalValue = tr.querySelector('.subtotal-value');
        const btnRemove = tr.querySelector('.btn-remove-row');

        const calculateSubtotal = () => {
            const cant = parseFloat(inputCant.value) || 0;
            const precio = parseFloat(inputPrecio.value) || 0;
            const sub = cant * precio;
            subtotalValue.value = sub.toFixed(2);
            inputSubtotal.value = formatMoney(sub);
            updateTotal();
        };

        select.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption && selectedOption.value) {
                const precioCompra = selectedOption.getAttribute('data-precio');
                if (precioCompra && inputPrecio.value === '') {
                    inputPrecio.value = parseFloat(precioCompra).toFixed(2);
                }
            }
            calculateSubtotal();
        });

        inputCant.addEventListener('input', calculateSubtotal);
        inputPrecio.addEventListener('input', calculateSubtotal);

        btnRemove.addEventListener('click', function() {
            tr.remove();
            if (tbody.children.length === 0) {
                emptyState.style.display = 'block';
            }
            updateTotal();
        });
    }

    if (btnAddRow) {
        btnAddRow.addEventListener('click', addRow);
    }

    if (form) {
        form.addEventListener('submit', function(e) {
            if (tbody.children.length === 0) {
                e.preventDefault();
                alert('Debe agregar al menos un producto a la orden de compra.');
            }
        });
    }

    // Add one row by default if the table is empty
    if (tbody && tbody.children.length === 0) {
        addRow();
    }
});

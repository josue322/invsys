/**
 * InvSys — Reportes Page Scripts
 * Handles category chart and date-filtered export logic
 */
document.addEventListener('DOMContentLoaded', function() {
    const PAGE_DATA = JSON.parse(document.getElementById('page-data')?.textContent || '{}');

    // === Gráfico de categorías ===
    const catData = PAGE_DATA.categorias || [];
    const catLabels = catData.map(c => c.categoria);
    const catValues = catData.map(c => parseInt(c.total));
    const catColors = ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#06b6d4', '#8b5cf6', '#f97316', '#14b8a6'];

    if (catLabels.length > 0) {
        new Chart(document.getElementById('chartReporteCategorias'), {
            type: 'polarArea',
            data: {
                labels: catLabels,
                datasets: [{
                    data: catValues,
                    backgroundColor: catColors.map(c => c + 'cc'),
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    // === Filtro de fecha para movimientos ===
    const modoSelect = document.getElementById('filtroFechaModo');
    const exactaGroup = document.getElementById('filtroFechaExactaGroup');
    const desdeGroup = document.getElementById('filtroFechaDesdeGroup');
    const hastaGroup = document.getElementById('filtroFechaHastaGroup');
    const inputExacta = document.getElementById('filtroFechaExacta');
    const inputDesde = document.getElementById('filtroFechaDesde');
    const inputHasta = document.getElementById('filtroFechaHasta');
    const btnCSV = document.getElementById('btnExportFechaCSV');
    const btnPDF = document.getElementById('btnExportFechaPDF');
    const baseCSV = PAGE_DATA.exportCsvUrl || '';
    const basePDF = PAGE_DATA.exportPdfUrl || '';

    modoSelect.addEventListener('change', function() {
        if (this.value === 'exacta') {
            exactaGroup.classList.remove('d-none');
            desdeGroup.classList.add('d-none');
            hastaGroup.classList.add('d-none');
        } else {
            exactaGroup.classList.add('d-none');
            desdeGroup.classList.remove('d-none');
            hastaGroup.classList.remove('d-none');
        }
    });

    function buildUrl(base) {
        const modo = modoSelect.value;
        if (modo === 'exacta') {
            const fecha = inputExacta.value;
            if (!fecha) {
                alert('Por favor, selecciona una fecha.');
                return null;
            }
            return base + '?fecha=' + fecha;
        } else {
            const desde = inputDesde.value;
            const hasta = inputHasta.value;
            if (!desde && !hasta) {
                alert('Por favor, selecciona al menos una fecha del rango.');
                return null;
            }
            let params = [];
            if (desde) params.push('fecha_desde=' + desde);
            if (hasta) params.push('fecha_hasta=' + hasta);
            return base + '?' + params.join('&');
        }
    }

    btnCSV.addEventListener('click', function() {
        const url = buildUrl(baseCSV);
        if (url) window.location.href = url;
    });

    btnPDF.addEventListener('click', function() {
        const url = buildUrl(basePDF);
        if (url) window.open(url, '_blank');
    });
});

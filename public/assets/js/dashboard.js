/**
 * InvSys — Dashboard Page Scripts
 * Handles KPI counter animations and Chart.js chart rendering
 */
document.addEventListener('DOMContentLoaded', function() {
    // ─── KPI Counter Animation ───
    document.querySelectorAll('.kpi-value').forEach(el => {
        const raw = el.textContent.trim();
        const prefixMatch = raw.match(/^[^0-9]*/);
        const prefix = prefixMatch ? prefixMatch[0] : '';
        const numPart = raw.substring(prefix.length);
        const numStr = numPart.replace(/[^0-9.]/g, '');
        const target = parseFloat(numStr);
        if (isNaN(target) || target === 0) return;

        const hasDec = numPart.includes('.');
        const duration = 1200;
        let start = null;

        el.textContent = prefix + '0';
        
        function step(ts) {
            if (!start) start = ts;
            const progress = Math.min((ts - start) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            const current = eased * target;
            
            if (hasDec) {
                const formatted = current.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                const parts = formatted.split('.');
                el.innerHTML = `<span class="kpi-prefix">${prefix}</span>${parts[0]}.<span class="kpi-decimal">${parts[1]}</span>`;
            } else {
                el.innerHTML = `<span class="kpi-prefix">${prefix}</span>${Math.floor(current).toLocaleString('en-US')}`;
            }
            if (progress < 1) requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
    });

    // ─── Load page data from JSON bridge ───
    const PAGE_DATA = JSON.parse(document.getElementById('page-data')?.textContent || '{}');

    // ─── Movimientos Chart (Bar with Gradients) ───
    const movData = PAGE_DATA.movimientos || [];
    const fechas = [...new Set(movData.map(m => m.fecha))];
    const entradas = fechas.map(f => {
        const item = movData.find(m => m.fecha === f && m.tipo === 'entrada');
        return item ? parseInt(item.cantidad_total) : 0;
    });
    const salidas = fechas.map(f => {
        const item = movData.find(m => m.fecha === f && m.tipo === 'salida');
        return item ? parseInt(item.cantidad_total) : 0;
    });
    const ajustes = fechas.map(f => {
        const item = movData.find(m => m.fecha === f && m.tipo === 'ajuste');
        return item ? parseInt(item.cantidad_total) : 0;
    });

    const fechaLabels = fechas.map(f => {
        const d = new Date(f + 'T00:00:00');
        return d.toLocaleDateString('es-MX', { day: '2-digit', month: 'short' });
    });

    const ctxMov = document.getElementById('chartMovimientos').getContext('2d');

    const gradEntrada = ctxMov.createLinearGradient(0, 0, 0, 280);
    gradEntrada.addColorStop(0, 'rgba(16, 185, 129, 0.9)');
    gradEntrada.addColorStop(1, 'rgba(16, 185, 129, 0.3)');

    const gradSalida = ctxMov.createLinearGradient(0, 0, 0, 280);
    gradSalida.addColorStop(0, 'rgba(239, 68, 68, 0.9)');
    gradSalida.addColorStop(1, 'rgba(239, 68, 68, 0.3)');

    const gradAjuste = ctxMov.createLinearGradient(0, 0, 0, 280);
    gradAjuste.addColorStop(0, 'rgba(6, 182, 212, 0.9)');
    gradAjuste.addColorStop(1, 'rgba(6, 182, 212, 0.3)');

    const premiumTooltip = {
        backgroundColor: 'rgba(15, 23, 42, 0.92)',
        titleFont: { weight: '700', size: 13, family: 'Inter' },
        bodyFont: { size: 12, family: 'Inter' },
        padding: { top: 10, right: 14, bottom: 10, left: 14 },
        cornerRadius: 10,
        displayColors: true,
        boxPadding: 4,
        caretSize: 6,
        borderColor: 'rgba(99, 102, 241, 0.2)',
        borderWidth: 1,
    };

    new Chart(ctxMov, {
        type: 'bar',
        data: {
            labels: fechaLabels.length > 0 ? fechaLabels : ['Hoy'],
            datasets: [
                {
                    label: 'Entradas',
                    data: entradas.length > 0 ? entradas : [0],
                    backgroundColor: gradEntrada,
                    hoverBackgroundColor: 'rgba(16, 185, 129, 1)',
                    borderRadius: 8,
                    borderSkipped: false,
                },
                {
                    label: 'Salidas',
                    data: salidas.length > 0 ? salidas : [0],
                    backgroundColor: gradSalida,
                    hoverBackgroundColor: 'rgba(239, 68, 68, 1)',
                    borderRadius: 8,
                    borderSkipped: false,
                },
                {
                    label: 'Ajustes',
                    data: ajustes.length > 0 ? ajustes : [0],
                    backgroundColor: gradAjuste,
                    hoverBackgroundColor: 'rgba(6, 182, 212, 1)',
                    borderRadius: 8,
                    borderSkipped: false,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 1000,
                easing: 'easeOutQuart',
                delay: (ctx) => ctx.dataIndex * 80
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        pointStyle: 'rectRounded',
                        font: { size: 12, weight: '500', family: 'Inter' }
                    }
                },
                tooltip: premiumTooltip
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false },
                    ticks: { font: { size: 11, family: 'Inter' }, padding: 8 },
                    border: { display: false }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11, family: 'Inter' }, padding: 4 },
                    border: { display: false }
                }
            }
        }
    });

    // ─── Categorías Chart (Doughnut with Hover Effect) ───
    const catData = PAGE_DATA.categorias || [];
    const catLabels = catData.map(c => c.categoria);
    const catValues = catData.map(c => parseInt(c.total));
    const catColors = ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#06b6d4', '#8b5cf6', '#f97316'];

    new Chart(document.getElementById('chartCategorias'), {
        type: 'doughnut',
        data: {
            labels: catLabels.length > 0 ? catLabels : ['Sin datos'],
            datasets: [{
                data: catValues.length > 0 ? catValues : [1],
                backgroundColor: catColors,
                borderWidth: 0,
                hoverOffset: 12,
                hoverBorderWidth: 2,
                hoverBorderColor: '#fff',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '68%',
            animation: {
                animateRotate: true,
                duration: 1200,
                easing: 'easeOutQuart'
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 16,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        font: { size: 12, weight: '500', family: 'Inter' }
                    }
                },
                tooltip: premiumTooltip
            }
        }
    });
});

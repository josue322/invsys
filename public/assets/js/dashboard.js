/**
 * InvSys — Dashboard Page Scripts
 * Handles KPI counter animations and Chart.js chart rendering
 */
document.addEventListener('DOMContentLoaded', function () {
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

    // ─── Value Trend Chart (Area with Gradient) ───
    const trendRaw = PAGE_DATA.valorTendencia || [];
    const currentValue = parseFloat(PAGE_DATA.valorActual) || 0;

    // Build daily net changes map
    const dailyChanges = {};
    trendRaw.forEach(d => {
        dailyChanges[d.fecha] = parseFloat(d.valor_entradas || 0)
                              - parseFloat(d.valor_salidas || 0)
                              + parseFloat(d.valor_ajustes || 0);
    });

    // Reconstruct last 30 days of inventory value (working backwards)
    const trendDays = [];
    const trendValues = [];
    let runningValue = currentValue;

    for (let i = 0; i < 30; i++) {
        const date = new Date();
        date.setDate(date.getDate() - i);
        const dateStr = date.toISOString().split('T')[0];
        trendDays.unshift(dateStr);
        trendValues.unshift(runningValue);
        if (dailyChanges[dateStr]) {
            runningValue -= dailyChanges[dateStr];
        }
    }

    const trendLabels = trendDays.map(f => {
        const d = new Date(f + 'T00:00:00');
        return d.toLocaleDateString('es-MX', { day: '2-digit', month: 'short' });
    });

    const ctxTrend = document.getElementById('chartValorTendencia');
    if (ctxTrend) {
        const ctx2d = ctxTrend.getContext('2d');
        const gradFill = ctx2d.createLinearGradient(0, 0, 0, 300);
        gradFill.addColorStop(0, 'rgba(99, 102, 241, 0.25)');
        gradFill.addColorStop(1, 'rgba(99, 102, 241, 0.01)');

        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: trendLabels,
                datasets: [{
                    label: 'Valor de Inventario',
                    data: trendValues,
                    borderColor: '#6366f1',
                    borderWidth: 2.5,
                    backgroundColor: gradFill,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#6366f1',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 3,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: 'index' },
                animation: { duration: 1000, easing: 'easeOutQuart' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        ...premiumTooltip,
                        callbacks: {
                            label: (ctx) => {
                                const val = ctx.parsed.y;
                                return ' ' + val.toLocaleString('es-MX', {
                                    style: 'currency', currency: 'MXN',
                                    minimumFractionDigits: 0, maximumFractionDigits: 0
                                });
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false },
                        ticks: {
                            font: { size: 11, family: 'Inter' },
                            padding: 8,
                            callback: (v) => (v / 1000).toFixed(0) + 'k'
                        },
                        border: { display: false }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 10, family: 'Inter' },
                            padding: 4,
                            maxTicksLimit: 8
                        },
                        border: { display: false }
                    }
                }
            }
        });
    }

    // ─── Categorías Chart (Doughnut with Hover Effect) ───
    const catData = PAGE_DATA.categorias || [];
    const catLabels = catData.map(c => c.categoria);
    const catValues = catData.map(c => parseInt(c.total));
    const catColors = [];
    const basePalette = ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#06b6d4', '#8b5cf6', '#f97316'];
    const numCats = catLabels.length > 0 ? catLabels.length : 1;

    for (let i = 0; i < numCats; i++) {
        if (i < basePalette.length) {
            catColors.push(basePalette[i]);
        } else {
            // Generar colores HSL armónicos dinámicamente para categorías adicionales
            // Usamos un salto de 137.5 grados (ángulo áureo) para máxima distinción visual
            const hue = (i * 137.5) % 360;
            catColors.push(`hsl(${hue}, 75%, 60%)`);
        }
    }

    // ─── Custom HTML Legend (vertical list, no grid) ───
    const getOrCreateLegendList = (id) => {
        const legendContainer = document.getElementById(id);
        if (!legendContainer) return null;
        let listContainer = legendContainer.querySelector('ul');
        if (!listContainer) {
            listContainer = document.createElement('ul');
            listContainer.style.display = 'flex';
            listContainer.style.flexDirection = 'column';
            listContainer.style.gap = '10px';
            listContainer.style.margin = '0';
            listContainer.style.padding = '0';
            listContainer.style.listStyle = 'none';
            listContainer.style.maxHeight = '180px';
            listContainer.style.overflowY = 'auto';
            listContainer.style.paddingRight = '4px';
            legendContainer.appendChild(listContainer);
        }
        return listContainer;
    };

    const htmlLegendPlugin = {
        id: 'htmlLegend',
        afterUpdate(chart, args, options) {
            const ul = getOrCreateLegendList(options.containerID);
            if (!ul) return;

            const items = chart.options.plugins.legend.labels.generateLabels(chart);

            // Anti-loop: if items already exist, just update visual state
            if (ul.childElementCount === items.length) {
                items.forEach((item, i) => {
                    const li = ul.children[i];
                    if (!li) return;
                    li.style.opacity = item.hidden ? '0.4' : '1';
                    li.style.textDecoration = item.hidden ? 'line-through' : 'none';
                });
                return;
            }

            // First render: build legend items
            while (ul.firstChild) {
                ul.firstChild.remove();
            }

            items.forEach(item => {
                const li = document.createElement('li');
                li.style.display = 'flex';
                li.style.alignItems = 'center';
                li.style.cursor = 'pointer';
                li.style.fontSize = '12px';
                li.style.fontFamily = 'Inter, sans-serif';
                li.style.color = '#475569';
                li.style.lineHeight = '1.3';
                li.style.transition = 'opacity 0.2s ease';
                li.style.opacity = item.hidden ? '0.4' : '1';
                li.style.textDecoration = item.hidden ? 'line-through' : 'none';
                li.style.padding = '2px 0';

                li.onmouseover = () => { li.style.opacity = '0.65'; };
                li.onmouseout = () => { li.style.opacity = item.hidden ? '0.4' : '1'; };

                li.onclick = () => {
                    const { type } = chart.config;
                    if (type === 'pie' || type === 'doughnut') {
                        chart.toggleDataVisibility(item.index);
                    } else {
                        chart.setDatasetVisibility(item.datasetIndex, !chart.isDatasetVisible(item.datasetIndex));
                    }
                    chart.update();
                };

                // Color dot
                const dot = document.createElement('span');
                dot.style.background = item.fillStyle;
                dot.style.display = 'inline-block';
                dot.style.width = '10px';
                dot.style.height = '10px';
                dot.style.borderRadius = '50%';
                dot.style.marginRight = '8px';
                dot.style.flexShrink = '0';
                dot.style.boxShadow = '0 1px 3px rgba(0,0,0,0.12)';

                // Category name
                const label = document.createElement('span');
                label.style.flex = '1';
                label.style.fontWeight = '500';
                label.style.overflow = 'hidden';
                label.style.textOverflow = 'ellipsis';
                label.style.whiteSpace = 'nowrap';
                label.title = item.text; // Full text on hover tooltip
                label.appendChild(document.createTextNode(item.text));

                // Value badge
                const val = document.createElement('span');
                val.style.marginLeft = '8px';
                val.style.fontSize = '11px';
                val.style.fontWeight = '600';
                val.style.color = '#6366f1';
                val.style.background = 'rgba(99, 102, 241, 0.08)';
                val.style.borderRadius = '10px';
                val.style.padding = '1px 8px';
                val.style.flexShrink = '0';
                val.textContent = catValues[item.index] ?? '';

                li.appendChild(dot);
                li.appendChild(label);
                li.appendChild(val);
                ul.appendChild(li);
            });
        }
    };

    // ─── Center Text Plugin (draws total inside the doughnut hole) ───
    const centerTextPlugin = {
        id: 'centerText',
        afterDraw(chart) {
            const { ctx, chartArea } = chart;
            if (!chartArea) return;

            const totalVisible = chart.data.datasets[0].data.reduce((sum, val, i) => {
                return chart.getDataVisibility(i) ? sum + val : sum;
            }, 0);

            const centerX = (chartArea.left + chartArea.right) / 2;
            const centerY = (chartArea.top + chartArea.bottom) / 2;

            ctx.save();
            // Number
            ctx.font = 'bold 28px Inter, sans-serif';
            ctx.fillStyle = '#1e293b';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(totalVisible, centerX, centerY - 10);
            // Label
            ctx.font = '500 11px Inter, sans-serif';
            ctx.fillStyle = '#94a3b8';
            ctx.fillText('Productos', centerX, centerY + 14);
            ctx.restore();
        }
    };

    new Chart(document.getElementById('chartCategorias'), {
        type: 'doughnut',
        data: {
            labels: catLabels.length > 0 ? catLabels : ['Sin datos'],
            datasets: [{
                data: catValues.length > 0 ? catValues : [1],
                backgroundColor: catColors,
                borderWidth: 2,
                borderColor: '#fff',
                hoverOffset: 8,
                hoverBorderWidth: 2,
                hoverBorderColor: '#fff',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '72%',
            animation: {
                animateRotate: true,
                duration: 1200,
                easing: 'easeOutQuart'
            },
            plugins: {
                legend: {
                    display: false
                },
                htmlLegend: {
                    containerID: 'chartCategoriasLegend'
                },
                tooltip: premiumTooltip
            }
        },
        plugins: [htmlLegendPlugin, centerTextPlugin]
    });
});


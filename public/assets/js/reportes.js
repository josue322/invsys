/**
 * InvSys — Reportes Page Scripts
 * Handles category advanced analysis (Bars/Radar), monthly trend with smart single-point interpolation, and date exports.
 */
document.addEventListener('DOMContentLoaded', function() {
    // Safe JSON parse from the data bridge
    let PAGE_DATA = {};
    try {
        const el = document.getElementById('page-data');
        if (el && el.textContent) {
            PAGE_DATA = JSON.parse(el.textContent);
        }
    } catch (e) {
        console.warn('InvSys: Error parsing page-data JSON', e);
    }

    // Helper: dark mode detection
    const isDark = () => document.documentElement.getAttribute('data-bs-theme') === 'dark';

    // Helper: Formatear monedas en JS
    function formatMoneyJS(amount) {
        return 'S/ ' + parseFloat(amount).toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // Helper: Formatear meses en español ("2026-05" -> "May 26")
    function formatMonthName(mesStr) {
        if (!mesStr) return '';
        const parts = mesStr.split('-');
        if (parts.length !== 2) return mesStr;
        const months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        const monthIndex = parseInt(parts[1]) - 1;
        const shortYear = parts[0].substring(2);
        return (months[monthIndex] || parts[1]) + ' ' + shortYear;
    }

    // Premium tooltip config (reusable)
    const premiumTooltip = {
        backgroundColor: 'rgba(15, 23, 42, 0.94)',
        titleFont: { weight: '700', size: 13, family: 'Inter, sans-serif' },
        bodyFont: { size: 12, family: 'Inter, sans-serif' },
        padding: { top: 10, right: 14, bottom: 10, left: 14 },
        cornerRadius: 10,
        displayColors: true,
        boxPadding: 5,
        caretSize: 6,
        borderColor: 'rgba(99, 102, 241, 0.25)',
        borderWidth: 1,
    };

    // === 1. Gráficos Analíticos por Categoría (Togglable: Barras vs Radar) ===
    const catAnalysisData = Array.isArray(PAGE_DATA.categoriaAnalisis) ? PAGE_DATA.categoriaAnalisis : [];
    
    if (catAnalysisData.length > 0) {
        const catNames = catAnalysisData.map(c => c.categoria || 'Sin Nombre');
        const catValues = catAnalysisData.map(c => parseFloat(c.valor_total) || 0);
        const catStocks = catAnalysisData.map(c => parseInt(c.stock_total) || 0);
        const catProducts = catAnalysisData.map(c => parseInt(c.total_productos) || 0);

        // Actualizar estadísticas del pie de tarjeta
        const highestValObj = [...catAnalysisData].sort((a, b) => (parseFloat(b.valor_total) || 0) - (parseFloat(a.valor_total) || 0))[0];
        const totalStockSum = catStocks.reduce((a, b) => a + b, 0);

        if (highestValObj) {
            document.getElementById('cat-major-value').textContent = highestValObj.categoria + ' (' + formatMoneyJS(highestValObj.valor_total) + ')';
        }
        document.getElementById('cat-total-stock').textContent = totalStockSum.toLocaleString('es-ES') + ' unidades';

        // 1.A. Gráfico de Barras (Valor de Inventario)
        const ctxBars = document.getElementById('chartCategoryBars');
        let chartBars = null;
        if (ctxBars) {
            const barGradient = ctxBars.getContext('2d').createLinearGradient(0, 0, 0, 240);
            barGradient.addColorStop(0, 'rgba(99, 102, 241, 0.85)');
            barGradient.addColorStop(1, 'rgba(99, 102, 241, 0.15)');

            chartBars = new Chart(ctxBars, {
                type: 'bar',
                data: {
                    labels: catNames,
                    datasets: [{
                        label: 'Valor Total',
                        data: catValues,
                        backgroundColor: barGradient,
                        borderColor: '#6366f1',
                        borderWidth: 1.5,
                        borderRadius: 6,
                        borderSkipped: false,
                        maxBarThickness: 32
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            ...premiumTooltip,
                            callbacks: {
                                label: function(context) {
                                    return ' Valor: ' + formatMoneyJS(context.raw);
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { family: 'Inter, sans-serif', size: 10 } },
                            border: { display: false }
                        },
                        y: {
                            grid: { color: 'rgba(148, 163, 184, 0.08)' },
                            ticks: {
                                font: { family: 'Inter, sans-serif', size: 10 },
                                callback: function(value) {
                                    return 'S/ ' + (value >= 1000 ? (value / 1000) + 'k' : value);
                                }
                            },
                            border: { display: false }
                        }
                    }
                }
            });
        }

        // 1.B. Gráfico de Radar (Perfil Operativo)
        const ctxRadar = document.getElementById('chartCategoryRadar');
        let chartRadar = null;
        if (ctxRadar) {
            // Normalizar valores para que se vean proporcionados en el Radar
            // Usamos una escala relativa simple para comparar volumen físico de stock vs variedad
            chartRadar = new Chart(ctxRadar, {
                type: 'radar',
                data: {
                    labels: catNames,
                    datasets: [
                        {
                            label: 'Stock Total',
                            data: catStocks,
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.12)',
                            borderWidth: 2,
                            pointBackgroundColor: '#10b981',
                            pointHoverRadius: 5
                        },
                        {
                            label: 'Variedad (SKUs)',
                            data: catProducts.map(p => p * 10), // Escalamos ligeramente para que haga contraste visual
                            borderColor: '#f59e0b',
                            backgroundColor: 'rgba(245, 158, 11, 0.08)',
                            borderWidth: 2,
                            pointBackgroundColor: '#f59e0b',
                            pointHoverRadius: 5
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 10,
                                boxHeight: 10,
                                font: { family: 'Inter, sans-serif', size: 10, weight: '600' }
                            }
                        },
                        tooltip: premiumTooltip
                    },
                    scales: {
                        r: {
                            grid: { color: 'rgba(148, 163, 184, 0.12)' },
                            angleLines: { color: 'rgba(148, 163, 184, 0.12)' },
                            ticks: { display: false },
                            pointLabels: {
                                font: { family: 'Inter, sans-serif', size: 9, weight: '600' },
                                color: isDark() ? '#94a3b8' : '#64748b'
                            }
                        }
                    }
                }
            });
        }

        // 1.C. Gestión de Toggles (Barras vs Radar)
        const btnBars = document.getElementById('btnChartBars');
        const btnRadar = document.getElementById('btnChartRadar');

        if (btnBars && btnRadar) {
            btnBars.addEventListener('click', function() {
                btnBars.className = 'btn btn-sm btn-primary active py-1 px-3';
                btnRadar.className = 'btn btn-sm btn-outline-secondary text-muted py-1 px-3';
                
                ctxBars.classList.remove('d-none');
                ctxRadar.classList.add('d-none');
                
                if (chartBars) chartBars.update();
            });

            btnRadar.addEventListener('click', function() {
                btnRadar.className = 'btn btn-sm btn-primary active py-1 px-3';
                btnBars.className = 'btn btn-sm btn-outline-secondary text-muted py-1 px-3';
                
                ctxRadar.classList.remove('d-none');
                ctxBars.classList.add('d-none');
                
                if (chartRadar) chartRadar.update();
            });
        }
    } else {
        // En caso de que no existan categorías cargadas
        const container = document.getElementById('chartCategoryContainer');
        if (container) {
            container.innerHTML = '<div class="d-flex flex-column align-items-center justify-content-center text-center py-4" style="height:240px;">' +
                '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="opacity:0.5; margin-bottom:12px;"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"/><path d="M22 12A10 10 0 0 0 12 2v10z"/></svg>' +
                '<p class="text-muted mb-0" style="font-size:0.82rem; font-family: Inter, sans-serif;">Crea productos con categorías asignadas<br>para visualizar el análisis operativo</p>' +
            '</div>';
        }
    }

    // === 2. Gráfico de Tendencia Mensual (con interpolación inteligente) ===
    const trendContainer = document.getElementById('chartReporteTendencia');
    let trendDataRaw = Array.isArray(PAGE_DATA.tendenciaMensual) ? PAGE_DATA.tendenciaMensual : [];

    // Interpolación inteligente si solo hay 1 punto de datos
    if (trendDataRaw.length === 1) {
        const singlePoint = trendDataRaw[0];
        const parts = singlePoint.mes.split('-');
        if (parts.length === 2) {
            let prevYear = parseInt(parts[0]);
            let prevMonth = parseInt(parts[1]) - 1;
            if (prevMonth === 0) {
                prevMonth = 12;
                prevYear -= 1;
            }
            const prevMonthStr = prevYear + '-' + (prevMonth < 10 ? '0' + prevMonth : prevMonth);
            // Precedemos con un mes anterior en 0 para trazar una línea hermosa
            trendDataRaw = [
                { mes: prevMonthStr, entradas: 0, salidas: 0 },
                singlePoint
            ];
        }
    }

    if (trendContainer && trendDataRaw.length > 0) {
        const trendLabels = trendDataRaw.map(t => formatMonthName(t.mes));
        const trendEntradas = trendDataRaw.map(t => parseInt(t.entradas) || 0);
        const trendSalidas = trendDataRaw.map(t => parseInt(t.salidas) || 0);

        const ctx2d = trendContainer.getContext('2d');

        // Gradient fills for area under the lines
        const gradEntrada = ctx2d.createLinearGradient(0, 0, 0, 240);
        gradEntrada.addColorStop(0, 'rgba(16, 185, 129, 0.18)');
        gradEntrada.addColorStop(1, 'rgba(16, 185, 129, 0.01)');

        const gradSalida = ctx2d.createLinearGradient(0, 0, 0, 240);
        gradSalida.addColorStop(0, 'rgba(239, 68, 68, 0.15)');
        gradSalida.addColorStop(1, 'rgba(239, 68, 68, 0.01)');

        new Chart(trendContainer, {
            type: 'line',
            data: {
                labels: trendLabels,
                datasets: [
                    {
                        label: 'Entradas',
                        data: trendEntradas,
                        borderColor: '#10b981',
                        backgroundColor: gradEntrada,
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: isDark() ? '#1e293b' : '#fff',
                        pointBorderWidth: 2,
                        pointHoverRadius: 7,
                        pointHoverBorderWidth: 3,
                        pointRadius: 4.5
                    },
                    {
                        label: 'Salidas',
                        data: trendSalidas,
                        borderColor: '#ef4444',
                        backgroundColor: gradSalida,
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#ef4444',
                        pointBorderColor: isDark() ? '#1e293b' : '#fff',
                        pointBorderWidth: 2,
                        pointHoverRadius: 7,
                        pointHoverBorderWidth: 3,
                        pointRadius: 4.5
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: 'index' },
                animation: { duration: 1100, easing: 'easeOutQuart' },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            boxWidth: 8,
                            boxHeight: 8,
                            padding: 20,
                            font: { family: 'Inter, sans-serif', size: 11, weight: '600' }
                        }
                    },
                    tooltip: premiumTooltip
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { family: 'Inter, sans-serif', size: 10.5 },
                            padding: 4
                        },
                        border: { display: false }
                    },
                    y: {
                        grid: { color: 'rgba(148, 163, 184, 0.08)' },
                        ticks: {
                            font: { family: 'Inter, sans-serif', size: 10.5 },
                            padding: 8
                        },
                        beginAtZero: true,
                        border: { display: false }
                    }
                }
            }
        });
    } else if (trendContainer) {
        // Show empty state message when no trend data
        const parent = trendContainer.parentElement;
        parent.innerHTML = '<div class="d-flex flex-column align-items-center justify-content-center text-center py-4" style="height:260px;">' +
            '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="opacity:0.5; margin-bottom:12px;"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>' +
            '<p class="text-muted mb-0" style="font-size:0.82rem; font-family: Inter, sans-serif;">No hay suficientes datos de movimientos<br>para generar la tendencia mensual</p>' +
        '</div>';
    }

    // === 3. Filtro de Fecha para Movimientos ===
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

    if (modoSelect) {
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
    }

    function buildUrl(base) {
        var modo = modoSelect.value;
        if (modo === 'exacta') {
            var fecha = inputExacta.value;
            if (!fecha) {
                alert('Por favor, selecciona una fecha.');
                return null;
            }
            return base + '?fecha=' + fecha;
        } else {
            var desde = inputDesde.value;
            var hasta = inputHasta.value;
            if (!desde && !hasta) {
                alert('Por favor, selecciona al menos una fecha del rango.');
                return null;
            }
            var params = [];
            if (desde) params.push('fecha_desde=' + desde);
            if (hasta) params.push('fecha_hasta=' + hasta);
            return base + '?' + params.join('&');
        }
    }

    if (btnCSV) {
        btnCSV.addEventListener('click', function() {
            var url = buildUrl(baseCSV);
            if (url) window.location.href = url;
        });
    }

    if (btnPDF) {
        btnPDF.addEventListener('click', function() {
            var url = buildUrl(basePDF);
            if (url) window.open(url, '_blank');
        });
    }

    // === 4. Gráfico ABC — Distribución por Valor (Doughnut Premium) ===
    const ctxABC = document.getElementById('chartABC');
    if (ctxABC && PAGE_DATA.totals) {
        const totals = PAGE_DATA.totals;
        const valA = parseFloat(totals.A) || 0;
        const valB = parseFloat(totals.B) || 0;
        const valC = parseFloat(totals.C) || 0;
        const grandTotal = valA + valB + valC;

        // Only render if there is actual data
        if (grandTotal > 0) {
            const abcColors = ['#ef4444', '#f59e0b', '#06b6d4'];
            const abcHoverColors = ['#dc2626', '#d97706', '#0891b2'];
            const borderCol = isDark() ? '#1e293b' : '#ffffff';

            new Chart(ctxABC, {
                type: 'doughnut',
                data: {
                    labels: ['Clase A (Alto Valor)', 'Clase B (Valor Medio)', 'Clase C (Bajo Valor)'],
                    datasets: [{
                        data: [valA, valB, valC],
                        backgroundColor: abcColors,
                        hoverBackgroundColor: abcHoverColors,
                        borderColor: borderCol,
                        borderWidth: 2.5,
                        hoverBorderColor: borderCol,
                        borderRadius: 4,
                        spacing: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '62%',
                    animation: {
                        animateRotate: true,
                        animateScale: true,
                        duration: 1000,
                        easing: 'easeOutQuart'
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                boxWidth: 9,
                                boxHeight: 9,
                                padding: 16,
                                font: { family: 'Inter, sans-serif', size: 11, weight: '600' },
                                color: isDark() ? '#cbd5e1' : '#475569',
                                generateLabels: function(chart) {
                                    const dataset = chart.data.datasets[0];
                                    return chart.data.labels.map(function(label, i) {
                                        const value = dataset.data[i];
                                        const pct = grandTotal > 0 ? ((value / grandTotal) * 100).toFixed(1) : '0.0';
                                        return {
                                            text: label + '  (' + pct + '%)',
                                            fillStyle: dataset.backgroundColor[i],
                                            strokeStyle: dataset.borderColor,
                                            lineWidth: 0,
                                            hidden: false,
                                            index: i,
                                            pointStyle: 'circle'
                                        };
                                    });
                                }
                            }
                        },
                        tooltip: {
                            ...premiumTooltip,
                            callbacks: {
                                label: function(context) {
                                    const value = context.raw;
                                    const pct = grandTotal > 0 ? ((value / grandTotal) * 100).toFixed(1) : '0.0';
                                    return ' ' + context.label + ': ' + formatMoneyJS(value) + ' (' + pct + '%)';
                                }
                            }
                        }
                    }
                },
                plugins: [{
                    // Center text plugin: shows total value in the middle of the doughnut
                    id: 'abcCenterText',
                    afterDraw: function(chart) {
                        var ctx = chart.ctx;
                        var width = chart.width;
                        var height = chart.chartArea.top + chart.chartArea.bottom;
                        var centerX = width / 2;
                        var centerY = (chart.chartArea.top + chart.chartArea.bottom) / 2;

                        ctx.save();
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';

                        // Main value
                        ctx.font = '700 15px Inter, sans-serif';
                        ctx.fillStyle = isDark() ? '#e2e8f0' : '#1e293b';
                        ctx.fillText(formatMoneyJS(grandTotal), centerX, centerY - 8);

                        // Subtitle
                        ctx.font = '500 10px Inter, sans-serif';
                        ctx.fillStyle = isDark() ? '#94a3b8' : '#64748b';
                        ctx.fillText('Valor Total', centerX, centerY + 10);

                        ctx.restore();
                    }
                }]
            });
        } else {
            // Empty state when all values are zero
            var abcParent = ctxABC.parentElement;
            abcParent.innerHTML = '<div class="d-flex flex-column align-items-center justify-content-center text-center py-4" style="height:280px;">' +
                '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="opacity:0.5; margin-bottom:12px;"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"/><path d="M22 12A10 10 0 0 0 12 2v10z"/></svg>' +
                '<p class="text-muted mb-0" style="font-size:0.82rem; font-family: Inter, sans-serif;">No hay productos con stock<br>para generar la distribución ABC</p>' +
            '</div>';
        }
    }
});

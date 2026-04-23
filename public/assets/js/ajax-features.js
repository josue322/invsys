/**
 * InvSys - AJAX Features
 * 
 * 1. Live Search (autocomplete para productos)
 * 2. Alert Polling (actualización automática del badge de alertas)
 */

document.addEventListener('DOMContentLoaded', function () {

    const BASE = document.querySelector('meta[name="base-url"]')?.content || '/invsys/public';

    // =========================================================
    // 1. LIVE SEARCH — Búsqueda de productos en tiempo real
    // =========================================================
    const searchBox = document.getElementById('global-search');
    const searchResults = document.getElementById('search-results');

    if (searchBox && searchResults) {
        let debounceTimer = null;

        searchBox.addEventListener('input', function () {
            const query = this.value.trim();
            clearTimeout(debounceTimer);

            if (query.length < 2) {
                searchResults.classList.remove('show');
                searchResults.innerHTML = '';
                return;
            }

            debounceTimer = setTimeout(() => {
                fetch(`${BASE}/productos/buscar?q=${encodeURIComponent(query)}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.length === 0) {
                        searchResults.innerHTML = `
                            <div class="search-empty">
                                <i class="bi bi-search"></i>
                                <span>Sin resultados para "${query}"</span>
                            </div>`;
                        searchResults.classList.add('show');
                        return;
                    }

                    searchResults.innerHTML = data.map(p => `
                        <a href="${p.url}" class="search-result-item">
                            <img src="${p.imagen}" alt="" class="search-thumb">
                            <div class="search-info">
                                <strong>${p.nombre}</strong>
                                <small>${p.sku} · ${p.categoria}</small>
                            </div>
                            <div class="search-meta">
                                <span class="search-price">${p.precio}</span>
                                <span class="search-stock ${p.stock <= 0 ? 'text-danger' : ''}">${p.stock} uds</span>
                            </div>
                        </a>
                    `).join('');
                    searchResults.classList.add('show');
                })
                .catch(() => {
                    searchResults.classList.remove('show');
                });
            }, 300);
        });

        // Cerrar resultados al hacer clic fuera
        document.addEventListener('click', function (e) {
            if (!searchBox.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.classList.remove('show');
            }
        });

        // Cerrar con Escape
        searchBox.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                searchResults.classList.remove('show');
                searchBox.blur();
            }
        });

        // Ctrl+K to focus search
        document.addEventListener('keydown', function (e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                searchBox.focus();
            }
        });
    }

    // =========================================================
    // 2. ALERT POLLING — Actualización automática del badge
    // =========================================================
    const alertBadge = document.querySelector('.notification-badge');
    const alertDropdown = document.getElementById('alertDropdown');

    function updateAlertBadge() {
        fetch(`${BASE}/alertas/count`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            const count = data.count || 0;

            // Actualizar badge del navbar
            if (alertDropdown) {
                let badge = alertDropdown.querySelector('.notification-badge');
                if (count > 0) {
                    if (!badge) {
                        badge = document.createElement('span');
                        badge.className = 'notification-badge';
                        alertDropdown.appendChild(badge);
                    }
                    badge.textContent = count > 99 ? '99+' : count;
                } else if (badge) {
                    badge.remove();
                }
            }

            // Actualizar badge del sidebar 
            const sidebarBadge = document.querySelector('#nav-alertas .badge');
            if (sidebarBadge) {
                if (count > 0) {
                    sidebarBadge.textContent = count;
                    sidebarBadge.style.display = '';
                } else {
                    sidebarBadge.style.display = 'none';
                }
            }
        })
        .catch(() => {});
    }

    // Polling cada 30 segundos
    if (alertDropdown) {
        setInterval(updateAlertBadge, 30000);
    }
});

/**
 * InvSys - App Core JavaScript
 * 
 * Sidebar toggle, theme toggle, toast notification system,
 * flash messages, and global tooltip initialization.
 */

document.addEventListener('DOMContentLoaded', function() {
    // ─── Sidebar Toggle ───
    const sidebar = document.getElementById('sidebar');
    const sidebarOpen = document.getElementById('sidebarOpen');
    const sidebarClose = document.getElementById('sidebarClose');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    if (sidebarOpen) {
        sidebarOpen.addEventListener('click', () => {
            sidebar.classList.add('show');
            sidebarOverlay.classList.add('show');
        });
    }

    if (sidebarClose) {
        sidebarClose.addEventListener('click', () => {
            sidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
        });
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', () => {
            sidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
        });
    }

    // ─── Sidebar Scroll Retention ───
    const sidebarScrollContainer = document.querySelector('.sidebar');
    if (sidebarScrollContainer) {
        // Restore scroll position
        const savedScroll = sessionStorage.getItem('sidebarScrollPos');
        if (savedScroll !== null) {
            sidebarScrollContainer.scrollTop = parseInt(savedScroll, 10);
        } else {
            // Auto-scroll to active item if no saved position
            const activeItem = sidebarScrollContainer.querySelector('.nav-link.active');
            if (activeItem) {
                // Ensure the active item is visible in the scrollable area
                const navRect = sidebarScrollContainer.getBoundingClientRect();
                const itemRect = activeItem.getBoundingClientRect();
                if (itemRect.top < navRect.top || itemRect.bottom > navRect.bottom) {
                    activeItem.scrollIntoView({ behavior: 'auto', block: 'center' });
                }
            }
        }

        // Save scroll position before leaving page
        window.addEventListener('beforeunload', () => {
            sessionStorage.setItem('sidebarScrollPos', sidebarScrollContainer.scrollTop);
        });
    }

    // ─── Theme Toggle ───
    const themeToggle = document.getElementById('themeToggle');
    if (themeToggle) {
        const baseUrl = document.querySelector('meta[name="base-url"]')?.content || '';
        themeToggle.addEventListener('click', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
            fetch(baseUrl + '/tema/toggle', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.documentElement.setAttribute('data-bs-theme', data.theme);
                    const icon = themeToggle.querySelector('i');
                    const text = themeToggle.querySelector('span');
                    if (data.theme === 'dark') {
                        icon.className = 'bi bi-sun-fill';
                        text.textContent = 'Modo Claro';
                    } else {
                        icon.className = 'bi bi-moon-fill';
                        text.textContent = 'Modo Oscuro';
                    }
                }
            })
            .catch(err => console.error('Error al cambiar tema:', err));
        });
    }

    // ─── Toast Notification System ───
    window.showToast = function(message, type = 'success', title = null) {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const defaultTitles = {
            success: '¡Éxito!',
            error: 'Error',
            warning: 'Advertencia',
            info: 'Información'
        };

        const icons = {
            success: 'bi-check-lg',
            error: 'bi-exclamation-triangle-fill',
            warning: 'bi-exclamation-circle-fill',
            info: 'bi-info-circle-fill'
        };

        const toast = document.createElement('div');
        toast.className = `toast-notification toast-${type}`;
        
        toast.innerHTML = `
            <div class="toast-icon">
                <i class="bi ${icons[type]}"></i>
            </div>
            <div class="toast-body">
                <div class="toast-title">${title || defaultTitles[type]}</div>
                <div class="toast-message">${message}</div>
            </div>
            <button class="toast-close" title="Cerrar"><i class="bi bi-x"></i></button>
            <div class="toast-progress" style="animation-duration: 5s"></div>
        `;

        container.appendChild(toast);

        // Remove functionality
        const removeToast = () => {
            if (toast.classList.contains('toast-exiting')) return;
            toast.classList.add('toast-exiting');
            setTimeout(() => toast.remove(), 300);
        };

        toast.querySelector('.toast-close').addEventListener('click', removeToast);
        const autoDismiss = setTimeout(removeToast, 5000);

        // Pause on hover
        toast.addEventListener('mouseenter', () => {
            clearTimeout(autoDismiss);
            toast.querySelector('.toast-progress').style.animationPlayState = 'paused';
        });

        // Resume on mouse leave
        toast.addEventListener('mouseleave', () => {
            toast.querySelector('.toast-progress').style.animationPlayState = 'running';
            setTimeout(removeToast, 2000);
        });
    };

    // ─── Flash Messages ───
    const contentWrapper = document.querySelector('.content-wrapper');
    if (contentWrapper) {
        const flashType = contentWrapper.getAttribute('data-flash-type');
        const flashMessage = contentWrapper.getAttribute('data-flash-message');
        if (flashType && flashMessage) {
            showToast(flashMessage, flashType);
            contentWrapper.removeAttribute('data-flash-type');
            contentWrapper.removeAttribute('data-flash-message');
        }
    }

    // ─── Bootstrap Tooltips ───
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // ─── Global Print Handler ───
    const printButtons = document.querySelectorAll('.btn-print, #btnPrint');
    printButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            window.print();
        });
    });

    // ─── Global Action Handlers (CSP Compliant) ───
    document.addEventListener('click', function(e) {
        // Native Confirm
        // Generic History Back
        if (e.target.closest('.btn-history-back')) {
            e.preventDefault();
            history.back();
        }

        // Generic Reload
        if (e.target.closest('.btn-reload')) {
            e.preventDefault();
            location.reload();
        }
    });

    // (Removido manejador de data-native-confirm en favor de confirm-modal.js)
    // ─── Pagination Per-Page Selector ───
    const perPageSelectors = document.querySelectorAll('.per-page-selector');
    perPageSelectors.forEach(selector => {
        selector.addEventListener('change', function() {
            window.location.href = this.value;
        });
    });

    // ─── Auto-Submit Select (CSP-compliant replacement for onchange="this.form.submit()") ───
    const autoSubmitSelects = document.querySelectorAll('.auto-submit-select');
    autoSubmitSelects.forEach(select => {
        select.addEventListener('change', function() {
            const form = this.closest('form');
            if (form) form.submit();
        });
    });
});

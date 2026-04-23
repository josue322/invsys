    </div><!-- /.content-wrapper -->
</main><!-- /.main-content -->
</div><!-- /#app-wrapper -->

<!-- Sidebar overlay for mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Form Validator -->
<script src="<?= asset('js/form-validator.js') ?>?v=<?= ASSET_VERSION ?>"></script>

<!-- Custom Confirm Modal -->
<script src="<?= asset('js/confirm-modal.js') ?>?v=<?= ASSET_VERSION ?>"></script>

<!-- AJAX Features (search + alert polling) -->
<script src="<?= asset('js/ajax-features.js') ?>?v=<?= ASSET_VERSION ?>"></script>

<!-- Theme Toggle Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sidebar toggle
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

    // Theme toggle
    const themeToggle = document.getElementById('themeToggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            fetch('<?= url('tema/toggle') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
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
            setTimeout(() => toast.remove(), 300); // Wait for exit animation
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
            setTimeout(removeToast, 2000); // Give 2 seconds after mouse leave
        });
    };

    // Check for flash messages
    const contentWrapper = document.querySelector('.content-wrapper');
    if (contentWrapper) {
        const flashType = contentWrapper.getAttribute('data-flash-type');
        const flashMessage = contentWrapper.getAttribute('data-flash-message');
        if (flashType && flashMessage) {
            showToast(flashMessage, flashType);
            // Clean up attributes
            contentWrapper.removeAttribute('data-flash-type');
            contentWrapper.removeAttribute('data-flash-message');
        }
    }
    // Initialize tooltips globally
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

</body>
</html>

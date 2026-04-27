/**
 * InvSys - Custom Confirmation Modal
 * Replaces native browser confirm() dialogs with a premium,
 * themed modal that matches the application design system.
 */
const ConfirmModal = (() => {
    let modalEl = null;
    let pendingForm = null;
    let pendingCallback = null;

    /**
     * Build the modal DOM (once, lazily)
     */
    function ensureModal() {
        if (modalEl) return;

        modalEl = document.createElement('div');
        modalEl.id = 'confirmModal';
        modalEl.className = 'confirm-modal-backdrop';
        modalEl.innerHTML = `
            <div class="confirm-modal-dialog">
                <div class="confirm-modal-icon" id="confirmModalIcon">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <h5 class="confirm-modal-title" id="confirmModalTitle">¿Estás seguro?</h5>
                <p class="confirm-modal-message" id="confirmModalMessage">Esta acción no se puede deshacer.</p>
                <div class="confirm-modal-actions">
                    <button type="button" class="confirm-modal-btn confirm-modal-btn-cancel" id="confirmModalCancel">
                        Cancelar
                    </button>
                    <button type="button" class="confirm-modal-btn confirm-modal-btn-confirm" id="confirmModalConfirm">
                        <i class="bi bi-check-lg me-1"></i>Confirmar
                    </button>
                </div>
            </div>
        `;
        document.body.appendChild(modalEl);

        // Event listeners
        document.getElementById('confirmModalCancel').addEventListener('click', closeModal);
        document.getElementById('confirmModalConfirm').addEventListener('click', confirmAction);

        // Close on backdrop click
        modalEl.addEventListener('click', (e) => {
            if (e.target === modalEl) closeModal();
        });

        // Close on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && modalEl.classList.contains('show')) {
                closeModal();
            }
        });
    }

    /**
     * Open the modal with custom options
     * @param {Object} options
     * @param {string} options.title - Modal title
     * @param {string} options.message - Modal description
     * @param {string} options.confirmText - Text for the confirm button
     * @param {string} options.cancelText - Text for the cancel button
     * @param {string} options.type - 'danger' | 'warning' | 'info' (icon/color scheme)
     * @param {string} options.icon - Bootstrap icon class (e.g. 'bi-trash-fill')
     * @param {HTMLFormElement|Function} formOrCallback - The form to submit on confirm, or a callback function
     */
    function open(options, formOrCallback) {
        ensureModal();

        const title = options.title || '¿Estás seguro?';
        const message = options.message || 'Esta acción no se puede deshacer.';
        const confirmText = options.confirmText || 'Confirmar';
        const cancelText = options.cancelText || 'Cancelar';
        const type = options.type || 'danger';
        const icon = options.icon || getDefaultIcon(type);

        // Set content
        document.getElementById('confirmModalTitle').textContent = title;
        document.getElementById('confirmModalMessage').innerHTML = message;

        const confirmBtn = document.getElementById('confirmModalConfirm');
        confirmBtn.innerHTML = `<i class="bi ${getConfirmIcon(type)} me-1"></i>${confirmText}`;

        document.getElementById('confirmModalCancel').textContent = cancelText;

        // Set icon
        const iconEl = document.getElementById('confirmModalIcon');
        iconEl.innerHTML = `<i class="bi ${icon}"></i>`;

        // Set type class for color theming
        const dialog = modalEl.querySelector('.confirm-modal-dialog');
        dialog.className = 'confirm-modal-dialog';
        dialog.classList.add(`confirm-modal-${type}`);

        // Set confirm button type class
        confirmBtn.className = 'confirm-modal-btn confirm-modal-btn-confirm';
        confirmBtn.classList.add(`confirm-modal-btn-${type}`);

        // Support both form and callback
        if (typeof formOrCallback === 'function') {
            pendingForm = null;
            pendingCallback = formOrCallback;
        } else {
            pendingForm = formOrCallback || null;
            pendingCallback = null;
        }

        // Show with animation
        modalEl.classList.add('show');
        document.body.style.overflow = 'hidden';

        // Focus the cancel button for safety
        setTimeout(() => {
            document.getElementById('confirmModalCancel').focus();
        }, 100);
    }

    function getDefaultIcon(type) {
        switch (type) {
            case 'danger': return 'bi-exclamation-triangle-fill';
            case 'warning': return 'bi-exclamation-circle-fill';
            case 'info': return 'bi-info-circle-fill';
            default: return 'bi-exclamation-triangle-fill';
        }
    }

    function getConfirmIcon(type) {
        switch (type) {
            case 'danger': return 'bi-trash-fill';
            case 'warning': return 'bi-check-lg';
            case 'info': return 'bi-check-lg';
            default: return 'bi-check-lg';
        }
    }

    function closeModal() {
        if (!modalEl) return;
        modalEl.classList.remove('show');
        document.body.style.overflow = '';
        pendingForm = null;
        pendingCallback = null;
    }

    function confirmAction() {
        if (pendingCallback) {
            pendingCallback();
        } else if (pendingForm) {
            // Remove the data-confirm attribute temporarily so onsubmit doesn't re-trigger
            const savedAttr = pendingForm.getAttribute('data-confirm');
            pendingForm.removeAttribute('data-confirm');
            pendingForm.requestSubmit();
            if (savedAttr) {
                pendingForm.setAttribute('data-confirm', savedAttr);
            }
        }
        closeModal();
    }

    /**
     * Auto-bind all forms with [data-confirm] attribute.
     * Expected data attributes on the <form> element:
     *   data-confirm        (required – JSON-encoded options, or just "true" for defaults)
     *   e.g. data-confirm='{"title":"¿Eliminar?","message":"No se podrá recuperar","type":"danger"}'
     */
    function bindAll() {
        document.querySelectorAll('form[data-confirm]').forEach(form => {
            // Guard: don't double bind
            if (form._confirmBound) return;
            form._confirmBound = true;

            form.addEventListener('submit', function (e) {
                // If form still has the attribute, intercept
                if (!this.hasAttribute('data-confirm')) return;

                e.preventDefault();

                let options = {};
                try {
                    const raw = this.getAttribute('data-confirm');
                    if (raw && raw !== 'true') {
                        options = JSON.parse(raw);
                    }
                } catch (err) {
                    // If parsing fails, use as message
                    options = { message: this.getAttribute('data-confirm') };
                }

                open(options, this);
            });
        });
    }

    // Auto-init on DOM ready
    document.addEventListener('DOMContentLoaded', bindAll);

    // Public API
    return { open, close: closeModal, bindAll };
})();

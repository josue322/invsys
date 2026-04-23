/**
 * =====================================================
 * InvSys — FormValidator
 * Validación front-end centralizada y reutilizable
 * =====================================================
 * Uso:
 *   FormValidator.init('#formId', {
 *       campo: { required: true, email: true, minlength: 3, ... },
 *       ...
 *   });
 *
 * Soporta: required, email, minlength, maxlength, min, max,
 *          pattern, match, filesize, filetype, custom
 */
const FormValidator = (() => {
    'use strict';

    // ─── Mensajes por defecto ───
    const MESSAGES = {
        required: 'Este campo es obligatorio',
        email: 'Ingrese un correo electrónico válido',
        minlength: 'Debe tener al menos :min caracteres',
        maxlength: 'No puede exceder :max caracteres',
        min: 'El valor mínimo es :min',
        max: 'El valor máximo es :max',
        pattern: 'El formato no es válido',
        match: 'Los campos no coinciden',
        filesize: 'El archivo excede el tamaño máximo de :max',
        filetype: 'Tipo de archivo no permitido',
        custom: 'Este campo no es válido'
    };

    // ─── Utilidades ───
    function getField(form, name) {
        return form.querySelector(`[name="${name}"]`);
    }

    function getWrapper(field) {
        // Subir hasta .mb-3, .col-*, o parent directo
        let el = field.closest('.mb-3') || field.closest('[class*="col-"]') || field.parentElement;
        return el;
    }

    function getInputEl(field) {
        // Si está dentro de input-group, devolver el propio input
        return field;
    }

    function clearFeedback(field) {
        field.classList.remove('is-invalid', 'is-valid');
        const wrapper = getWrapper(field);
        wrapper.querySelectorAll('.fv-feedback').forEach(el => el.remove());
    }

    function showError(field, message) {
        clearFeedback(field);
        field.classList.add('is-invalid');
        // Shake animation
        field.classList.add('fv-shake');
        setTimeout(() => field.classList.remove('fv-shake'), 400);

        const wrapper = getWrapper(field);
        // Insert after field or after input-group
        const anchor = field.closest('.input-group') || field;
        const feedback = document.createElement('div');
        feedback.className = 'fv-feedback invalid-feedback d-block';
        feedback.innerHTML = `<i class="bi bi-exclamation-circle me-1"></i>${message}`;
        anchor.insertAdjacentElement('afterend', feedback);
    }

    function showValid(field) {
        clearFeedback(field);
        field.classList.add('is-valid');
    }

    function formatMsg(template, replacements) {
        let msg = template;
        for (const [key, val] of Object.entries(replacements)) {
            msg = msg.replace(`:${key}`, val);
        }
        return msg;
    }

    // ─── Validadores individuales ───
    const validators = {
        required(value, _param, field) {
            // Para selects, value vacío es inválido
            if (field.type === 'file') return field.files && field.files.length > 0;
            if (field.tagName === 'SELECT') return value !== '';
            return value.trim() !== '';
        },

        email(value) {
            if (!value) return true; // skip if empty (use required for that)
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
        },

        minlength(value, min) {
            if (!value) return true;
            return value.length >= parseInt(min);
        },

        maxlength(value, max) {
            if (!value) return true;
            return value.length <= parseInt(max);
        },

        min(value, minVal) {
            if (value === '' || value === null) return true;
            return parseFloat(value) >= parseFloat(minVal);
        },

        max(value, maxVal) {
            if (value === '' || value === null) return true;
            return parseFloat(value) <= parseFloat(maxVal);
        },

        pattern(value, regex) {
            if (!value) return true;
            const re = typeof regex === 'string' ? new RegExp(regex) : regex;
            return re.test(value);
        },

        match(value, targetName, field) {
            if (!value) return true;
            // Try by name first, then as CSS selector
            const targetField = field.form.querySelector(`[name="${targetName}"]`)
                || field.form.querySelector(targetName);
            return targetField && value === targetField.value;
        },

        filesize(value, maxBytes, field) {
            if (!field.files || field.files.length === 0) return true;
            return field.files[0].size <= parseInt(maxBytes);
        },

        filetype(value, types, field) {
            if (!field.files || field.files.length === 0) return true;
            const allowed = Array.isArray(types) ? types : types.split(',').map(t => t.trim());
            const file = field.files[0];
            return allowed.some(t => file.type === t || file.name.toLowerCase().endsWith(t));
        }
    };

    // ─── Validar un campo ───
    function validateField(form, fieldName, rules, showFeedback = true) {
        const field = getField(form, fieldName);
        if (!field) return true;

        // Skip disabled or hidden fields
        if (field.disabled || field.offsetParent === null) {
            clearFeedback(field);
            return true;
        }

        const value = field.value;

        for (const [rule, param] of Object.entries(rules)) {
            // Custom callback
            if (rule === 'custom' && typeof param === 'function') {
                const result = param(value, field);
                if (result !== true) {
                    if (showFeedback) showError(field, result || MESSAGES.custom);
                    return false;
                }
                continue;
            }

            // Custom message override
            if (rule === 'message' || rule === 'messages') continue;

            if (validators[rule]) {
                const isValid = validators[rule](value, param, field);
                if (!isValid) {
                    // Check for custom messages
                    const customMsgs = rules.messages || {};
                    let msg = customMsgs[rule] || MESSAGES[rule] || MESSAGES.custom;
                    msg = formatMsg(msg, { min: param, max: param });

                    // For filesize, format bytes nicely
                    if (rule === 'filesize') {
                        const mb = (parseInt(param) / (1024 * 1024)).toFixed(0);
                        msg = formatMsg(msg, { max: mb + 'MB' });
                    }

                    if (showFeedback) showError(field, msg);
                    return false;
                }
            }
        }

        if (showFeedback) showValid(field);
        return true;
    }

    // ─── Init: registrar formulario ───
    function init(formSelector, fieldRules) {
        const form = document.querySelector(formSelector);
        if (!form) return;

        const allRules = { ...fieldRules };

        // ── Eventos blur + input por campo ──
        for (const [fieldName, rules] of Object.entries(allRules)) {
            const field = getField(form, fieldName);
            if (!field) continue;

            let touched = false;

            // blur → validar al salir del campo
            field.addEventListener('blur', () => {
                touched = true;
                validateField(form, fieldName, rules);
            });

            // input → revalidar en tiempo real solo si ya se tocó
            field.addEventListener('input', () => {
                if (touched) {
                    validateField(form, fieldName, rules);
                }
            });

            // change → para selects y files
            field.addEventListener('change', () => {
                if (touched) {
                    validateField(form, fieldName, rules);
                }
            });
        }

        // ── Submit → validar todo ──
        form.addEventListener('submit', (e) => {
            let formValid = true;
            let firstInvalid = null;

            for (const [fieldName, rules] of Object.entries(allRules)) {
                const isValid = validateField(form, fieldName, rules);
                if (!isValid && formValid) {
                    formValid = false;
                    firstInvalid = getField(form, fieldName);
                }
            }

            if (!formValid) {
                e.preventDefault();
                e.stopPropagation();
                if (firstInvalid) {
                    firstInvalid.focus();
                    // Scroll suave al primer error
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            } else {
                // ─── Button Loading State ───
                const submitBtn = form.querySelector('[type="submit"], .btn-login');
                if (submitBtn && !submitBtn.classList.contains('btn-loading')) {
                    submitBtn.classList.add('btn-loading');
                    submitBtn.disabled = true;
                    // Safety reset after 8s (in case page doesn't navigate)
                    setTimeout(() => {
                        submitBtn.classList.remove('btn-loading');
                        submitBtn.disabled = false;
                    }, 8000);
                }
            }
        });
    }

    // ─── Password strength helper ───
    function passwordStrength(inputSelector, containerSelector) {
        const input = document.querySelector(inputSelector);
        const container = document.querySelector(containerSelector);
        if (!input || !container) return;

        input.addEventListener('input', function () {
            const val = this.value;
            let strength = 0;
            if (val.length >= 8) strength++;
            if (/[A-Z]/.test(val)) strength++;
            if (/[0-9]/.test(val)) strength++;
            if (/[^A-Za-z0-9]/.test(val)) strength++;

            const labels = ['', 'Débil', 'Regular', 'Buena', 'Fuerte'];
            const colors = ['', '#ef4444', '#f59e0b', '#06b6d4', '#10b981'];

            if (val.length > 0) {
                container.innerHTML = `
                    <div class="password-strength-bar">
                        <div class="password-strength-segments">
                            ${[1, 2, 3, 4].map(i => `
                                <div class="strength-segment ${i <= strength ? 'active' : ''}"
                                     style="${i <= strength ? 'background:' + colors[strength] : ''}"></div>
                            `).join('')}
                        </div>
                        <small class="password-strength-label" style="color:${colors[strength]}">${labels[strength]}</small>
                    </div>`;
            } else {
                container.innerHTML = '';
            }
        });
    }

    // ─── Public API ───
    return {
        init,
        validateField,
        passwordStrength,
        showError,
        showValid,
        clearFeedback
    };
})();

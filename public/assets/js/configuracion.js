/**
 * InvSys — Configuración Page Scripts
 */
document.addEventListener('DOMContentLoaded', function() {
// Toggle Switch Logic
document.querySelectorAll('.toggle-switch').forEach(toggle => {
    toggle.addEventListener('click', function() {
        const track = this.querySelector('.toggle-track');
        const input = this.querySelector('input[type="hidden"]');
        const isActive = track.classList.contains('active');
        const onVal = this.dataset.on || '1';
        const offVal = this.dataset.off || '0';
        if (isActive) { track.classList.remove('active'); input.value = offVal; }
        else { track.classList.add('active'); input.value = onVal; }
    });
});
// Color Swatch Selector
document.querySelectorAll('.color-swatch').forEach(swatch => {
    swatch.addEventListener('click', function() {
        document.querySelectorAll('.color-swatch').forEach(s => { s.classList.remove('active'); const c=s.querySelector('.swatch-check'); if(c) c.remove(); });
        this.classList.add('active'); this.querySelector('input').checked = true;
        if (!this.querySelector('.swatch-check')) { const i=document.createElement('i'); i.className='bi bi-check-lg swatch-check'; this.appendChild(i); }
    });
});
// Logo Preview
document.getElementById('logoInput')?.addEventListener('change', function(e) {
    const file = e.target.files[0]; if (!file) return;
    if (file.size > 2*1024*1024) { alert('El archivo es demasiado grande. Máximo 2MB.'); this.value=''; return; }
    const reader = new FileReader();
    reader.onload = function(ev) { const p=document.getElementById('logoPreview'); p.parentElement.innerHTML='<img src="'+ev.target.result+'" alt="Preview" class="logo-preview-img" id="logoPreview">'; };
    reader.readAsDataURL(file);
});
// Drag and Drop Logo
const uploadArea = document.getElementById('logoUploadArea');
if (uploadArea) {
    ['dragenter','dragover'].forEach(e => uploadArea.addEventListener(e, ev => { ev.preventDefault(); uploadArea.classList.add('drag-over'); }));
    ['dragleave','drop'].forEach(e => uploadArea.addEventListener(e, ev => { ev.preventDefault(); uploadArea.classList.remove('drag-over'); }));
    uploadArea.addEventListener('drop', e => { const f=e.dataTransfer.files; if(f.length){document.getElementById('logoInput').files=f; document.getElementById('logoInput').dispatchEvent(new Event('change'));} });
}
// Form Validation
FormValidator.init('#formConfig', { 'config[nombre_sistema]': { required: true, messages: { required: 'El nombre del sistema es obligatorio' } } });
// SMTP Toggle
function updateSmtpVisibility() { const t=document.querySelector('[data-config="smtp_activo"]'); const f=document.getElementById('smtpFields'); if(t&&f){ f.style.display=t.querySelector('.toggle-track').classList.contains('active')?'block':'none'; } }
updateSmtpVisibility();
document.querySelector('[data-config="smtp_activo"]')?.addEventListener('click', () => setTimeout(updateSmtpVisibility, 50));
// Registration Role
function updateRegistroRolVisibility() { const t=document.querySelector('[data-config="permitir_registro"]'); const r=document.getElementById('rolRegistroWrapper'); if(t&&r){ r.style.display=t.querySelector('.toggle-track').classList.contains('active')?'block':'none'; } }
updateRegistroRolVisibility();
document.querySelector('[data-config="permitir_registro"]')?.addEventListener('click', () => setTimeout(updateRegistroRolVisibility, 50));
// Password Toggle
document.querySelectorAll('.toggle-pass').forEach(btn => {
    btn.addEventListener('click', function() { const i=document.getElementById(this.dataset.target); const ic=this.querySelector('i'); if(i.type==='password'){i.type='text';ic.className='bi bi-eye-slash';}else{i.type='password';ic.className='bi bi-eye';} });
});
// Test SMTP
const btnTestMail = document.getElementById('btnTestMail');
const modalTestSmtp = document.getElementById('modalTestSmtp');
const btnConfirmTestMail = document.getElementById('btnConfirmTestMail');
let bsModalTestSmtp = null;

if (btnTestMail && modalTestSmtp) {
    btnTestMail.addEventListener('click', function() {
        if (!bsModalTestSmtp) {
            bsModalTestSmtp = new bootstrap.Modal(modalTestSmtp);
        }
        document.getElementById('smtpTestEmail').value = '';
        bsModalTestSmtp.show();
    });

    modalTestSmtp.addEventListener('shown.bs.modal', function () {
        document.getElementById('smtpTestEmail').focus();
    });

    if (btnConfirmTestMail) {
        btnConfirmTestMail.addEventListener('click', function() {
            const emailInput = document.getElementById('smtpTestEmail');
            const email = emailInput.value.trim();
            
            if (!email || !email.includes('@')) {
                if (typeof showToast === 'function') {
                    showToast('Por favor, ingrese un correo electrónico válido.', 'warning');
                } else {
                    alert('Por favor, ingrese un correo electrónico válido.');
                }
                emailInput.focus();
                return;
            }

            const PD = JSON.parse(document.getElementById('page-data')?.textContent || '{}');
            const btn = this;
            const orig = btn.innerHTML; 
            btn.disabled = true; 
            btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Enviando...';
            
            fetch(PD.testMailUrl || '', { 
                method: 'POST', 
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}, 
                body: '_csrf_token=' + encodeURIComponent(PD.csrfToken || '') + '&email=' + encodeURIComponent(email) 
            })
            .then(r => r.json())
            .then(d => { 
                btn.disabled = false;
                btn.innerHTML = orig;
                bsModalTestSmtp.hide();
                
                if(d.success){
                    if (typeof showToast === 'function') {
                        showToast('El correo de prueba ha sido enviado exitosamente a ' + email, 'success');
                    } else {
                        alert('¡El correo de prueba ha sido enviado exitosamente a ' + email + '!');
                    }
                } else {
                    if (typeof showToast === 'function') {
                        showToast(d.message || 'No se pudo enviar el correo de prueba', 'error');
                    } else {
                        alert('Error: ' + (d.message || 'No se pudo enviar'));
                    }
                }
            })
            .catch(() => {
                btn.disabled = false;
                btn.innerHTML = orig;
                bsModalTestSmtp.hide();
                if (typeof showToast === 'function') {
                    showToast('Error de conexión con el servidor.', 'error');
                } else {
                    alert('Error de conexión con el servidor.');
                }
            });
        });
    }
}

}); // End DOMContentLoaded

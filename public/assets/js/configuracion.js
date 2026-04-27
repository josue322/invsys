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
}); // End DOMContentLoaded

window.testSmtp = function() {
    const PD = JSON.parse(document.getElementById('page-data')?.textContent || '{}');
    
    const email = prompt('Ingrese el correo electrónico al que desea enviar la prueba:', '');
    if (email === null) return; // cancelado
    if (!email || email.trim() === '' || !email.includes('@')) {
        alert('Por favor, ingrese un correo electrónico válido.');
        return;
    }

    const btn = document.getElementById('btnTestMail');
    const orig = btn.innerHTML; 
    btn.disabled=true; 
    btn.innerHTML='<i class="bi bi-hourglass-split me-1"></i>Enviando...';
    
    fetch(PD.testMailUrl||'', { 
        method:'POST', 
        headers:{'Content-Type':'application/x-www-form-urlencoded'}, 
        body:'_csrf_token='+encodeURIComponent(PD.csrfToken||'') + '&email=' + encodeURIComponent(email.trim()) 
    })
    .then(r=>r.json()).then(d => { 
        btn.disabled=false;
        if(d.success){
            btn.innerHTML='<i class="bi bi-check-circle me-1"></i>¡Enviado!';
            btn.classList.remove('btn-outline-warning');
            btn.classList.add('btn-outline-success');
            alert('¡El correo de prueba ha sido enviado exitosamente a ' + email + '!');
        }
        else{
            btn.innerHTML='<i class="bi bi-x-circle me-1"></i>Error';
            btn.classList.remove('btn-outline-warning');
            btn.classList.add('btn-outline-danger');
            alert('Error: '+(d.message||'No se pudo enviar'));
        }
        setTimeout(()=>{btn.innerHTML=orig;btn.className='btn btn-sm btn-outline-warning';},3000);
    }).catch(()=>{btn.disabled=false;btn.innerHTML=orig;alert('Error de conexión con el servidor.');});
};

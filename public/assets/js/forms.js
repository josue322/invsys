/**
 * InvSys — Form Validators (simple forms without complex logic)
 * Covers: auth, categorias, ubicaciones, proveedores, usuarios, perfil, conteos, movimientos, import
 */
document.addEventListener('DOMContentLoaded', function() {
    // === Auth: Login ===
    if (document.getElementById('loginForm')) {
        FormValidator.init('#loginForm', {
            email: { required: true, email: true, messages: { required: 'Ingrese su correo electrónico' } },
            password: { required: true, messages: { required: 'Ingrese su contraseña' } }
        });
    }
    // === Auth: Register ===
    if (document.getElementById('registerForm')) {
        FormValidator.init('#registerForm', {
            nombre: { required: true, minlength: 3, messages: { required: 'Ingrese su nombre completo', minlength: 'El nombre debe tener al menos 3 caracteres' } },
            email: { required: true, email: true, messages: { required: 'Ingrese su correo electrónico' } },
            password: { required: true, minlength: 8, messages: { required: 'Ingrese una contraseña', minlength: 'Mínimo 8 caracteres' } },
            password_confirm: { required: true, match: 'password', messages: { required: 'Confirme su contraseña', match: 'Las contraseñas no coinciden' } }
        });
    }
    // === Categorías: Create ===
    if (document.getElementById('formCrearCategoria')) {
        FormValidator.init('#formCrearCategoria', { nombre: { required: true, maxlength: 100, messages: { required: 'El nombre de la categoría es obligatorio' } } });
    }
    // === Categorías: Edit ===
    if (document.getElementById('formEditarCategoria')) {
        FormValidator.init('#formEditarCategoria', { nombre: { required: true, maxlength: 100, messages: { required: 'El nombre de la categoría es obligatorio' } } });
    }
    // === Ubicaciones: Edit ===
    if (document.getElementById('formEditarUbicacion')) {
        FormValidator.init('#formEditarUbicacion', { nombre: { required: true, maxlength: 100, messages: { required: 'El nombre de la ubicación es obligatorio' } } });
    }
    // === Proveedores: Edit ===
    if (document.getElementById('formEditarProveedor')) {
        FormValidator.init('#formEditarProveedor', { nombre: { required: true, maxlength: 150, messages: { required: 'El nombre del proveedor es obligatorio' } } });
    }
    // === Usuarios: Create ===
    if (document.getElementById('formCrearUsuario')) {
        FormValidator.init('#formCrearUsuario', {
            nombre: { required: true, messages: { required: 'El nombre es obligatorio' } },
            email: { required: true, email: true, messages: { required: 'El correo es obligatorio' } },
            rol_id: { required: true, messages: { required: 'Seleccione un rol' } },
            password: { required: true, minlength: 8, messages: { required: 'La contraseña es obligatoria' } }
        });
        FormValidator.passwordStrength('#password', '#passStrengthCreate');
    }
    // === Usuarios: Edit ===
    if (document.getElementById('formEditarUsuario')) {
        FormValidator.init('#formEditarUsuario', {
            nombre: { required: true, messages: { required: 'El nombre es obligatorio' } },
            email: { required: true, email: true, messages: { required: 'El correo es obligatorio' } },
            rol_id: { required: true, messages: { required: 'Seleccione un rol' } }
        });
    }
    // === Perfil: Datos + Password ===
    if (document.getElementById('formDatos')) {
        FormValidator.init('#formDatos', {
            nombre: { required: true, minlength: 2, messages: { required: 'El nombre es obligatorio' } },
            email: { required: true, email: true, messages: { required: 'El correo es obligatorio' } }
        });
    }
    if (document.getElementById('formPassword')) {
        FormValidator.init('#formPassword', {
            current_password: { required: true, messages: { required: 'Ingrese su contraseña actual' } },
            new_password: { required: true, minlength: 8, messages: { required: 'Ingrese la nueva contraseña' } },
            confirm_password: { required: true, match: '[name="new_password"]', messages: { required: 'Confirme la nueva contraseña', match: 'Las contraseñas no coinciden' } }
        });
        FormValidator.passwordStrength('#newPass', '#passStrength');
    }
    // === Perfil + Usuarios Edit: Password toggle ===
    document.querySelectorAll('.toggle-pass').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = document.getElementById(this.dataset.target);
            const icon = this.querySelector('i');
            if (input.type === 'password') { input.type = 'text'; icon.className = 'bi bi-eye-slash'; }
            else { input.type = 'password'; icon.className = 'bi bi-eye'; }
        });
    });
    // === Usuarios Edit: Copy temp password ===
    if (document.getElementById('tempPass')) {
        window.copyTempPass = function() {
            const input = document.getElementById('tempPass');
            navigator.clipboard.writeText(input.value).then(() => {
                const btn = input.nextElementSibling;
                const orig = btn.innerHTML;
                btn.innerHTML = '<i class="bi bi-check-lg text-success"></i>';
                setTimeout(() => { btn.innerHTML = orig; }, 2000);
            });
        };
    }
    // === Conteos: Create ===
    if (document.getElementById('formCrearConteo')) {
        const filtroTipo = document.getElementById('filtro_tipo');
        const filtroCategoria = document.getElementById('filtroCategoria');
        const filtroUbicacion = document.getElementById('filtroUbicacion');
        const hiddenId = document.getElementById('filtro_id_hidden');
        filtroTipo.addEventListener('change', function() {
            filtroCategoria.classList.add('d-none'); filtroUbicacion.classList.add('d-none'); hiddenId.value = '';
            if (this.value === 'categoria') filtroCategoria.classList.remove('d-none');
            else if (this.value === 'ubicacion') filtroUbicacion.classList.remove('d-none');
        });
        document.querySelector('[name="filtro_id_categoria"]').addEventListener('change', function() { hiddenId.value = this.value; });
        document.querySelector('[name="filtro_id_ubicacion"]').addEventListener('change', function() { hiddenId.value = this.value; });
        FormValidator.init('#formCrearConteo', { nombre: { required: true, maxlength: 150, messages: { required: 'El nombre es obligatorio' } } });
    }
    // === Conteos: Show (inline counting) ===
    if (document.getElementById('conteoTable') && document.querySelector('.conteo-input')) {
        const PAGE_DATA = JSON.parse(document.getElementById('page-data')?.textContent || '{}');
        const BASE = document.querySelector('meta[name="base-url"]')?.content || '/invsys/public';
        const csrfToken = PAGE_DATA.csrfToken || '';
        document.querySelectorAll('.conteo-input').forEach(input => {
            input.addEventListener('input', function() { const b=document.querySelector(`.save-btn[data-item-id="${this.dataset.itemId}"]`); if(b) b.classList.remove('d-none'); });
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') { e.preventDefault(); saveItem(this.dataset.itemId, this.value);
                    const all=[...document.querySelectorAll('.conteo-input')]; const i=all.indexOf(this); if(i<all.length-1) all[i+1].focus(); }
            });
        });
        document.querySelectorAll('.save-btn').forEach(btn => {
            btn.addEventListener('click', function() { const i=document.querySelector(`.conteo-input[data-item-id="${this.dataset.itemId}"]`); saveItem(this.dataset.itemId, i.value); });
        });
        function saveItem(id, val) {
            if (val===''||val===null) return;
            const fd=new FormData(); fd.append('_csrf_token',csrfToken); fd.append('item_id',id); fd.append('stock_fisico',val);
            fetch(`${BASE}/conteos/item`,{method:'POST',headers:{'X-Requested-With':'XMLHttpRequest'},body:fd})
            .then(r=>r.json()).then(d=>{
                if(d.success){updateDiff(id,d.diferencia); const b=document.querySelector(`.save-btn[data-item-id="${id}"]`); if(b) b.classList.add('d-none');
                    const row=document.getElementById(`row-${id}`); row.style.transition='background 0.3s'; row.style.background='rgba(25,135,84,0.1)'; setTimeout(()=>{row.style.background='';},800);
                } else { showToast(d.error||'Error al guardar','error'); }
            }).catch(()=>showToast('Error de conexión','error'));
        }
        function updateDiff(id,diff) {
            const c=document.getElementById(`diff-${id}`); if(!c) return;
            if(diff===0) c.innerHTML='<span class="badge bg-success"><i class="bi bi-check-lg"></i> 0</span>';
            else if(diff>0) c.innerHTML=`<span class="badge bg-warning text-dark">+${diff}</span>`;
            else c.innerHTML=`<span class="badge bg-danger">${diff}</span>`;
        }
    }
    // === Movimientos: Create ===
    if (document.getElementById('formCrearMovimiento')) {
        const ps=document.getElementById('producto_id'), ts=document.getElementById('tipo');
        const sa=document.getElementById('stockActual'), pw=document.getElementById('proveedorWrapper'), dw=document.getElementById('destinoWrapper');
        const lw=document.getElementById('lotesWrapper'), le=document.getElementById('lotesEntradaUI'), ls=document.getElementById('lotesSalidaUI');
        const il=document.getElementById('numero_lote'), iv=document.getElementById('fecha_vencimiento');
        function updateFormUI() {
            const tipo=ts.value, opt=ps.options[ps.selectedIndex], isPer=opt&&opt.dataset.perecedero==='1';
            sa.value=opt?(opt.dataset.stock??'-'):'-';
            pw.classList.add('d-none'); dw.classList.add('d-none'); lw.classList.add('d-none'); le.classList.add('d-none'); ls.classList.add('d-none');
            il.required=false; iv.required=false;
            if(tipo==='entrada'){pw.classList.remove('d-none'); if(isPer){lw.classList.remove('d-none');le.classList.remove('d-none');il.required=true;iv.required=true;}}
            else if(tipo==='salida'){dw.classList.remove('d-none'); if(isPer){lw.classList.remove('d-none');ls.classList.remove('d-none');}}
        }
        ps.addEventListener('change', updateFormUI); ts.addEventListener('change', updateFormUI); updateFormUI();
        FormValidator.init('#formCrearMovimiento', {
            producto_id: { required: true, messages: { required: 'Seleccione un producto' } },
            tipo: { required: true, messages: { required: 'Seleccione el tipo de movimiento' } },
            cantidad: { required: true, min: 1, messages: { required: 'La cantidad es obligatoria' },
                custom(value) { const t=document.getElementById('tipo').value, o=ps.options[ps.selectedIndex], s=parseInt(o?.dataset?.stock??0);
                    if(t==='salida'&&parseInt(value)>s) return `La cantidad (${value}) supera el stock actual (${s})`; return true; } }
        });
    }
    // === Import: CSV Preview ===
    const csvInput = document.getElementById('csv_file');
    if (csvInput) {
        csvInput.addEventListener('change', function(e) {
            const file=e.target.files[0]; if(!file) return;
            const reader=new FileReader();
            reader.onload=function(evt){
                const lines=evt.target.result.split('\n').filter(l=>l.trim()); if(lines.length<2) return;
                const prev=document.getElementById('csv-preview'), th=document.querySelector('#preview-table thead'), tb=document.querySelector('#preview-table tbody');
                const headers=lines[0].split(',').map(h=>h.trim().replace(/['"]/g,''));
                th.innerHTML='<tr>'+headers.map(h=>'<th>'+h+'</th>').join('')+'</tr>';
                tb.innerHTML=''; const max=Math.min(lines.length,6);
                for(let i=1;i<max;i++){const cols=lines[i].split(',').map(c=>c.trim().replace(/['"]/g,'')); tb.innerHTML+='<tr>'+cols.map(c=>'<td><small>'+c+'</small></td>').join('')+'</tr>';}
                document.getElementById('preview-count').textContent=`Mostrando ${max-1} de ${lines.length-1} filas`;
                prev.style.display='block';
            };
            reader.readAsText(file);
        });
    }
});

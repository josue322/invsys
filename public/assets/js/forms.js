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
    // Removido por función duplicada más abajo
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
        const ps=document.getElementById('producto_id'), ts=document.getElementById('tipo'), qi=document.getElementById('cantidad');
        const sa=document.getElementById('stockActual'), pw=document.getElementById('proveedorWrapper'), dw=document.getElementById('destinoWrapper');
        const lw=document.getElementById('lotesWrapper'), le=document.getElementById('lotesEntradaUI'), ls=document.getElementById('lotesSalidaUI');
        const il=document.getElementById('numero_lote'), iv=document.getElementById('fecha_vencimiento');
        const sw=document.getElementById('seriesWrapper'), se=document.getElementById('seriesEntradaUI'), ss=document.getElementById('seriesSalidaUI');
        const sic=document.getElementById('seriesInputContainer'), scc=document.getElementById('seriesCheckContainer');
        const BASE = document.querySelector('meta[name="base-url"]')?.content || '/invsys/public';

        function updateFormUI() {
            var tipo=ts.value, opt=ps.options[ps.selectedIndex], isPer=opt&&opt.dataset.perecedero==='1', isSerie=opt&&opt.dataset.serie==='1';
            sa.value=opt?(opt.dataset.stock||'-'):'-';
            pw.classList.add('d-none'); dw.classList.add('d-none'); lw.classList.add('d-none'); le.classList.add('d-none'); ls.classList.add('d-none');
            if(sw) { sw.classList.add('d-none'); se.classList.add('d-none'); ss.classList.add('d-none'); }
            il.required=false; iv.required=false;
            if(tipo==='entrada'){pw.classList.remove('d-none'); if(isPer){lw.classList.remove('d-none');le.classList.remove('d-none');il.required=true;iv.required=true;}}
            else if(tipo==='salida'){dw.classList.remove('d-none'); if(isPer){lw.classList.remove('d-none');ls.classList.remove('d-none');}}

            // Serial logic
            if(isSerie && sw) {
                if(tipo==='entrada') {
                    sw.classList.remove('d-none');
                    se.classList.remove('d-none');
                    ss.classList.add('d-none');
                    buildSerialInputs();
                } else if(tipo==='salida') {
                    sw.classList.remove('d-none');
                    ss.classList.remove('d-none');
                    se.classList.add('d-none');
                    loadAvailableSerials();
                }
            }
        }

        function buildSerialInputs() {
            var qty = parseInt(qi.value) || 0;
            sic.innerHTML = '';
            const pageDataScript = document.getElementById('page-data');
            const pageData = pageDataScript ? JSON.parse(pageDataScript.textContent || '{}') : {};
            
            for (var i = 0; i < qty; i++) {
                const oldVal = (pageData.old_seriales && pageData.old_seriales[i]) ? pageData.old_seriales[i] : '';
                var div = document.createElement('div');
                div.className = 'col-md-4';
                div.innerHTML = '<input type="text" class="form-control form-control-sm" name="numeros_serie[]" placeholder="Serie #' + (i+1) + '" value="' + oldVal.replace(/"/g, '&quot;') + '" required>';
                sic.appendChild(div);
            }
            if (qty === 0) {
                sic.innerHTML = '<div class="text-muted text-center py-2"><small>Ingrese una cantidad mayor a 0</small></div>';
            }
        }

        function loadAvailableSerials() {
            var prodId = ps.value;
            if (!prodId) return;
            scc.innerHTML = '<div class="text-muted text-center py-2"><small><span class="spinner-border spinner-border-sm me-1"></span>Cargando seriales...</small></div>';
            fetch(BASE + '/productos/seriales/' + prodId, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (!data.seriales || data.seriales.length === 0) {
                        scc.innerHTML = '<div class="alert alert-warning py-2 mb-0"><i class="bi bi-exclamation-triangle me-1"></i>No hay seriales disponibles para este producto.</div>';
                        return;
                    }
                    scc.innerHTML = '';
                    const pageDataScript = document.getElementById('page-data');
                    const pageData = pageDataScript ? JSON.parse(pageDataScript.textContent || '{}') : {};
                    const oldSerieIds = pageData.old_serie_ids || [];

                    data.seriales.forEach(function(s) {
                        var isChecked = oldSerieIds.includes(s.id.toString()) ? 'checked' : '';
                        var div = document.createElement('div');
                        div.className = 'col-md-4';
                        div.innerHTML = '<div class="form-check border rounded px-3 py-2 bg-white">' +
                            '<input class="form-check-input serial-check" type="checkbox" name="serie_ids[]" value="' + s.id + '" id="serie_' + s.id + '" ' + isChecked + '>' +
                            '<label class="form-check-label ms-2 w-100" for="serie_' + s.id + '" style="font-size:0.82rem;">' +
                            '<code class="fw-bold">' + s.numero_serie + '</code>' +
                            '<br><small class="text-muted">' + s.created_at + '</small>' +
                            '</label></div>';
                        scc.appendChild(div);
                    });
                })
                .catch(function() {
                    scc.innerHTML = '<div class="alert alert-danger py-2 mb-0">Error al cargar seriales</div>';
                });
        }

        ps.addEventListener('change', updateFormUI);
        ts.addEventListener('change', updateFormUI);
        qi.addEventListener('input', function() {
            var opt = ps.options[ps.selectedIndex];
            if (opt && opt.dataset.serie === '1' && ts.value === 'entrada') {
                buildSerialInputs();
            }
        });
        updateFormUI();

        FormValidator.init('#formCrearMovimiento', {
            producto_id: { required: true, messages: { required: 'Seleccione un producto' } },
            tipo: { required: true, messages: { required: 'Seleccione el tipo de movimiento' } },
            cantidad: { required: true, min: 1, messages: { required: 'La cantidad es obligatoria' },
                custom(value) { var t=document.getElementById('tipo').value, o=ps.options[ps.selectedIndex], s=parseInt(o?.dataset?.stock??0);
                    if(t==='salida'&&parseInt(value)>s) return 'La cantidad ('+value+') supera el stock actual ('+s+')'; return true; } }
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

    // === Transferencias: Create ===
    if (document.getElementById('formTransferencia')) {
        const selectProducto = document.getElementById('producto_id');
        const selectDestino = document.getElementById('ubicacion_destino_id');
        const btnSubmit = document.getElementById('btnSubmitTransferencia');
        const detalleDiv = document.getElementById('detalleProducto');
        const lblStock = document.getElementById('lblStock');
        const lblUbicacionOrigen = document.getElementById('lblUbicacionOrigen');
        const errorMismaUbicacion = document.getElementById('errorUbicacionMisma');

        let currentUbicacionId = '';

        function actualizarEstadoProducto() {
            const option = selectProducto.options[selectProducto.selectedIndex];
            
            if (selectProducto.value) {
                detalleDiv.classList.remove('d-none');
                lblStock.textContent = option.dataset.stock;
                lblUbicacionOrigen.textContent = option.dataset.ubicacionNombre;
                currentUbicacionId = option.dataset.ubicacionId;
                selectDestino.disabled = false;
            } else {
                detalleDiv.classList.add('d-none');
                currentUbicacionId = '';
                selectDestino.disabled = true;
                selectDestino.value = '';
            }
            
            validarFormulario();
        }

        selectProducto.addEventListener('change', actualizarEstadoProducto);

        if (selectProducto.value) {
            actualizarEstadoProducto();
        }

        selectDestino.addEventListener('change', validarFormulario);

        function validarFormulario() {
            let esValido = true;
            errorMismaUbicacion.classList.add('d-none');
            selectDestino.classList.remove('is-invalid');

            if (!selectProducto.value || !selectDestino.value) {
                esValido = false;
            }

            if (selectProducto.value && selectDestino.value && selectDestino.value === currentUbicacionId) {
                esValido = false;
                errorMismaUbicacion.classList.remove('d-none');
                selectDestino.classList.add('is-invalid');
            }

            btnSubmit.disabled = !esValido;
        }
    }

    // === Requisiciones: Create ===
    if (document.getElementById('form-requisicion')) {
        const tableBody = document.querySelector('#tabla-productos tbody');
        const btnAdd = document.getElementById('btn-add-row');

        if (tableBody) {
            tableBody.addEventListener('change', function(e) {
                if (e.target.classList.contains('select-producto')) {
                    const option = e.target.options[e.target.selectedIndex];
                    const tr = e.target.closest('tr');
                    const stockDisp = tr.querySelector('.stock-disp');
                    const unidadDisp = tr.querySelector('.unidad-disp');
                    const inputQty = tr.querySelector('.input-qty');

                    if (option.value) {
                        const stock = option.getAttribute('data-stock');
                        stockDisp.textContent = stock;
                        unidadDisp.textContent = option.getAttribute('data-unidad');
                        inputQty.max = stock; 
                    } else {
                        stockDisp.textContent = '0';
                        unidadDisp.textContent = '';
                        inputQty.max = '';
                    }
                }
            });

            tableBody.addEventListener('input', function(e) {
                if (e.target.classList.contains('input-qty')) {
                    const max = parseInt(e.target.max);
                    const val = parseInt(e.target.value);
                    if (val > max) {
                        e.target.value = max;
                        if (typeof showToast === 'function') showToast('La cantidad solicitada supera el stock disponible (' + max + ').', 'warning');
                    }
                }
            });

            if (btnAdd) {
                btnAdd.addEventListener('click', function() {
                    const firstRow = tableBody.querySelector('tr');
                    if (firstRow) {
                        const newRow = firstRow.cloneNode(true);
                        
                        const select = newRow.querySelector('.select-producto');
                        if (select) select.value = '';
                        
                        const input = newRow.querySelector('.input-qty');
                        if (input) input.value = '';
                        
                        const stockText = newRow.querySelector('.stock-disp');
                        if (stockText) stockText.textContent = '0';
                        
                        const unitText = newRow.querySelector('.unidad-disp');
                        if (unitText) unitText.textContent = '';
                        
                        tableBody.appendChild(newRow);
                    }
                });
            }

            tableBody.addEventListener('click', function(e) {
                if (e.target.closest('.btn-remove-row')) {
                    if (tableBody.querySelectorAll('tr').length > 1) {
                        e.target.closest('tr').remove();
                    } else {
                        if (typeof showToast === 'function') showToast('Debe haber al menos un producto en la requisición.', 'warning');
                    }
                }
            });
        }
    }

    // === Devoluciones: Create ===
    const dataBridge = document.getElementById('productosDataBridge');
    const formDevolucion = document.getElementById('formDevolucion');
    
    if (formDevolucion && dataBridge) {
        const productosDB = JSON.parse(dataBridge.getAttribute('data-productos') || '[]');
        const buscador = document.getElementById('buscadorProductos');
        const resultados = document.getElementById('resultadosBusqueda');
        const tablaBody = document.querySelector('#tablaDetalles tbody');
        const filaVacia = document.getElementById('filaVacia');
        let productosSeleccionados = new Set();

        buscador.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            resultados.innerHTML = '';
            
            if (query.length < 2) {
                resultados.style.display = 'none';
                return;
            }

            const filtrados = productosDB.filter(p => 
                p.nombre.toLowerCase().includes(query) || 
                p.sku.toLowerCase().includes(query)
            ).slice(0, 10);

            if (filtrados.length === 0) {
                resultados.innerHTML = '<li class="list-group-item text-muted">No se encontraron productos</li>';
            } else {
                filtrados.forEach(p => {
                    if (productosSeleccionados.has(p.id.toString())) return;

                    const li = document.createElement('li');
                    li.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';
                    li.style.cursor = 'pointer';
                    li.innerHTML = `
                        <div>
                            <strong>${p.nombre}</strong><br>
                            <small class="text-muted font-monospace">SKU: ${p.sku}</small>
                        </div>
                        <span class="badge bg-secondary rounded-pill">Añadir</span>
                    `;
                    li.addEventListener('click', () => agregarProducto(p));
                    resultados.appendChild(li);
                });
            }
            
            resultados.style.width = buscador.offsetWidth + 'px';
            resultados.style.left = buscador.offsetLeft + 'px';
            resultados.style.top = (buscador.offsetTop + buscador.offsetHeight) + 'px';
            resultados.style.display = 'block';
        });

        document.addEventListener('click', function(e) {
            if (!buscador.contains(e.target) && !resultados.contains(e.target)) {
                resultados.style.display = 'none';
            }
        });

        function agregarProducto(producto) {
            productosSeleccionados.add(producto.id.toString());
            if(filaVacia) filaVacia.style.display = 'none';
            resultados.style.display = 'none';
            buscador.value = '';

            const tr = document.createElement('tr');
            tr.id = `fila_prod_${producto.id}`;
            tr.innerHTML = `
                <td>
                    <input type="hidden" name="productos[]" value="${producto.id}">
                    <strong>${producto.nombre}</strong><br>
                    <small class="text-muted font-monospace">SKU: ${producto.sku}</small>
                </td>
                <td>
                    <input type="number" name="cantidades[${producto.id}]" class="form-control form-control-sm text-center tabular-nums" min="1" value="1" required>
                </td>
                <td>
                    <select name="estados[${producto.id}]" class="form-select form-select-sm" required>
                        <option value="bueno">Bueno / Operativo</option>
                        <option value="dañado">Dañado / Defectuoso</option>
                    </select>
                </td>
                <td>
                    <input type="text" name="motivos[${producto.id}]" class="form-control form-control-sm" placeholder="Ej: Sobrante..." required>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove border-0" data-id="${producto.id}" title="Eliminar">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;

            tr.querySelector('.btn-remove').addEventListener('click', function() {
                const pid = this.getAttribute('data-id');
                productosSeleccionados.delete(pid);
                tr.remove();
                if (productosSeleccionados.size === 0 && filaVacia) {
                    filaVacia.style.display = '';
                }
            });

            tablaBody.appendChild(tr);
        }

        formDevolucion.addEventListener('submit', function(e) {
            if (productosSeleccionados.size === 0) {
                e.preventDefault();
                if (typeof showToast === 'function') showToast('Debe agregar al menos un producto a la devolución.', 'warning');
            }
        });
    }

    // === Copiar Contraseña Temporal ===
    const btnCopyTempPass = document.getElementById('btnCopyTempPass');
    if (btnCopyTempPass) {
        btnCopyTempPass.addEventListener('click', function() {
            const tempPass = document.getElementById('tempPass');
            if (tempPass) {
                navigator.clipboard.writeText(tempPass.value).then(() => {
                    const icon = btnCopyTempPass.querySelector('i');
                    icon.className = 'bi bi-clipboard-check text-success';
                    setTimeout(() => {
                        icon.className = 'bi bi-clipboard';
                    }, 2000);
                    if (typeof showToast === 'function') {
                        showToast('Contraseña copiada al portapapeles', 'success');
                    }
                });
            }
        });
    }

});

// === Ayuda / Docs ScrollSpy ===
document.addEventListener('DOMContentLoaded', function() {
    const sections = document.querySelectorAll('.docs-section');
    const navItems = document.querySelectorAll('.docs-nav-item');
    if (navItems.length === 0) return;
    
    navItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const targetEl = document.getElementById(targetId);
            if (targetEl) {
                window.scrollTo({
                    top: targetEl.offsetTop - 20,
                    behavior: 'smooth'
                });
            }
        });
    });

    window.addEventListener('scroll', () => {
        let current = '';
        const scrollY = window.pageYOffset;
        sections.forEach(section => {
            const sectionTop = section.offsetTop - 100;
            if (scrollY >= sectionTop) {
                current = section.getAttribute('id');
            }
        });
        navItems.forEach(item => {
            item.classList.remove('active');
            if (item.getAttribute('href').substring(1) === current) {
                item.classList.add('active');
            }
        });
    });
});

// === Ayuda / Soporte Form ===
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-soporte');
    const btnSubmit = document.getElementById('btn-enviar-soporte');
    const fileInput = document.getElementById('captura');
    const fileError = document.getElementById('file-error');
    
    if (fileInput && fileError) {
        fileInput.addEventListener('change', function() {
            fileError.classList.add('d-none');
            if (this.files && this.files[0]) {
                const file = this.files[0];
                const maxSize = 2 * 1024 * 1024; // 2MB
                
                if (file.size > maxSize) {
                    fileError.textContent = 'La imagen excede el límite de 2MB. Por favor, comprímela o elige otra.';
                    fileError.classList.remove('d-none');
                    this.value = ''; // Clear input
                }
            }
        });
    }

    if (form && btnSubmit && fileInput && fileError) {
        form.addEventListener('submit', function(e) {
            if (fileInput.files && fileInput.files[0] && fileInput.files[0].size > 2 * 1024 * 1024) {
                e.preventDefault();
                fileError.textContent = 'La imagen excede el límite de 2MB.';
                fileError.classList.remove('d-none');
                return;
            }
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando ticket...';
        });
    }
});

// === Form Draft Autosave (Movimientos & Conteos) ===
document.addEventListener('DOMContentLoaded', function() {
    // 1. Movimientos
    const formMov = document.getElementById('formCrearMovimiento');
    if (formMov) {
        const STORAGE_KEY_MOV = 'invsys_draft_movimiento';
        const inputsMov = ['producto_id', 'tipo', 'cantidad', 'proveedor_id', 'destino', 'referencia', 'numero_lote', 'fecha_vencimiento'];
        
        function saveMovState() {
            const state = {};
            inputsMov.forEach(id => {
                const el = document.getElementById(id) || document.querySelector(`[name="${id}"]`);
                if (el) state[id] = el.value;
            });
            sessionStorage.setItem(STORAGE_KEY_MOV, JSON.stringify(state));
        }

        const savedMovStr = sessionStorage.getItem(STORAGE_KEY_MOV);
        if (savedMovStr) {
            try {
                const state = JSON.parse(savedMovStr);
                inputsMov.forEach(id => {
                    if (state[id]) {
                        const el = document.getElementById(id) || document.querySelector(`[name="${id}"]`);
                        if (el) {
                            el.value = state[id];
                            el.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    }
                });
            } catch (e) {}
        }

        formMov.addEventListener('input', saveMovState);
        formMov.addEventListener('change', saveMovState);
        formMov.addEventListener('submit', () => sessionStorage.removeItem(STORAGE_KEY_MOV));
    }

    // 2. Conteos
    const formConteo = document.getElementById('formCrearConteo');
    if (formConteo) {
        const STORAGE_KEY_CONTEO = 'invsys_draft_conteo';
        const inputsConteo = ['nombre', 'descripcion', 'filtro_tipo', 'filtro_id_categoria', 'filtro_id_ubicacion'];
        
        function saveConteoState() {
            const state = {};
            inputsConteo.forEach(id => {
                const el = document.getElementById(id) || document.querySelector(`[name="${id}"]`);
                if (el) state[id] = el.value;
            });
            sessionStorage.setItem(STORAGE_KEY_CONTEO, JSON.stringify(state));
        }

        const savedConteoStr = sessionStorage.getItem(STORAGE_KEY_CONTEO);
        if (savedConteoStr) {
            try {
                const state = JSON.parse(savedConteoStr);
                inputsConteo.forEach(id => {
                    if (state[id]) {
                        const el = document.getElementById(id) || document.querySelector(`[name="${id}"]`);
                        if (el) {
                            el.value = state[id];
                            el.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    }
                });
            } catch (e) {}
        }

        formConteo.addEventListener('input', saveConteoState);
        formConteo.addEventListener('change', saveConteoState);
        formConteo.addEventListener('submit', () => sessionStorage.removeItem(STORAGE_KEY_CONTEO));
    }
});

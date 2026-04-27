<?php
/**
 * InvSys - Definición de Rutas
 * 
 * Todas las rutas del sistema se definen aquí.
 * Formato: método, ruta, controlador, acción, middleware
 */

return [
    // =====================================================
    // AUTENTICACIÓN (sin middleware auth)
    // =====================================================
    [
        'method' => 'GET',
        'path' => 'login',
        'controller' => 'AuthController',
        'action' => 'showLogin',
        'middleware' => ['guest'],
    ],
    [
        'method' => 'POST',
        'path' => 'login',
        'controller' => 'AuthController',
        'action' => 'login',
        'middleware' => ['guest'],
    ],
    [
        'method' => 'GET',
        'path' => 'logout',
        'controller' => 'AuthController',
        'action' => 'logout',
        'middleware' => ['auth'],
    ],
    [
        'method' => 'GET',
        'path' => 'registro',
        'controller' => 'AuthController',
        'action' => 'showRegister',
        'middleware' => ['guest'],
    ],
    [
        'method' => 'POST',
        'path' => 'registro',
        'controller' => 'AuthController',
        'action' => 'register',
        'middleware' => ['guest'],
    ],

    // =====================================================
    // DASHBOARD
    // =====================================================
    [
        'method' => 'GET',
        'path' => '',
        'controller' => 'DashboardController',
        'action' => 'index',
        'middleware' => ['auth', 'permiso:dashboard.ver'],
    ],
    [
        'method' => 'GET',
        'path' => 'dashboard',
        'controller' => 'DashboardController',
        'action' => 'index',
        'middleware' => ['auth', 'permiso:dashboard.ver'],
    ],
    [
        'method' => 'GET',
        'path' => 'dashboard/data',
        'controller' => 'DashboardController',
        'action' => 'getData',
        'middleware' => ['auth'],
    ],

    // =====================================================
    // PRODUCTOS
    // =====================================================
    [
        'method' => 'GET',
        'path' => 'productos',
        'controller' => 'ProductoController',
        'action' => 'index',
        'middleware' => ['auth', 'permiso:productos.ver'],
    ],
    [
        'method' => 'GET',
        'path' => 'productos/crear',
        'controller' => 'ProductoController',
        'action' => 'create',
        'middleware' => ['auth', 'permiso:productos.crear'],
    ],
    [
        'method' => 'POST',
        'path' => 'productos/crear',
        'controller' => 'ProductoController',
        'action' => 'store',
        'middleware' => ['auth', 'permiso:productos.crear'],
    ],
    [
        'method' => 'GET',
        'path' => 'productos/editar/{id}',
        'controller' => 'ProductoController',
        'action' => 'edit',
        'middleware' => ['auth', 'permiso:productos.editar'],
    ],
    [
        'method' => 'POST',
        'path' => 'productos/editar/{id}',
        'controller' => 'ProductoController',
        'action' => 'update',
        'middleware' => ['auth', 'permiso:productos.editar'],
    ],
    [
        'method' => 'POST',
        'path' => 'productos/eliminar/{id}',
        'controller' => 'ProductoController',
        'action' => 'destroy',
        'middleware' => ['auth', 'permiso:productos.eliminar'],
    ],
    [
        'method' => 'POST',
        'path' => 'productos/toggle/{id}',
        'controller' => 'ProductoController',
        'action' => 'toggle',
        'middleware' => ['auth', 'permiso:productos.editar'],
    ],
    [
        'method' => 'GET',
        'path' => 'productos/ver/{id}',
        'controller' => 'ProductoController',
        'action' => 'show',
        'middleware' => ['auth', 'permiso:productos.ver'],
    ],
    [
        'method' => 'GET',
        'path' => 'productos/importar',
        'controller' => 'ProductoController',
        'action' => 'importForm',
        'middleware' => ['auth', 'permiso:productos.crear'],
    ],
    [
        'method' => 'POST',
        'path' => 'productos/importar',
        'controller' => 'ProductoController',
        'action' => 'import',
        'middleware' => ['auth', 'permiso:productos.crear'],
    ],
    [
        'method' => 'GET',
        'path' => 'productos/buscar',
        'controller' => 'ProductoController',
        'action' => 'search',
        'middleware' => ['auth', 'permiso:productos.ver'],
    ],
    // Producto-Proveedor (AJAX)
    [
        'method' => 'POST',
        'path' => 'productos/proveedor/add',
        'controller' => 'ProductoController',
        'action' => 'addProveedor',
        'middleware' => ['auth', 'permiso:productos.editar'],
    ],
    [
        'method' => 'POST',
        'path' => 'productos/proveedor/remove',
        'controller' => 'ProductoController',
        'action' => 'removeProveedor',
        'middleware' => ['auth', 'permiso:productos.editar'],
    ],

    // =====================================================
    // CATEGORÍAS
    // =====================================================
    [
        'method' => 'GET',
        'path' => 'categorias',
        'controller' => 'CategoriaController',
        'action' => 'index',
        'middleware' => ['auth', 'permiso:categorias.ver'],
    ],
    [
        'method' => 'GET',
        'path' => 'categorias/crear',
        'controller' => 'CategoriaController',
        'action' => 'create',
        'middleware' => ['auth', 'permiso:categorias.crear'],
    ],
    [
        'method' => 'POST',
        'path' => 'categorias/crear',
        'controller' => 'CategoriaController',
        'action' => 'store',
        'middleware' => ['auth', 'permiso:categorias.crear'],
    ],
    [
        'method' => 'GET',
        'path' => 'categorias/editar/{id}',
        'controller' => 'CategoriaController',
        'action' => 'edit',
        'middleware' => ['auth', 'permiso:categorias.editar'],
    ],
    [
        'method' => 'POST',
        'path' => 'categorias/editar/{id}',
        'controller' => 'CategoriaController',
        'action' => 'update',
        'middleware' => ['auth', 'permiso:categorias.editar'],
    ],
    [
        'method' => 'POST',
        'path' => 'categorias/toggle/{id}',
        'controller' => 'CategoriaController',
        'action' => 'toggle',
        'middleware' => ['auth', 'permiso:categorias.editar'],
    ],
    [
        'method' => 'POST',
        'path' => 'categorias/eliminar/{id}',
        'controller' => 'CategoriaController',
        'action' => 'destroy',
        'middleware' => ['auth', 'permiso:categorias.eliminar'],
    ],

    // =====================================================
    // PROVEEDORES
    // =====================================================
    [
        'method' => 'GET',
        'path' => 'proveedores',
        'controller' => 'ProveedorController',
        'action' => 'index',
        'middleware' => ['auth', 'permiso:proveedores.ver'],
    ],
    [
        'method' => 'GET',
        'path' => 'proveedores/crear',
        'controller' => 'ProveedorController',
        'action' => 'create',
        'middleware' => ['auth', 'permiso:proveedores.crear'],
    ],
    [
        'method' => 'POST',
        'path' => 'proveedores/crear',
        'controller' => 'ProveedorController',
        'action' => 'store',
        'middleware' => ['auth', 'permiso:proveedores.crear'],
    ],
    [
        'method' => 'GET',
        'path' => 'proveedores/editar/{id}',
        'controller' => 'ProveedorController',
        'action' => 'edit',
        'middleware' => ['auth', 'permiso:proveedores.editar'],
    ],
    [
        'method' => 'POST',
        'path' => 'proveedores/editar/{id}',
        'controller' => 'ProveedorController',
        'action' => 'update',
        'middleware' => ['auth', 'permiso:proveedores.editar'],
    ],
    [
        'method' => 'POST',
        'path' => 'proveedores/toggle/{id}',
        'controller' => 'ProveedorController',
        'action' => 'toggle',
        'middleware' => ['auth', 'permiso:proveedores.editar'],
    ],
    [
        'method' => 'POST',
        'path' => 'proveedores/eliminar/{id}',
        'controller' => 'ProveedorController',
        'action' => 'destroy',
        'middleware' => ['auth', 'permiso:proveedores.eliminar'],
    ],

    // =====================================================
    // UBICACIONES
    // =====================================================
    [
        'method' => 'GET',
        'path' => 'ubicaciones',
        'controller' => 'UbicacionController',
        'action' => 'index',
        'middleware' => ['auth', 'permiso:ubicaciones.ver'],
    ],
    [
        'method' => 'GET',
        'path' => 'ubicaciones/crear',
        'controller' => 'UbicacionController',
        'action' => 'create',
        'middleware' => ['auth', 'permiso:ubicaciones.crear'],
    ],
    [
        'method' => 'POST',
        'path' => 'ubicaciones/crear',
        'controller' => 'UbicacionController',
        'action' => 'store',
        'middleware' => ['auth', 'permiso:ubicaciones.crear'],
    ],
    [
        'method' => 'GET',
        'path' => 'ubicaciones/editar/{id}',
        'controller' => 'UbicacionController',
        'action' => 'edit',
        'middleware' => ['auth', 'permiso:ubicaciones.editar'],
    ],
    [
        'method' => 'POST',
        'path' => 'ubicaciones/editar/{id}',
        'controller' => 'UbicacionController',
        'action' => 'update',
        'middleware' => ['auth', 'permiso:ubicaciones.editar'],
    ],
    [
        'method' => 'POST',
        'path' => 'ubicaciones/toggle/{id}',
        'controller' => 'UbicacionController',
        'action' => 'toggle',
        'middleware' => ['auth', 'permiso:ubicaciones.editar'],
    ],
    [
        'method' => 'POST',
        'path' => 'ubicaciones/eliminar/{id}',
        'controller' => 'UbicacionController',
        'action' => 'destroy',
        'middleware' => ['auth', 'permiso:ubicaciones.eliminar'],
    ],

    // =====================================================
    // ESCÁNER DE CÓDIGOS
    // =====================================================
    [
        'method' => 'GET',
        'path' => 'escaner',
        'controller' => 'EscanerController',
        'action' => 'index',
        'middleware' => ['auth', 'permiso:productos.ver'],
    ],
    [
        'method' => 'GET',
        'path' => 'escaner/buscar/{codigo}',
        'controller' => 'EscanerController',
        'action' => 'buscar',
        'middleware' => ['auth', 'permiso:productos.ver'],
    ],
    [
        'method' => 'GET',
        'path' => 'escaner/lookup/{codigo}',
        'controller' => 'EscanerController',
        'action' => 'lookupExterno',
        'middleware' => ['auth', 'permiso:productos.ver'],
    ],

    // =====================================================
    // CONTEO FÍSICO (Auditoría de Inventario)
    // =====================================================
    [
        'method' => 'GET',
        'path' => 'conteos',
        'controller' => 'ConteoController',
        'action' => 'index',
        'middleware' => ['auth', 'permiso:movimientos.ver'],
    ],
    [
        'method' => 'GET',
        'path' => 'conteos/crear',
        'controller' => 'ConteoController',
        'action' => 'create',
        'middleware' => ['auth', 'permiso:movimientos.crear'],
    ],
    [
        'method' => 'POST',
        'path' => 'conteos/crear',
        'controller' => 'ConteoController',
        'action' => 'store',
        'middleware' => ['auth', 'permiso:movimientos.crear'],
    ],
    [
        'method' => 'GET',
        'path' => 'conteos/{id}',
        'controller' => 'ConteoController',
        'action' => 'show',
        'middleware' => ['auth', 'permiso:movimientos.ver'],
    ],
    [
        'method' => 'POST',
        'path' => 'conteos/item',
        'controller' => 'ConteoController',
        'action' => 'updateItem',
        'middleware' => ['auth', 'permiso:movimientos.crear'],
    ],
    [
        'method' => 'POST',
        'path' => 'conteos/cerrar/{id}',
        'controller' => 'ConteoController',
        'action' => 'close',
        'middleware' => ['auth', 'permiso:movimientos.crear'],
    ],
    [
        'method' => 'POST',
        'path' => 'conteos/aplicar/{id}',
        'controller' => 'ConteoController',
        'action' => 'apply',
        'middleware' => ['auth', 'permiso:movimientos.crear'],
    ],
    [
        'method' => 'POST',
        'path' => 'conteos/eliminar/{id}',
        'controller' => 'ConteoController',
        'action' => 'destroy',
        'middleware' => ['auth', 'permiso:movimientos.crear'],
    ],
    [
        'method' => 'GET',
        'path' => 'conteos/exportar-pdf/{id}',
        'controller' => 'ConteoController',
        'action' => 'exportPDF',
        'middleware' => ['auth', 'permiso:movimientos.ver'],
    ],
    [
        'method' => 'GET',
        'path' => 'conteos/exportar-csv/{id}',
        'controller' => 'ConteoController',
        'action' => 'exportCSV',
        'middleware' => ['auth', 'permiso:movimientos.ver'],
    ],

    // =====================================================
    // MOVIMIENTOS
    // =====================================================
    [
        'method' => 'GET',
        'path' => 'movimientos',
        'controller' => 'MovimientoController',
        'action' => 'index',
        'middleware' => ['auth', 'permiso:movimientos.ver'],
    ],
    [
        'method' => 'GET',
        'path' => 'movimientos/crear',
        'controller' => 'MovimientoController',
        'action' => 'create',
        'middleware' => ['auth', 'permiso:movimientos.crear'],
    ],
    [
        'method' => 'POST',
        'path' => 'movimientos/crear',
        'controller' => 'MovimientoController',
        'action' => 'store',
        'middleware' => ['auth', 'permiso:movimientos.crear'],
    ],

    // =====================================================
    // ALERTAS
    // =====================================================
    [
        'method' => 'GET',
        'path' => 'alertas',
        'controller' => 'AlertaController',
        'action' => 'index',
        'middleware' => ['auth', 'permiso:alertas.ver'],
    ],
    [
        'method' => 'POST',
        'path' => 'alertas/leer/{id}',
        'controller' => 'AlertaController',
        'action' => 'markRead',
        'middleware' => ['auth', 'permiso:alertas.gestionar'],
    ],
    [
        'method' => 'POST',
        'path' => 'alertas/leer-todas',
        'controller' => 'AlertaController',
        'action' => 'markAllRead',
        'middleware' => ['auth', 'permiso:alertas.gestionar'],
    ],
    [
        'method' => 'GET',
        'path' => 'alertas/count',
        'controller' => 'AlertaController',
        'action' => 'count',
        'middleware' => ['auth'],
    ],
    [
        'method' => 'GET',
        'path' => 'alertas/recientes',
        'controller' => 'AlertaController',
        'action' => 'recent',
        'middleware' => ['auth'],
    ],

    // =====================================================
    // REPORTES
    // =====================================================
    [
        'method' => 'GET',
        'path' => 'reportes',
        'controller' => 'ReporteController',
        'action' => 'index',
        'middleware' => ['auth', 'permiso:reportes.ver'],
    ],

    // --- Exportación CSV ---
    [
        'method' => 'GET',
        'path' => 'reportes/exportar/inventario/csv',
        'controller' => 'ReporteController',
        'action' => 'exportInventarioCSV',
        'middleware' => ['auth', 'permiso:reportes.ver'],
    ],
    [
        'method' => 'GET',
        'path' => 'reportes/exportar/stock-bajo/csv',
        'controller' => 'ReporteController',
        'action' => 'exportStockBajoCSV',
        'middleware' => ['auth', 'permiso:reportes.ver'],
    ],
    [
        'method' => 'GET',
        'path' => 'reportes/exportar/top-productos/csv',
        'controller' => 'ReporteController',
        'action' => 'exportTopProductosCSV',
        'middleware' => ['auth', 'permiso:reportes.ver'],
    ],
    [
        'method' => 'GET',
        'path' => 'reportes/exportar/categorias/csv',
        'controller' => 'ReporteController',
        'action' => 'exportCategoriasCSV',
        'middleware' => ['auth', 'permiso:reportes.ver'],
    ],
    [
        'method' => 'GET',
        'path' => 'reportes/exportar/movimientos/csv',
        'controller' => 'ReporteController',
        'action' => 'exportMovimientosCSV',
        'middleware' => ['auth', 'permiso:reportes.ver'],
    ],

    // --- Exportación PDF ---
    [
        'method' => 'GET',
        'path' => 'reportes/exportar/inventario/pdf',
        'controller' => 'ReporteController',
        'action' => 'exportInventarioPDF',
        'middleware' => ['auth', 'permiso:reportes.ver'],
    ],
    [
        'method' => 'GET',
        'path' => 'reportes/exportar/stock-bajo/pdf',
        'controller' => 'ReporteController',
        'action' => 'exportStockBajoPDF',
        'middleware' => ['auth', 'permiso:reportes.ver'],
    ],
    [
        'method' => 'GET',
        'path' => 'reportes/exportar/movimientos/pdf',
        'controller' => 'ReporteController',
        'action' => 'exportMovimientosPDF',
        'middleware' => ['auth', 'permiso:reportes.ver'],
    ],
    [
        'method' => 'GET',
        'path' => 'reportes/exportar/completo/pdf',
        'controller' => 'ReporteController',
        'action' => 'exportCompletoPDF',
        'middleware' => ['auth', 'permiso:reportes.ver'],
    ],

    // --- Fase 2: Kardex ---
    [
        'method' => 'GET',
        'path' => 'reportes/kardex',
        'controller' => 'ReporteController',
        'action' => 'kardex',
        'middleware' => ['auth', 'permiso:reportes.ver'],
    ],
    [
        'method' => 'GET',
        'path' => 'reportes/kardex/exportar/csv',
        'controller' => 'ReporteController',
        'action' => 'exportKardexCSV',
        'middleware' => ['auth', 'permiso:reportes.ver'],
    ],

    // --- Fase 2: Análisis Avanzados ---
    [
        'method' => 'GET',
        'path' => 'reportes/analisis/abc',
        'controller' => 'ReporteController',
        'action' => 'analisisABC',
        'middleware' => ['auth', 'permiso:reportes.ver'],
    ],
    [
        'method' => 'GET',
        'path' => 'reportes/analisis/abc/csv',
        'controller' => 'ReporteController',
        'action' => 'exportABCcsv',
        'middleware' => ['auth', 'permiso:reportes.ver'],
    ],
    [
        'method' => 'GET',
        'path' => 'reportes/analisis/rotacion',
        'controller' => 'ReporteController',
        'action' => 'rotacion',
        'middleware' => ['auth', 'permiso:reportes.ver'],
    ],
    [
        'method' => 'GET',
        'path' => 'reportes/analisis/rotacion/csv',
        'controller' => 'ReporteController',
        'action' => 'exportRotacionCSV',
        'middleware' => ['auth', 'permiso:reportes.ver'],
    ],
    [
        'method' => 'GET',
        'path' => 'reportes/analisis/muertos',
        'controller' => 'ReporteController',
        'action' => 'productosMuertos',
        'middleware' => ['auth', 'permiso:reportes.ver'],
    ],

    // ==========================================
    // FASE 3: COMPRAS (ABASTECIMIENTO)
    // ==========================================
    [
        'method' => 'GET',
        'path' => 'compras',
        'controller' => 'OrdenCompraController',
        'action' => 'index',
        'middleware' => ['auth', 'permiso:compras.ver'],
    ],
    [
        'method' => 'GET',
        'path' => 'compras/crear',
        'controller' => 'OrdenCompraController',
        'action' => 'crear',
        'middleware' => ['auth', 'permiso:compras.crear'],
    ],
    [
        'method' => 'POST',
        'path' => 'compras/store',
        'controller' => 'OrdenCompraController',
        'action' => 'store',
        'middleware' => ['auth', 'permiso:compras.crear'],
    ],
    [
        'method' => 'GET',
        'path' => 'compras/show/{id}',
        'controller' => 'OrdenCompraController',
        'action' => 'show',
        'middleware' => ['auth', 'permiso:compras.ver'],
    ],
    [
        'method' => 'POST',
        'path' => 'compras/recibir/{id}',
        'controller' => 'OrdenCompraController',
        'action' => 'recibir',
        'middleware' => ['auth'], // requires validation inside
    ],
    [
        'method' => 'POST',
        'path' => 'compras/cancelar/{id}',
        'controller' => 'OrdenCompraController',
        'action' => 'cancelar',
        'middleware' => ['auth', 'permiso:compras.crear'],
    ],

    // ==========================================
    // FASE 6: DEPARTAMENTOS
    // ==========================================
    [
        'method' => 'GET',
        'path' => 'departamentos',
        'controller' => 'DepartamentoController',
        'action' => 'index',
        'middleware' => ['auth', 'permiso:departamentos.ver'],
    ],
    [
        'method' => 'GET',
        'path' => 'departamentos/create',
        'controller' => 'DepartamentoController',
        'action' => 'create',
        'middleware' => ['auth', 'permiso:departamentos.crear'],
    ],
    [
        'method' => 'POST',
        'path' => 'departamentos/store',
        'controller' => 'DepartamentoController',
        'action' => 'store',
        'middleware' => ['auth', 'permiso:departamentos.crear'],
    ],
    [
        'method' => 'GET',
        'path' => 'departamentos/edit/{id}',
        'controller' => 'DepartamentoController',
        'action' => 'edit',
        'middleware' => ['auth', 'permiso:departamentos.crear'],
    ],
    [
        'method' => 'POST',
        'path' => 'departamentos/update/{id}',
        'controller' => 'DepartamentoController',
        'action' => 'update',
        'middleware' => ['auth', 'permiso:departamentos.crear'],
    ],

    // ==========================================
    // FASE 7: REQUISICIONES
    // ==========================================
    [
        'method' => 'GET',
        'path' => 'requisiciones',
        'controller' => 'RequisicionController',
        'action' => 'index',
        'middleware' => ['auth', 'permiso:requisiciones.ver'],
    ],
    [
        'method' => 'GET',
        'path' => 'requisiciones/crear',
        'controller' => 'RequisicionController',
        'action' => 'crear',
        'middleware' => ['auth', 'permiso:requisiciones.crear'],
    ],
    [
        'method' => 'POST',
        'path' => 'requisiciones/store',
        'controller' => 'RequisicionController',
        'action' => 'store',
        'middleware' => ['auth', 'permiso:requisiciones.crear'],
    ],
    [
        'method' => 'GET',
        'path' => 'requisiciones/show/{id}',
        'controller' => 'RequisicionController',
        'action' => 'show',
        'middleware' => ['auth', 'permiso:requisiciones.ver'],
    ],
    [
        'method' => 'POST',
        'path' => 'requisiciones/despachar/{id}',
        'controller' => 'RequisicionController',
        'action' => 'despachar',
        'middleware' => ['auth', 'permiso:requisiciones.despachar'],
    ],
    [
        'method' => 'POST',
        'path' => 'requisiciones/cancelar/{id}',
        'controller' => 'RequisicionController',
        'action' => 'cancelar',
        'middleware' => ['auth', 'permiso:requisiciones.crear'],
    ],

    // =====================================================
    // USUARIOS (solo Admin)
    // =====================================================
    [
        'method' => 'GET',
        'path' => 'usuarios',
        'controller' => 'UsuarioController',
        'action' => 'index',
        'middleware' => ['auth', 'permiso:usuarios.ver'],
    ],
    [
        'method' => 'GET',
        'path' => 'usuarios/ver/{id}',
        'controller' => 'UsuarioController',
        'action' => 'show',
        'middleware' => ['auth', 'permiso:usuarios.ver'],
    ],
    [
        'method' => 'GET',
        'path' => 'usuarios/crear',
        'controller' => 'UsuarioController',
        'action' => 'create',
        'middleware' => ['auth', 'permiso:usuarios.crear'],
    ],
    [
        'method' => 'POST',
        'path' => 'usuarios/crear',
        'controller' => 'UsuarioController',
        'action' => 'store',
        'middleware' => ['auth', 'permiso:usuarios.crear'],
    ],
    [
        'method' => 'GET',
        'path' => 'usuarios/editar/{id}',
        'controller' => 'UsuarioController',
        'action' => 'edit',
        'middleware' => ['auth', 'permiso:usuarios.editar'],
    ],
    [
        'method' => 'POST',
        'path' => 'usuarios/editar/{id}',
        'controller' => 'UsuarioController',
        'action' => 'update',
        'middleware' => ['auth', 'permiso:usuarios.editar'],
    ],
    [
        'method' => 'POST',
        'path' => 'usuarios/toggle/{id}',
        'controller' => 'UsuarioController',
        'action' => 'toggleStatus',
        'middleware' => ['auth', 'permiso:usuarios.editar'],
    ],
    [
        'method' => 'POST',
        'path' => 'usuarios/eliminar/{id}',
        'controller' => 'UsuarioController',
        'action' => 'destroy',
        'middleware' => ['auth', 'permiso:usuarios.eliminar'],
    ],

    // =====================================================
    // CONFIGURACIÓN (solo Admin)
    // =====================================================
    [
        'method' => 'GET',
        'path' => 'configuracion',
        'controller' => 'ConfigController',
        'action' => 'index',
        'middleware' => ['auth', 'permiso:configuracion.ver'],
    ],
    [
        'method' => 'POST',
        'path' => 'configuracion',
        'controller' => 'ConfigController',
        'action' => 'update',
        'middleware' => ['auth', 'permiso:configuracion.editar'],
    ],
    [
        'method' => 'POST',
        'path' => 'configuracion/test-mail',
        'controller' => 'ConfigController',
        'action' => 'testMail',
        'middleware' => ['auth', 'permiso:configuracion.editar'],
    ],

    // =====================================================
    // SEGURIDAD / AUDITORÍA (solo Admin)
    // =====================================================
    [
        'method' => 'GET',
        'path' => 'seguridad',
        'controller' => 'SeguridadController',
        'action' => 'index',
        'middleware' => ['auth', 'permiso:seguridad.ver'],
    ],

    // =====================================================
    // COPIAS DE SEGURIDAD (solo Admin)
    // =====================================================
    [
        'method' => 'GET',
        'path' => 'backups',
        'controller' => 'BackupController',
        'action' => 'index',
        'middleware' => ['auth', 'permiso:configuracion.editar'],
    ],
    [
        'method' => 'POST',
        'path' => 'backups/crear',
        'controller' => 'BackupController',
        'action' => 'create',
        'middleware' => ['auth', 'permiso:configuracion.editar'],
    ],
    [
        'method' => 'GET',
        'path' => 'backups/descargar/{id}',
        'controller' => 'BackupController',
        'action' => 'download',
        'middleware' => ['auth', 'permiso:configuracion.editar'],
    ],
    [
        'method' => 'POST',
        'path' => 'backups/eliminar/{id}',
        'controller' => 'BackupController',
        'action' => 'destroy',
        'middleware' => ['auth', 'permiso:configuracion.editar'],
    ],

    // =====================================================
    // TEMA (AJAX)
    // =====================================================
    [
        'method' => 'POST',
        'path' => 'tema/toggle',
        'controller' => 'TemaController',
        'action' => 'toggle',
        'middleware' => ['auth'],
    ],

    // =====================================================
    // PERFIL DE USUARIO (todos los roles)
    // =====================================================
    [
        'method' => 'GET',
        'path' => 'perfil',
        'controller' => 'PerfilController',
        'action' => 'index',
        'middleware' => ['auth'],
    ],
    [
        'method' => 'POST',
        'path' => 'perfil/info',
        'controller' => 'PerfilController',
        'action' => 'updateInfo',
        'middleware' => ['auth'],
    ],
    [
        'method' => 'POST',
        'path' => 'perfil/password',
        'controller' => 'PerfilController',
        'action' => 'updatePassword',
        'middleware' => ['auth'],
    ],
];

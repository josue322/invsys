-- =====================================================
-- InvSys - Fix de encoding UTF-8
-- Corrige datos que se insertaron con charset incorrecto
-- =====================================================

SET NAMES utf8mb4;

-- === CATEGORÍAS ===
UPDATE categorias SET nombre = 'Electrónica', descripcion = 'Dispositivos y componentes electrónicos' WHERE id = 1;
UPDATE categorias SET descripcion = 'Herramientas manuales y eléctricas' WHERE nombre = 'Herramientas';
UPDATE categorias SET descripcion = 'Equipos de protección y seguridad' WHERE nombre = 'Seguridad';

-- === ROLES ===
UPDATE roles SET descripcion = 'Administrador del sistema con acceso total' WHERE id = 1;
UPDATE roles SET descripcion = 'Supervisor con acceso a reportes y gestión' WHERE id = 2;
UPDATE roles SET descripcion = 'Operador con acceso básico a inventario' WHERE id = 3;

-- === PROVEEDORES ===
UPDATE proveedores SET direccion = 'Av. Tecnológica 123, Lima' WHERE id = 1;

-- === UBICACIONES ===
UPDATE ubicaciones SET descripcion = 'Electrónica de alto valor' WHERE id = 1;
UPDATE ubicaciones SET nombre = 'Cuarto Frío 1', descripcion = 'Productos perecederos y químicos' WHERE id = 4;

-- === PRODUCTOS ===
UPDATE productos SET descripcion = 'Detergente líquido industrial' WHERE sku = 'LIMP-001';

-- === CONFIGURACIONES (descripciones) ===
UPDATE configuraciones SET descripcion = 'Permitir registro público de nuevos usuarios' WHERE clave = 'permitir_registro';
UPDATE configuraciones SET descripcion = 'ID del rol asignado a nuevos usuarios registrados públicamente' WHERE clave = 'rol_registro_publico';
UPDATE configuraciones SET descripcion = 'Número máximo de intentos de login fallidos' WHERE clave = 'intentos_login_max';
UPDATE configuraciones SET descripcion = 'Tiempo de bloqueo por intentos fallidos (minutos)' WHERE clave = 'tiempo_bloqueo_minutos';
UPDATE configuraciones SET descripcion = 'Tiempo de vida de la sesión en segundos' WHERE clave = 'session_lifetime';
UPDATE configuraciones SET descripcion = 'Enviar alertas por correo electrónico' WHERE clave = 'alertas_email';
UPDATE configuraciones SET descripcion = 'Símbolo de la moneda' WHERE clave = 'moneda_simbolo';
UPDATE configuraciones SET descripcion = 'Código ISO de la moneda' WHERE clave = 'moneda_codigo';
UPDATE configuraciones SET descripcion = 'Formato de visualización de fechas' WHERE clave = 'formato_fecha';
UPDATE configuraciones SET descripcion = 'Registros por página en listados' WHERE clave = 'registros_por_pagina';
UPDATE configuraciones SET descripcion = 'Habilitar animaciones de interfaz' WHERE clave = 'animaciones';
UPDATE configuraciones SET descripcion = 'Días de retención de logs de auditoría' WHERE clave = 'retencion_logs';
UPDATE configuraciones SET descripcion = 'Generar alerta al alcanzar stock mínimo' WHERE clave = 'reorden_automatico';
UPDATE configuraciones SET descripcion = 'Alertas de accesos fallidos y cambios' WHERE clave = 'alertas_seguridad';
UPDATE configuraciones SET descripcion = 'Configuración del servidor de correo saliente' WHERE clave = 'smtp_activo';

-- === PERMISOS ===
UPDATE permisos SET descripcion = 'Ver categorías' WHERE modulo = 'categorias' AND accion = 'ver';
UPDATE permisos SET descripcion = 'Crear categorías' WHERE modulo = 'categorias' AND accion = 'crear';
UPDATE permisos SET descripcion = 'Editar categorías' WHERE modulo = 'categorias' AND accion = 'editar';
UPDATE permisos SET descripcion = 'Eliminar categorías' WHERE modulo = 'categorias' AND accion = 'eliminar';
UPDATE permisos SET descripcion = 'Ver configuración' WHERE modulo = 'configuracion' AND accion = 'ver';
UPDATE permisos SET descripcion = 'Editar configuración' WHERE modulo = 'configuracion' AND accion = 'editar';
UPDATE permisos SET descripcion = 'Ver logs de seguridad' WHERE modulo = 'seguridad' AND accion = 'ver';

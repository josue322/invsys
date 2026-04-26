-- =====================================================
-- InvSys - Sistema de Gestión de Inventario
-- Script SQL Completo
-- MySQL 8+
-- =====================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS `invsys_db`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `invsys_db`;

-- =====================================================
-- TABLA: roles
-- =====================================================
CREATE TABLE IF NOT EXISTS `roles` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(50) NOT NULL,
    `descripcion` VARCHAR(255) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_roles_nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: permisos
-- =====================================================
CREATE TABLE IF NOT EXISTS `permisos` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `modulo` VARCHAR(50) NOT NULL,
    `accion` VARCHAR(50) NOT NULL,
    `descripcion` VARCHAR(255) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_permisos_modulo_accion` (`modulo`, `accion`),
    INDEX `idx_permisos_modulo` (`modulo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: rol_permiso
-- =====================================================
CREATE TABLE IF NOT EXISTS `rol_permiso` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `rol_id` INT UNSIGNED NOT NULL,
    `permiso_id` INT UNSIGNED NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_rol_permiso` (`rol_id`, `permiso_id`),
    CONSTRAINT `fk_rp_rol` FOREIGN KEY (`rol_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_rp_permiso` FOREIGN KEY (`permiso_id`) REFERENCES `permisos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: usuarios
-- =====================================================
CREATE TABLE IF NOT EXISTS `usuarios` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `rol_id` INT UNSIGNED NOT NULL,
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    `intentos_fallidos` INT UNSIGNED NOT NULL DEFAULT 0,
    `bloqueado_hasta` DATETIME DEFAULT NULL,
    `ultimo_login` DATETIME DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_usuarios_email` (`email`),
    INDEX `idx_usuarios_rol` (`rol_id`),
    INDEX `idx_usuarios_activo` (`activo`),
    CONSTRAINT `fk_usuarios_rol` FOREIGN KEY (`rol_id`) REFERENCES `roles`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: categorias
-- =====================================================
CREATE TABLE IF NOT EXISTS `categorias` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(100) NOT NULL,
    `descripcion` TEXT DEFAULT NULL,
    `activa` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_categorias_nombre` (`nombre`),
    INDEX `idx_categorias_activa` (`activa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: proveedores
-- =====================================================
CREATE TABLE IF NOT EXISTS `proveedores` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(150) NOT NULL,
    `ruc_dni` VARCHAR(20) DEFAULT NULL,
    `contacto` VARCHAR(100) DEFAULT NULL,
    `telefono` VARCHAR(50) DEFAULT NULL,
    `email` VARCHAR(150) DEFAULT NULL,
    `direccion` TEXT DEFAULT NULL,
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_proveedores_ruc_dni` (`ruc_dni`),
    INDEX `idx_proveedores_activo` (`activo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: ubicaciones
-- =====================================================
CREATE TABLE IF NOT EXISTS `ubicaciones` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(100) NOT NULL,
    `descripcion` VARCHAR(255) DEFAULT NULL,
    `activa` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_ubicaciones_nombre` (`nombre`),
    INDEX `idx_ubicaciones_activa` (`activa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: productos
-- =====================================================
CREATE TABLE IF NOT EXISTS `productos` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(200) NOT NULL,
    `descripcion` TEXT DEFAULT NULL,
    `sku` VARCHAR(16) NOT NULL,
    `categoria_id` INT UNSIGNED DEFAULT NULL,
    `unidad_medida` VARCHAR(20) NOT NULL DEFAULT 'Unidad',
    `ubicacion_id` INT UNSIGNED DEFAULT NULL,
    `precio` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `stock` INT NOT NULL DEFAULT 0,
    `stock_minimo` INT UNSIGNED NOT NULL DEFAULT 5,
    `imagen` VARCHAR(255) DEFAULT NULL COMMENT 'Nombre del archivo de imagen del producto',
    `es_perecedero` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Indica si el producto exige lotes con vencimiento',
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_productos_sku` (`sku`),
    INDEX `idx_productos_categoria` (`categoria_id`),
    INDEX `idx_productos_ubicacion` (`ubicacion_id`),
    INDEX `idx_productos_stock` (`stock`),
    INDEX `idx_productos_activo` (`activo`),
    CONSTRAINT `fk_productos_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_productos_ubicacion` FOREIGN KEY (`ubicacion_id`) REFERENCES `ubicaciones`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: lotes
-- =====================================================
CREATE TABLE IF NOT EXISTS `lotes` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `producto_id` INT UNSIGNED NOT NULL,
    `numero_lote` VARCHAR(50) NOT NULL,
    `cantidad_inicial` INT NOT NULL,
    `stock_actual` INT NOT NULL,
    `fecha_vencimiento` DATE DEFAULT NULL,
    `proveedor_id` INT UNSIGNED DEFAULT NULL,
    `estado` ENUM('disponible','agotado','vencido','aislado') NOT NULL DEFAULT 'disponible',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_lotes_producto_numero` (`producto_id`, `numero_lote`),
    INDEX `idx_lotes_producto` (`producto_id`),
    INDEX `idx_lotes_proveedor` (`proveedor_id`),
    INDEX `idx_lotes_vencimiento` (`fecha_vencimiento`),
    INDEX `idx_lotes_estado` (`estado`),
    CONSTRAINT `fk_lotes_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_lotes_proveedor` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: movimientos
-- =====================================================
CREATE TABLE IF NOT EXISTS `movimientos` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `producto_id` INT UNSIGNED NOT NULL,
    `usuario_id` INT UNSIGNED NOT NULL,
    `lote_id` INT UNSIGNED DEFAULT NULL,
    `proveedor_id` INT UNSIGNED DEFAULT NULL,
    `destino` VARCHAR(150) DEFAULT NULL,
    `tipo` ENUM('entrada','salida','ajuste') NOT NULL,
    `cantidad` INT NOT NULL,
    `stock_anterior` INT NOT NULL,
    `stock_nuevo` INT NOT NULL,
    `referencia` VARCHAR(100) DEFAULT NULL,
    `observaciones` TEXT DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_movimientos_producto` (`producto_id`),
    INDEX `idx_movimientos_usuario` (`usuario_id`),
    INDEX `idx_movimientos_lote` (`lote_id`),
    INDEX `idx_movimientos_proveedor` (`proveedor_id`),
    INDEX `idx_movimientos_tipo` (`tipo`),
    INDEX `idx_movimientos_fecha` (`created_at`),
    CONSTRAINT `fk_movimientos_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos`(`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_movimientos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_movimientos_lote` FOREIGN KEY (`lote_id`) REFERENCES `lotes`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_movimientos_proveedor` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: alertas
-- =====================================================
CREATE TABLE IF NOT EXISTS `alertas` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `producto_id` INT UNSIGNED NOT NULL,
    `tipo` ENUM('stock_minimo','stock_agotado','otro') NOT NULL DEFAULT 'stock_minimo',
    `mensaje` VARCHAR(500) NOT NULL,
    `leida` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_alertas_producto` (`producto_id`),
    INDEX `idx_alertas_leida` (`leida`),
    INDEX `idx_alertas_tipo` (`tipo`),
    CONSTRAINT `fk_alertas_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: configuraciones
-- =====================================================
CREATE TABLE IF NOT EXISTS `configuraciones` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `clave` VARCHAR(100) NOT NULL,
    `valor` TEXT DEFAULT NULL,
    `descripcion` VARCHAR(255) DEFAULT NULL,
    `tipo` ENUM('text','number','boolean','json') NOT NULL DEFAULT 'text',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_configuraciones_clave` (`clave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: logs
-- =====================================================
CREATE TABLE IF NOT EXISTS `logs` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `usuario_id` INT UNSIGNED DEFAULT NULL,
    `accion` VARCHAR(100) NOT NULL,
    `modulo` VARCHAR(50) NOT NULL,
    `detalles` TEXT DEFAULT NULL,
    `ip` VARCHAR(45) DEFAULT NULL,
    `user_agent` VARCHAR(500) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_logs_usuario` (`usuario_id`),
    INDEX `idx_logs_modulo` (`modulo`),
    INDEX `idx_logs_fecha` (`created_at`),
    CONSTRAINT `fk_logs_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: sesiones
-- =====================================================
CREATE TABLE IF NOT EXISTS `sesiones` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `usuario_id` INT UNSIGNED NOT NULL,
    `token` VARCHAR(255) NOT NULL,
    `ip` VARCHAR(45) DEFAULT NULL,
    `user_agent` VARCHAR(500) DEFAULT NULL,
    `inicio` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `ultimo_acceso` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `activa` TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`),
    INDEX `idx_sesiones_usuario` (`usuario_id`),
    INDEX `idx_sesiones_token` (`token`),
    INDEX `idx_sesiones_activa` (`activa`),
    CONSTRAINT `fk_sesiones_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: user_settings
-- =====================================================
CREATE TABLE IF NOT EXISTS `user_settings` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `usuario_id` INT UNSIGNED NOT NULL,
    `clave` VARCHAR(100) NOT NULL,
    `valor` TEXT DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_user_settings` (`usuario_id`, `clave`),
    CONSTRAINT `fk_us_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: precio_historial
-- =====================================================
CREATE TABLE IF NOT EXISTS `precio_historial` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `producto_id` INT UNSIGNED NOT NULL,
    `precio_anterior` DECIMAL(12,2) NOT NULL,
    `precio_nuevo` DECIMAL(12,2) NOT NULL,
    `usuario_id` INT UNSIGNED DEFAULT NULL,
    `motivo` VARCHAR(255) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_ph_producto` (`producto_id`),
    INDEX `idx_ph_fecha` (`created_at`),
    CONSTRAINT `fk_ph_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_ph_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: conteos (Sesiones de conteo físico)
-- =====================================================
CREATE TABLE IF NOT EXISTS `conteos` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(150) NOT NULL COMMENT 'Ej: Conteo Mensual Abril 2026',
    `descripcion` TEXT DEFAULT NULL,
    `estado` ENUM('abierto','cerrado','ajustado') NOT NULL DEFAULT 'abierto',
    `filtro_tipo` ENUM('todos','categoria','ubicacion') NOT NULL DEFAULT 'todos',
    `filtro_id` INT UNSIGNED DEFAULT NULL COMMENT 'ID de categoría o ubicación filtrada',
    `usuario_id` INT UNSIGNED NOT NULL COMMENT 'Quién creó la sesión',
    `cerrado_por` INT UNSIGNED DEFAULT NULL,
    `fecha_cierre` DATETIME DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_conteos_estado` (`estado`),
    INDEX `idx_conteos_fecha` (`created_at`),
    CONSTRAINT `fk_conteos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_conteos_cerrado` FOREIGN KEY (`cerrado_por`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: conteo_detalle (Detalle por producto)
-- =====================================================
CREATE TABLE IF NOT EXISTS `conteo_detalle` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `conteo_id` INT UNSIGNED NOT NULL,
    `producto_id` INT UNSIGNED NOT NULL,
    `stock_sistema` INT NOT NULL COMMENT 'Stock al momento de crear el conteo',
    `stock_fisico` INT DEFAULT NULL COMMENT 'Cantidad contada por el operador',
    `diferencia` INT GENERATED ALWAYS AS (COALESCE(`stock_fisico`, 0) - `stock_sistema`) STORED,
    `observaciones` TEXT DEFAULT NULL,
    `contado_por` INT UNSIGNED DEFAULT NULL,
    `contado_at` DATETIME DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_conteo_producto` (`conteo_id`, `producto_id`),
    INDEX `idx_cd_conteo` (`conteo_id`),
    INDEX `idx_cd_producto` (`producto_id`),
    CONSTRAINT `fk_cd_conteo` FOREIGN KEY (`conteo_id`) REFERENCES `conteos`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_cd_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos`(`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_cd_contado` FOREIGN KEY (`contado_por`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- DATOS SEED
-- =====================================================

-- Roles
INSERT INTO `roles` (`id`, `nombre`, `descripcion`) VALUES
(1, 'Admin', 'Administrador del sistema con acceso total'),
(2, 'Supervisor', 'Supervisor con acceso a reportes y gestión'),
(3, 'Operador', 'Operador con acceso básico a inventario');

-- Permisos (módulo + acción)
INSERT INTO `permisos` (`modulo`, `accion`, `descripcion`) VALUES
-- Dashboard
('dashboard', 'ver', 'Ver dashboard principal'),
-- Productos
('productos', 'ver', 'Ver listado de productos'),
('productos', 'crear', 'Crear nuevos productos'),
('productos', 'editar', 'Editar productos existentes'),
('productos', 'eliminar', 'Eliminar productos'),
-- Categorías
('categorias', 'ver', 'Ver categorías'),
('categorias', 'crear', 'Crear categorías'),
('categorias', 'editar', 'Editar categorías'),
('categorias', 'eliminar', 'Eliminar categorías'),
-- Proveedores
('proveedores', 'ver', 'Ver proveedores'),
('proveedores', 'crear', 'Crear proveedores'),
('proveedores', 'editar', 'Editar proveedores'),
('proveedores', 'eliminar', 'Eliminar proveedores'),
-- Ubicaciones
('ubicaciones', 'ver', 'Ver ubicaciones'),
('ubicaciones', 'crear', 'Crear ubicaciones'),
('ubicaciones', 'editar', 'Editar ubicaciones'),
('ubicaciones', 'eliminar', 'Eliminar ubicaciones'),
-- Movimientos
('movimientos', 'ver', 'Ver movimientos de inventario'),
('movimientos', 'crear', 'Registrar movimientos'),
-- Alertas
('alertas', 'ver', 'Ver alertas del sistema'),
('alertas', 'gestionar', 'Marcar alertas como leídas'),
-- Reportes
('reportes', 'ver', 'Ver reportes'),
('reportes', 'exportar', 'Exportar reportes'),
-- Usuarios
('usuarios', 'ver', 'Ver usuarios'),
('usuarios', 'crear', 'Crear usuarios'),
('usuarios', 'editar', 'Editar usuarios'),
('usuarios', 'eliminar', 'Eliminar usuarios'),
-- Configuración
('configuracion', 'ver', 'Ver configuración'),
('configuracion', 'editar', 'Editar configuración'),
-- Seguridad
('seguridad', 'ver', 'Ver logs de seguridad'),
('seguridad', 'gestionar', 'Gestionar seguridad');

-- Asignar TODOS los permisos al Admin
INSERT INTO `rol_permiso` (`rol_id`, `permiso_id`)
SELECT 1, `id` FROM `permisos`;

-- Asignar permisos al Supervisor (todo excepto usuarios, configuración y seguridad)
INSERT INTO `rol_permiso` (`rol_id`, `permiso_id`)
SELECT 2, `id` FROM `permisos`
WHERE `modulo` IN ('dashboard', 'productos', 'categorias', 'proveedores', 'ubicaciones', 'movimientos', 'alertas', 'reportes');

-- Asignar permisos al Operador (solo ver y operaciones básicas)
INSERT INTO `rol_permiso` (`rol_id`, `permiso_id`)
SELECT 3, `id` FROM `permisos`
WHERE (`modulo` = 'dashboard' AND `accion` = 'ver')
   OR (`modulo` = 'productos' AND `accion` = 'ver')
   OR (`modulo` = 'movimientos' AND `accion` IN ('ver', 'crear'))
   OR (`modulo` = 'alertas' AND `accion` = 'ver');

-- Usuario Admin por defecto (password: Admin123!)
INSERT INTO `usuarios` (`nombre`, `email`, `password`, `rol_id`, `activo`) VALUES
('Administrador', 'admin@invsys.com', '$2y$10$.74.6ymWfm3gn7QmGRGzReb3EGMBptyIiI6QvQ1nE3SscBfNQ.lLG', 1, 1),
('Supervisor Demo', 'supervisor@invsys.com', '$2y$10$.74.6ymWfm3gn7QmGRGzReb3EGMBptyIiI6QvQ1nE3SscBfNQ.lLG', 2, 1),
('Operador Demo', 'operador@invsys.com', '$2y$10$.74.6ymWfm3gn7QmGRGzReb3EGMBptyIiI6QvQ1nE3SscBfNQ.lLG', 3, 1);

-- Categorías
INSERT INTO `categorias` (`nombre`, `descripcion`) VALUES
('Electrónica', 'Dispositivos y componentes electrónicos'),
('Oficina', 'Material y suministros de oficina'),
('Herramientas', 'Herramientas manuales y eléctricas'),
('Limpieza', 'Productos de limpieza e higiene'),
('Seguridad', 'Equipos de protección y seguridad');

-- Proveedores
INSERT INTO `proveedores` (`nombre`, `ruc_dni`, `contacto`, `telefono`, `email`, `direccion`) VALUES
('Tech Supp S.A.C.', '20123456789', 'Carlos Ruiz', '999888777', 'ventas@techsupp.com', 'Av. Tecnológica 123, Lima'),
('OfficeCorp', '20987654321', 'María López', '999111222', 'contacto@officecorp.com', 'Calle Los Pinos 456, Arequipa'),
('CleanPro Distribuciones', '20555666777', 'Jorge Pérez', '999333444', 'pedidos@cleanpro.com', 'Parque Industrial Sur Lote 8');

-- Ubicaciones
INSERT INTO `ubicaciones` (`nombre`, `descripcion`) VALUES
('Pasillo 1 - Estante A', 'Electrónica de alto valor'),
('Pasillo 2 - Estante B', 'Insumos generales de oficina'),
('Pasillo 3 - Zona C', 'Herramientas pesadas'),
('Cuarto Frío 1', 'Productos perecederos y químicos');

-- Productos de ejemplo
INSERT INTO `productos` (`id`, `nombre`, `descripcion`, `sku`, `categoria_id`, `unidad_medida`, `ubicacion_id`, `precio`, `stock`, `stock_minimo`, `imagen`, `es_perecedero`) VALUES
(1, 'Laptop HP ProBook 450', 'Laptop empresarial HP, i7, 16GB', 'ELEC-001', 1, 'Unidad', 1, 18500.00, 12, 3, NULL, 0),
(2, 'Monitor Dell 24\"', 'Monitor Dell P2422H 24 pulgadas', 'ELEC-002', 1, 'Unidad', 1, 5200.00, 25, 5, NULL, 0),
(3, 'Resma Papel A4', 'Resma de papel bond A4 75g', 'OFIC-001', 2, 'Paquete', 2, 120.00, 200, 50, NULL, 0),
(4, 'Detergente Industrial 5L', 'Detergente líquido industrial', 'LIMP-001', 4, 'Galon', 4, 95.00, 30, 10, NULL, 1),
(5, 'Guantes de Latex x100', 'Caja de 100 guantes talla M', 'LIMP-002', 4, 'Caja', 4, 150.00, 50, 15, NULL, 1);

-- Lotes
INSERT INTO `lotes` (`producto_id`, `numero_lote`, `cantidad_inicial`, `stock_actual`, `fecha_vencimiento`, `proveedor_id`) VALUES
(4, 'L-DET-1025', 50, 30, '2026-12-15', 3),
(5, 'L-GLA-0824', 100, 50, '2027-06-30', 3);

-- Alertas
INSERT INTO `alertas` (`producto_id`, `tipo`, `mensaje`) VALUES
(4, 'stock_minimo', 'El producto "Detergente Industrial 5L" tiene stock cercano al mínimo.');

-- Configuraciones del sistema
INSERT INTO `configuraciones` (`clave`, `valor`, `descripcion`, `tipo`) VALUES
('nombre_sistema', 'InvSys', 'Nombre del sistema', 'text'),
('color_principal', '#6366f1', 'Color principal de la interfaz', 'text'),
('logo', '', 'Archivo del logo personalizado', 'text'),
('stock_minimo_global', '5', 'Stock mínimo global por defecto para nuevos productos', 'number'),
('intentos_login_max', '5', 'Número máximo de intentos de login fallidos', 'number'),
('tiempo_bloqueo_minutos', '15', 'Tiempo de bloqueo por intentos fallidos (minutos)', 'number'),
('session_lifetime', '3600', 'Tiempo de vida de la sesión en segundos', 'number'),
('alertas_email', '0', 'Enviar alertas por correo electrónico', 'boolean'),
('alertas_seguridad', '1', 'Alertas de accesos fallidos y cambios', 'boolean'),
('moneda_simbolo', '$', 'Símbolo de la moneda', 'text'),
('moneda_codigo', 'MXN', 'Código ISO de la moneda', 'text'),
('zona_horaria', 'America/Lima', 'Zona horaria del sistema', 'text'),
('formato_fecha', 'DD/MM/YYYY', 'Formato de visualización de fechas', 'text'),
('registros_por_pagina', '15', 'Registros por página en listados', 'number'),
('tema_defecto', 'light', 'Tema por defecto del sistema (light/dark)', 'text'),
('sidebar_colapsable', '1', 'Permitir contraer el sidebar', 'boolean'),
('densidad_compacta', '0', 'Modo de densidad compacta', 'boolean'),
('animaciones', '1', 'Habilitar animaciones de interfaz', 'boolean'),
('permitir_stock_negativo', '0', 'Permitir stock negativo en salidas', 'boolean'),
('reorden_automatico', '1', 'Generar alerta al alcanzar stock mínimo', 'boolean'),
('retencion_logs', '90', 'Días de retención de logs de auditoría', 'number'),
('permitir_registro', '0', 'Permitir registro público de nuevos usuarios', 'boolean'),
('rol_registro_publico', '3', 'ID del rol asignado a nuevos usuarios registrados públicamente', 'number');

-- Configuración de preferencias de usuarios demo
INSERT INTO `user_settings` (`usuario_id`, `clave`, `valor`) VALUES
(1, 'tema', 'light'),
(2, 'tema', 'light'),
(3, 'tema', 'light');

-- Algunos movimientos de ejemplo
INSERT INTO `movimientos` (`producto_id`, `usuario_id`, `lote_id`, `proveedor_id`, `destino`, `tipo`, `cantidad`, `stock_anterior`, `stock_nuevo`, `referencia`, `observaciones`) VALUES
(1, 1, NULL, 1, NULL, 'entrada', 12, 0, 12, 'OC-2025-001', 'Stock inicial - Compra de laptops'),
(2, 1, NULL, 1, NULL, 'entrada', 25, 0, 25, 'OC-2025-001', 'Stock inicial - Compra de monitores'),
(3, 1, NULL, 2, NULL, 'entrada', 200, 0, 200, 'OC-2025-002', 'Stock inicial - Resmas de papel'),
(4, 1, 1, 3, NULL, 'entrada', 50, 0, 50, 'OC-2025-004', 'Ingreso de detergente con Lote'),
(4, 1, 1, NULL, 'Área de Limpieza Central', 'salida', 20, 50, 30, 'REQ-2025-001', 'Despacho para uso interno');

COMMIT;

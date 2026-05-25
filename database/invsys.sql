-- =====================================================
-- InvSys - Base de Datos Unificada de Producción
-- Generado automáticamente para despliegue en hosting
-- Fecha: 2026-05-25 03:17:36
-- =====================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;

-- -----------------------------------------------------
-- TABLA: alertas
-- -----------------------------------------------------
DROP TABLE IF EXISTS `alertas`;
CREATE TABLE `alertas` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `producto_id` int(10) unsigned NOT NULL,
  `tipo` enum('stock_minimo','stock_agotado','otro') NOT NULL DEFAULT 'stock_minimo',
  `mensaje` varchar(500) NOT NULL,
  `leida` tinyint(1) NOT NULL DEFAULT 0,
  `notificado_correo` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_alertas_producto` (`producto_id`),
  KEY `idx_alertas_leida` (`leida`),
  KEY `idx_alertas_tipo` (`tipo`),
  KEY `idx_alertas_notificado_correo` (`notificado_correo`),
  CONSTRAINT `fk_alertas_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `alertas` (`id`, `producto_id`, `tipo`, `mensaje`, `leida`, `notificado_correo`, `created_at`, `updated_at`) VALUES
('1', '6', 'stock_minimo', 'El producto \"Mouse Inalámbrico Logitech M280\" (SKU: MOU-LOG-01) tiene stock bajo (5 unidades). Stock mínimo: 5.', '0', '1', '2026-05-13 19:57:03', '2026-05-19 20:44:42'),
('2', '7', 'otro', '⏰ LOTE POR VENCER: El Lote 001-RB de \"Red Bull\" (SKU: 9002490100070) vence el 30/05/2026 (17 días restantes).', '0', '1', '2026-05-13 20:02:38', '2026-05-19 20:44:42'),
('3', '7', 'stock_minimo', 'El producto \"Red Bull\" (SKU: 9002490100070) tiene stock bajo (1 unidades). Stock mínimo: 5.', '0', '1', '2026-05-14 13:22:52', '2026-05-19 20:44:42'),
('4', '12', 'otro', '⏰ LOTE POR VENCER: El Lote 001 A de \"Paquete de Agua Mineral Sin Gas (Botella 500ml)\" (SKU: CAF-AGU-001) vence el 30/05/2026 (15 días restantes).', '0', '1', '2026-05-15 14:00:53', '2026-05-19 20:44:42'),
('5', '13', 'stock_agotado', '⚠️ STOCK AGOTADO: El producto \"Josue\" (SKU: JLC-001) no tiene existencias.', '0', '1', '2026-05-16 12:56:46', '2026-05-19 20:44:42'),
('6', '13', 'stock_minimo', 'El producto \"Josue\" (SKU: JLC-001) tiene stock bajo (1 unidades). Stock mínimo: 5.', '0', '1', '2026-05-16 12:57:43', '2026-05-19 20:44:42'),
('7', '13', 'otro', '⏰ LOTE POR VENCER: El Lote 001-RB de \"Josue\" (SKU: JLC-001) vence el 29/05/2026 (13 días restantes).', '0', '1', '2026-05-16 13:16:46', '2026-05-19 20:44:42'),
('8', '14', 'otro', '⏰ LOTE POR VENCER: El Lote LOT 002 de \"daaa\" (SKU: 0051G1) vence el 20/05/2026 (4 días restantes).', '0', '1', '2026-05-16 14:51:58', '2026-05-19 20:44:42'),
('9', '19', 'stock_minimo', 'El producto \"Pc Aio Hp Proone 440\" (SKU: AIO1) tiene stock bajo (2 unidades). Stock mínimo: 5.', '0', '1', '2026-05-18 17:46:44', '2026-05-19 20:44:42'),
('12', '16', 'otro', '⏰ LOTE POR VENCER: El Lote LOT 003 de \"Café en Grano Tostado 1Kg\" (SKU: ALIM-001) vence el 22/06/2026 (29 días restantes).', '0', '0', '2026-05-24 14:35:56', NULL);

-- -----------------------------------------------------
-- TABLA: categorias
-- -----------------------------------------------------
DROP TABLE IF EXISTS `categorias`;
CREATE TABLE `categorias` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `activa` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_categorias_nombre` (`nombre`),
  KEY `idx_categorias_activa` (`activa`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categorias` (`id`, `nombre`, `descripcion`, `activa`, `created_at`, `updated_at`) VALUES
('1', 'Equipos de Cómputo', 'Laptops, monitores y accesorios tecnológicos', '1', '2026-05-13 15:11:35', NULL),
('2', 'Insumos de Oficina', 'Papelería, bolígrafos y material de escritorio', '1', '2026-05-13 15:11:35', NULL),
('3', 'Material de Limpieza', 'Detergentes, escobas y productos de aseo', '1', '2026-05-13 15:11:35', NULL),
('4', 'Herramientas Básicas', 'Herramientas manuales y eléctricas menores', '1', '2026-05-13 15:11:35', NULL),
('5', 'Seguridad Industrial', 'Equipos de protección personal (EPP)', '1', '2026-05-13 15:11:35', NULL),
('6', 'Insumos de Cafetería y Comedor', 'Productos consumibles, alimentos no perecederos y bebidas para abastecimiento de áreas comunes, salas de reuniones y comedores.', '1', '2026-05-15 12:28:26', NULL),
('7', 'Mobiliario', 'Sillas, escritorios, estantes y demás mobiliario para oficinas.', '1', '2026-05-18 12:07:59', NULL),
('8', 'Alimentos y Bebidas', 'Insumos consumibles, snacks y cafetería para uso interno.', '1', '2026-05-18 12:08:30', NULL),
('9', 'Repuestos Informáticos', 'Piezas, cables y refacciones para mantenimiento de equipos de cómputo.', '1', '2026-05-18 12:08:45', NULL);

-- -----------------------------------------------------
-- TABLA: configuraciones
-- -----------------------------------------------------
DROP TABLE IF EXISTS `configuraciones`;
CREATE TABLE `configuraciones` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `clave` varchar(100) NOT NULL,
  `valor` text DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `tipo` enum('text','number','boolean','json') NOT NULL DEFAULT 'text',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_configuraciones_clave` (`clave`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `configuraciones` (`id`, `clave`, `valor`, `descripcion`, `tipo`, `created_at`, `updated_at`) VALUES
('1', 'nombre_sistema', 'InvSys', 'Nombre del sistema', 'text', '2026-04-20 23:48:03', NULL),
('2', 'color_principal', '#6366f1', 'Color principal de la interfaz', 'text', '2026-04-20 23:48:03', NULL),
('3', 'logo', 'logo_1779380263.png', 'Archivo del logo personalizado', 'text', '2026-04-20 23:48:03', '2026-05-21 11:17:43'),
('4', 'stock_minimo_global', '5', 'Stock mínimo global por defecto para nuevos productos', 'number', '2026-04-20 23:48:03', '2026-05-14 15:56:16'),
('5', 'intentos_login_max', '5', 'Número máximo de intentos de login fallidos', 'number', '2026-04-20 23:48:03', '2026-04-25 13:36:34'),
('6', 'tiempo_bloqueo_minutos', '15', 'Tiempo de bloqueo por intentos fallidos (minutos)', 'number', '2026-04-20 23:48:03', NULL),
('7', 'session_lifetime', '3600', 'Tiempo de vida de la sesión en segundos', 'number', '2026-04-20 23:48:03', '2026-04-25 13:36:34'),
('8', 'alertas_email', '1', 'Enviar alertas por correo electrónico', 'boolean', '2026-04-20 23:48:03', '2026-05-04 14:51:27'),
('9', 'alertas_seguridad', '1', 'Alertas de accesos fallidos y cambios', 'boolean', '2026-04-20 23:48:03', NULL),
('10', 'moneda_simbolo', 'S/ ', 'Símbolo de la moneda', 'text', '2026-04-20 23:48:03', '2026-04-25 13:36:34'),
('11', 'moneda_codigo', 'PEN', 'Código ISO de la moneda', 'text', '2026-04-20 23:48:03', '2026-04-25 13:36:34'),
('12', 'zona_horaria', 'America/Lima', 'Zona horaria del sistema', 'text', '2026-04-20 23:48:03', NULL),
('13', 'formato_fecha', 'DD/MM/YYYY', 'Formato de visualización de fechas', 'text', '2026-04-20 23:48:03', '2026-04-25 13:36:34'),
('14', 'registros_por_pagina', '15', 'Registros por página en listados', 'number', '2026-04-20 23:48:03', '2026-05-13 20:15:49'),
('15', 'tema_defecto', 'light', 'Tema por defecto del sistema (light/dark)', 'text', '2026-04-20 23:48:03', '2026-05-13 22:31:41'),
('16', 'sidebar_colapsable', '1', 'Permitir contraer el sidebar', 'boolean', '2026-04-20 23:48:03', NULL),
('17', 'densidad_compacta', '0', 'Modo de densidad compacta', 'boolean', '2026-04-20 23:48:03', '2026-05-04 14:50:52'),
('18', 'animaciones', '1', 'Habilitar animaciones de interfaz', 'boolean', '2026-04-20 23:48:03', NULL),
('19', 'permitir_stock_negativo', '0', 'Permitir stock negativo en salidas', 'boolean', '2026-04-20 23:48:03', NULL),
('20', 'reorden_automatico', '1', 'Generar alerta al alcanzar stock mínimo', 'boolean', '2026-04-20 23:48:03', '2026-04-25 13:36:34'),
('21', 'retencion_logs', '90', 'Días de retención de logs de auditoría', 'number', '2026-04-20 23:48:03', '2026-04-25 13:36:34'),
('22', 'permitir_registro', '1', 'Permitir registro público de nuevos usuarios', 'boolean', '2026-04-20 23:48:03', '2026-04-25 13:36:34'),
('23', 'smtp_activo', '1', 'Configuraci?n del servidor de correo saliente', 'text', '2026-04-24 19:35:18', '2026-04-27 12:21:18'),
('24', 'smtp_host', 'smtp.gmail.com', NULL, 'text', '2026-04-24 19:35:18', NULL),
('25', 'smtp_port', '587', NULL, 'text', '2026-04-24 19:35:18', NULL),
('26', 'smtp_encryption', 'tls', NULL, 'text', '2026-04-24 19:35:18', NULL),
('27', 'smtp_auth', '1', NULL, 'text', '2026-04-24 19:35:18', NULL),
('28', 'smtp_username', 'josuexd123lc@gmail.com', NULL, 'text', '2026-04-24 19:35:18', NULL),
('29', 'smtp_password', 'kzws hvpa oowb tewo', NULL, 'text', '2026-04-24 19:35:18', NULL),
('30', 'mail_from_address', 'josuexd123lc@gmail.com', NULL, 'text', '2026-04-24 19:35:18', NULL),
('31', 'mail_from_name', 'InvSys', NULL, 'text', '2026-04-24 19:35:18', NULL),
('32', 'rol_registro_publico', '3', 'ID del rol asignado a nuevos usuarios registrados públicamente', 'number', '2026-04-25 13:02:30', '2026-04-25 13:36:34'),
('33', 'whatsapp_enabled', '1', 'Habilitar notificaciones por WhatsApp (1=Sí, 0=No)', 'boolean', '2026-05-05 17:42:22', '2026-05-05 18:06:25'),
('37', 'whatsapp_phone', '+51931993019', 'Número de teléfono del administrador con código de país (ej: +50588887777)', '', '2026-05-08 12:39:09', '2026-05-08 13:48:16'),
('38', 'whatsapp_apikey', '7424396', 'API Key de CallMeBot (obtenida al registrarse)', '', '2026-05-08 12:39:09', '2026-05-08 13:48:16');

-- -----------------------------------------------------
-- TABLA: conteo_detalle
-- -----------------------------------------------------
DROP TABLE IF EXISTS `conteo_detalle`;
CREATE TABLE `conteo_detalle` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `conteo_id` int(10) unsigned NOT NULL,
  `producto_id` int(10) unsigned NOT NULL,
  `stock_sistema` int(11) NOT NULL COMMENT 'Stock al momento de crear el conteo',
  `stock_fisico` int(11) DEFAULT NULL COMMENT 'Cantidad contada por el operador',
  `diferencia` int(11) GENERATED ALWAYS AS (coalesce(`stock_fisico`,0) - `stock_sistema`) STORED,
  `observaciones` text DEFAULT NULL,
  `contado_por` int(10) unsigned DEFAULT NULL,
  `contado_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_conteo_producto` (`conteo_id`,`producto_id`),
  KEY `idx_cd_conteo` (`conteo_id`),
  KEY `idx_cd_producto` (`producto_id`),
  KEY `fk_cd_contado` (`contado_por`),
  CONSTRAINT `fk_cd_contado` FOREIGN KEY (`contado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_cd_conteo` FOREIGN KEY (`conteo_id`) REFERENCES `conteos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cd_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `conteo_detalle` (`id`, `conteo_id`, `producto_id`, `stock_sistema`, `stock_fisico`, `diferencia`, `observaciones`, `contado_por`, `contado_at`, `created_at`) VALUES
('1', '1', '4', '35', '40', '5', NULL, '1', '2026-04-23 20:01:29', '2026-04-23 20:01:11'),
('2', '1', '5', '20', '20', '0', NULL, '1', '2026-04-23 20:10:29', '2026-04-23 20:01:11'),
('3', '1', '6', '15', '15', '0', NULL, '1', '2026-04-23 20:10:29', '2026-04-23 20:01:11'),
('4', '1', '1', '12', '12', '0', NULL, '1', '2026-04-23 20:10:30', '2026-04-23 20:01:11'),
('5', '1', '7', '100', '99', '-1', NULL, '1', '2026-04-23 20:10:30', '2026-04-23 20:01:11'),
('6', '1', '8', '20', '20', '0', NULL, '1', '2026-04-23 20:10:31', '2026-04-23 20:01:11'),
('7', '1', '2', '25', '25', '0', NULL, '1', '2026-04-23 20:10:31', '2026-04-23 20:01:11'),
('8', '1', '3', '200', '200', '0', NULL, '1', '2026-04-23 20:10:32', '2026-04-23 20:01:11'),
('10', '2', '2', '50', NULL, '-50', NULL, NULL, NULL, '2026-05-13 20:50:09'),
('11', '2', '5', '40', NULL, '-40', NULL, NULL, NULL, '2026-05-13 20:50:09'),
('12', '2', '3', '30', NULL, '-30', NULL, NULL, NULL, '2026-05-13 20:50:09'),
('13', '2', '1', '20', NULL, '-20', NULL, NULL, NULL, '2026-05-13 20:50:09'),
('14', '2', '6', '15', NULL, '-15', NULL, NULL, NULL, '2026-05-13 20:50:09'),
('15', '2', '7', '100', NULL, '-100', NULL, NULL, NULL, '2026-05-13 20:50:09'),
('16', '2', '4', '20', NULL, '-20', NULL, NULL, NULL, '2026-05-13 20:50:09');

-- -----------------------------------------------------
-- TABLA: conteos
-- -----------------------------------------------------
DROP TABLE IF EXISTS `conteos`;
CREATE TABLE `conteos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL COMMENT 'Ej: Conteo Mensual Abril 2026',
  `descripcion` text DEFAULT NULL,
  `estado` enum('abierto','cerrado','ajustado') NOT NULL DEFAULT 'abierto',
  `filtro_tipo` enum('todos','categoria','ubicacion') NOT NULL DEFAULT 'todos',
  `filtro_id` int(10) unsigned DEFAULT NULL COMMENT 'ID de categor├¡a o ubicaci├│n filtrada',
  `usuario_id` int(10) unsigned NOT NULL COMMENT 'Qui├®n cre├│ la sesi├│n',
  `cerrado_por` int(10) unsigned DEFAULT NULL,
  `fecha_cierre` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_conteos_estado` (`estado`),
  KEY `idx_conteos_fecha` (`created_at`),
  KEY `fk_conteos_usuario` (`usuario_id`),
  KEY `fk_conteos_cerrado` (`cerrado_por`),
  CONSTRAINT `fk_conteos_cerrado` FOREIGN KEY (`cerrado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_conteos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `conteos` (`id`, `nombre`, `descripcion`, `estado`, `filtro_tipo`, `filtro_id`, `usuario_id`, `cerrado_por`, `fecha_cierre`, `created_at`, `updated_at`) VALUES
('1', 'MAY-2026', '', 'ajustado', 'todos', NULL, '4', '4', '2026-05-13 20:51:50', '2026-05-13 20:10:08', '2026-05-13 20:51:53'),
('2', 'test', '', 'cerrado', 'todos', NULL, '4', '4', '2026-05-13 20:50:57', '2026-05-13 20:50:09', '2026-05-13 20:50:57');

-- -----------------------------------------------------
-- TABLA: costo_historial
-- -----------------------------------------------------
DROP TABLE IF EXISTS `costo_historial`;
CREATE TABLE `costo_historial` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `producto_id` int(10) unsigned NOT NULL,
  `costo_anterior` decimal(12,2) NOT NULL,
  `costo_nuevo` decimal(12,2) NOT NULL,
  `usuario_id` int(10) unsigned DEFAULT NULL,
  `motivo` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_ph_producto` (`producto_id`),
  KEY `idx_ph_fecha` (`created_at`),
  KEY `fk_ph_usuario` (`usuario_id`),
  CONSTRAINT `fk_ph_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ph_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `costo_historial` (`id`, `producto_id`, `costo_anterior`, `costo_nuevo`, `usuario_id`, `motivo`, `created_at`) VALUES
('1', '7', '200.00', '900.00', '4', NULL, '2026-05-11 19:08:40'),
('2', '7', '900.00', '1200.00', '4', NULL, '2026-05-11 19:12:47'),
('3', '8', '322.00', '350.00', '4', NULL, '2026-05-11 19:40:25'),
('5', '2', '25.50', '25.00', '4', NULL, '2026-05-13 19:07:19');

-- -----------------------------------------------------
-- TABLA: departamentos
-- -----------------------------------------------------
DROP TABLE IF EXISTS `departamentos`;
CREATE TABLE `departamentos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `responsable` varchar(150) DEFAULT NULL,
  `centro_costo` varchar(50) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_departamentos_nombre` (`nombre`),
  KEY `idx_departamentos_activo` (`activo`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `departamentos` (`id`, `nombre`, `responsable`, `centro_costo`, `telefono`, `activo`, `created_at`, `updated_at`) VALUES
('1', 'Ventas y Marketing', 'Ana López', 'CC-101', '555-1001', '1', '2026-05-13 15:11:35', NULL),
('2', 'Tecnología de la Información', 'Carlos Gómez', 'CC-201', '555-2001', '1', '2026-05-13 15:11:35', NULL),
('3', 'Recursos Humanos', 'María Torres', 'CC-301', '555-3001', '1', '2026-05-13 15:11:35', NULL),
('4', 'Operaciones y Logística', 'Luis Rojas', 'CC-401', '555-4001', '1', '2026-05-13 15:11:35', NULL);

-- -----------------------------------------------------
-- TABLA: devolucion_detalles
-- -----------------------------------------------------
DROP TABLE IF EXISTS `devolucion_detalles`;
CREATE TABLE `devolucion_detalles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `devolucion_id` int(10) unsigned NOT NULL,
  `producto_id` int(10) unsigned NOT NULL,
  `lote_id` int(10) unsigned DEFAULT NULL,
  `cantidad` int(11) NOT NULL,
  `motivo` varchar(100) NOT NULL,
  `estado_producto` enum('bueno','da├▒ado') NOT NULL DEFAULT 'bueno',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_dev_det_devolucion` (`devolucion_id`),
  KEY `idx_dev_det_producto` (`producto_id`),
  KEY `idx_dev_det_lote` (`lote_id`),
  CONSTRAINT `fk_dev_det_devolucion` FOREIGN KEY (`devolucion_id`) REFERENCES `devoluciones` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_dev_det_lote` FOREIGN KEY (`lote_id`) REFERENCES `lotes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_dev_det_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `devolucion_detalles` (`id`, `devolucion_id`, `producto_id`, `lote_id`, `cantidad`, `motivo`, `estado_producto`, `created_at`) VALUES
('4', '4', '17', NULL, '1', 'prestamo', 'bueno', '2026-05-18 15:45:49');

-- -----------------------------------------------------
-- TABLA: devoluciones
-- -----------------------------------------------------
DROP TABLE IF EXISTS `devoluciones`;
CREATE TABLE `devoluciones` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `numero_devolucion` varchar(50) NOT NULL,
  `departamento_id` int(10) unsigned NOT NULL,
  `requisicion_id` int(10) unsigned DEFAULT NULL COMMENT 'Opcional: Requisici├│n de la que provienen',
  `usuario_id` int(10) unsigned NOT NULL COMMENT 'Usuario que registr├│ la devoluci├│n',
  `estado` enum('pendiente','aprobada','rechazada') NOT NULL DEFAULT 'pendiente',
  `fecha_solicitud` date NOT NULL,
  `fecha_procesamiento` datetime DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_devoluciones_numero` (`numero_devolucion`),
  KEY `idx_devoluciones_departamento` (`departamento_id`),
  KEY `idx_devoluciones_usuario` (`usuario_id`),
  KEY `idx_devoluciones_estado` (`estado`),
  KEY `fk_devoluciones_requisicion` (`requisicion_id`),
  CONSTRAINT `fk_devoluciones_departamento` FOREIGN KEY (`departamento_id`) REFERENCES `departamentos` (`id`),
  CONSTRAINT `fk_devoluciones_requisicion` FOREIGN KEY (`requisicion_id`) REFERENCES `requisiciones` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_devoluciones_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `devoluciones` (`id`, `numero_devolucion`, `departamento_id`, `requisicion_id`, `usuario_id`, `estado`, `fecha_solicitud`, `fecha_procesamiento`, `notas`, `created_at`, `updated_at`) VALUES
('4', 'DEV-0001', '2', '2', '4', 'aprobada', '2026-05-18', '2026-05-18 15:46:00', '', '2026-05-18 15:45:49', '2026-05-18 15:46:00');

-- -----------------------------------------------------
-- TABLA: logs
-- -----------------------------------------------------
DROP TABLE IF EXISTS `logs`;
CREATE TABLE `logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` int(10) unsigned DEFAULT NULL,
  `accion` varchar(100) NOT NULL,
  `modulo` varchar(50) NOT NULL,
  `detalles` text DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_logs_usuario` (`usuario_id`),
  KEY `idx_logs_modulo` (`modulo`),
  KEY `idx_logs_fecha` (`created_at`),
  CONSTRAINT `fk_logs_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=158 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `logs` (`id`, `usuario_id`, `accion`, `modulo`, `detalles`, `ip`, `user_agent`, `created_at`) VALUES
('1', '4', 'SOPORTE_TICKET', 'Ayuda', 'El usuario ha enviado un ticket de soporte: daaa', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 15:17:20'),
('2', '4', 'exportar_reporte_completo_pdf', 'reportes', 'Exportación PDF de reporte completo (5 productos, valor: S/ 21,035)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 15:24:28'),
('3', '4', 'exportar_reporte_completo_pdf', 'reportes', 'Exportación PDF de reporte completo (5 productos, valor: S/ 21,035)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 15:25:03'),
('4', '4', 'exportar_reporte_completo_pdf', 'reportes', 'Exportación PDF de reporte completo (5 productos, valor: S/ 21,035)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 15:25:03'),
('5', '4', 'exportar_reporte_completo_pdf', 'reportes', 'Exportación PDF de reporte completo (5 productos, valor: S/ 21,035)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 15:25:18'),
('6', '4', 'exportar_reporte_completo_pdf', 'reportes', 'Exportación PDF de reporte completo (5 productos, valor: S/ 21,035)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 15:25:20'),
('7', '4', 'exportar_inventario_csv', 'reportes', 'Exportación CSV de inventario general (5 productos)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 15:25:33'),
('8', '4', 'exportar_reporte_completo_pdf', 'reportes', 'Exportación PDF de reporte completo (5 productos, valor: S/ 21,035)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 15:26:09'),
('9', '4', 'exportar_reporte_completo_pdf', 'reportes', 'Exportación PDF de reporte completo (5 productos, valor: S/ 21,035)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 15:26:35'),
('10', '4', 'exportar_reporte_completo_pdf', 'reportes', 'Exportación PDF de reporte completo (5 productos, valor: S/ 21,035)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 15:40:01'),
('11', '4', 'exportar_reporte_completo_pdf', 'reportes', 'Exportación PDF de reporte completo (5 productos, valor: S/ 21,035)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 15:40:29'),
('12', '4', 'exportar_inventario_pdf', 'reportes', 'Exportación PDF de inventario general (5 productos, valor: S/ 21,035)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 15:40:43'),
('13', '4', 'exportar_reporte_completo_pdf', 'reportes', 'Exportación PDF de reporte completo (5 productos, valor: S/ 21,035)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 15:40:50'),
('14', '4', 'exportar_reporte_completo_pdf', 'reportes', 'Exportación PDF de reporte completo (5 productos, valor: S/ 21,035)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 15:45:14'),
('15', '4', 'exportar_inventario_pdf', 'reportes', 'Exportación PDF de inventario general (5 productos, valor: S/ 21,035)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 15:45:32'),
('16', '4', 'exportar_reporte_completo_pdf', 'reportes', 'Exportación PDF de reporte completo (5 productos, valor: S/ 21,035)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 15:49:46'),
('17', '4', 'exportar_reporte_completo_pdf', 'reportes', 'Exportación PDF de reporte completo (5 productos, valor: S/ 21,035)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 15:50:37'),
('18', '4', 'export', 'reportes', 'Exportó Análisis ABC a CSV', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 15:52:06'),
('19', '4', 'exportar_reporte_completo_pdf', 'reportes', 'Exportación PDF de reporte completo (5 productos, valor: S/ 21,035)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 15:55:29'),
('20', '4', 'exportar_reporte_completo_pdf', 'reportes', 'Exportación PDF de reporte completo (5 productos, valor: S/ 21,035)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 15:56:00'),
('21', '4', 'exportar_reporte_completo_pdf', 'reportes', 'Exportación PDF de reporte completo (5 productos, valor: S/ 21,035)', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '2026-05-13 15:56:10'),
('22', '4', 'exportar_reporte_completo_pdf', 'reportes', 'Exportación PDF de reporte completo (5 productos, valor: S/ 21,035)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 16:06:34'),
('23', '4', 'exportar_inventario_pdf', 'reportes', 'Exportación PDF de inventario general (5 productos, valor: S/ 21,035)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 16:08:22'),
('24', '4', 'login_exitoso', 'auth', 'Login exitoso desde IP: ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 18:57:12'),
('25', '4', 'editar_producto', 'productos', 'Producto editado: Casco de Seguridad Amarillo (SKU: SEG-001, ID: 5)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 19:03:20'),
('26', '4', 'editar_producto', 'productos', 'Producto editado: Taladro Inalámbrico 20V (SKU: HER-001, ID: 4)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 19:05:44'),
('27', '4', 'editar_producto', 'productos', 'Producto editado: Desinfectante Multiusos 5L (SKU: LIM-001, ID: 3)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 19:06:41'),
('28', '4', 'editar_producto', 'productos', 'Producto editado: Cajas de Papel Bond A4 (SKU: OFI-001, ID: 2)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 19:07:19'),
('29', '4', 'editar_producto', 'productos', 'Producto editado: Cajas de Papel Bond A4 (SKU: OFI-001, ID: 2)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 19:08:43'),
('30', '4', 'editar_producto', 'productos', 'Producto editado: Laptop Dell Latitude 3420 (SKU: LAP-001, ID: 1)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 19:13:44'),
('31', '4', 'crear_producto', 'productos', 'Producto creado: Mouse Inalámbrico Logitech M280 (SKU: MOU-LOG-01, ID: 6)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 19:28:31'),
('32', '4', 'crear_producto', 'productos', 'Producto creado: Red Bull (SKU: 9002490100070, ID: 7)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 19:51:37'),
('33', '4', 'movimiento_entrada', 'movimientos', 'Se registró un movimiento de 90 para Red Bull', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 19:54:36'),
('34', '4', 'movimiento_salida', 'movimientos', 'Se registró un movimiento de 5 para Mouse Inalámbrico Logitech M280', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 19:57:03'),
('35', '4', 'create', 'compras', 'Creó la orden de compra OC-20260513-0001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 19:59:00'),
('36', '4', 'update', 'compras', 'Canceló la orden de compra OC-20260513-0001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 19:59:12'),
('37', '4', 'create', 'compras', 'Creó la orden de compra OC-20260513-0002', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 19:59:40'),
('38', '4', 'update', 'compras', 'Canceló la orden de compra OC-20260513-0002', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 20:00:09'),
('39', '4', 'create', 'compras', 'Creó la orden de compra OC-20260513-0003', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 20:02:18'),
('40', '4', 'approve', 'compras', 'Aprobó orden de compra ID 3', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 20:02:28'),
('41', '4', 'update', 'compras', 'Recibió la orden de compra OC-20260513-0003', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 20:02:31'),
('42', '4', 'transferencia', 'transferencias', 'Transfirió \'Cajas de Papel Bond A4\' de \'Bodega Secundaria (B2)\' a \'Almacén Principal (A1)\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 20:09:37'),
('43', '4', 'actualizar_configuracion', 'configuracion', 'Configuraciones del sistema actualizadas: nombre_sistema, moneda_codigo, moneda_simbolo, zona_horaria, formato_fecha, stock_minimo_global, registros_por_pagina, permitir_stock_negativo, reorden_automatico, tema_defecto, sidebar_colapsable, densidad_compacta, animaciones, alertas_email, alertas_seguridad, intentos_login_max, tiempo_bloqueo_minutos, session_lifetime, retencion_logs, permitir_registro, rol_registro_publico, smtp_activo, smtp_host, smtp_port, smtp_encryption, smtp_auth, smtp_username, smtp_password, mail_from_address, mail_from_name', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 20:15:49'),
('44', '4', 'editar_producto', 'productos', 'Producto editado: Mouse Inalámbrico Logitech M280 (SKU: MOU-LOG-01, ID: 6)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 20:24:42'),
('45', '4', 'create', 'compras', 'Creó la orden de compra OC-20260513-0004', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 20:25:06'),
('46', '4', 'approve', 'compras', 'Aprobó orden de compra ID 4', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 20:38:34'),
('47', '4', 'create', 'compras', 'Creó la orden de compra OC-20260513-0005', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 20:39:37'),
('48', '4', 'update', 'compras', 'Canceló la orden de compra OC-20260513-0005', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 20:39:46'),
('49', '4', 'delete', 'compras', 'Eliminó permanentemente la orden de compra OC-20260513-0001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 20:49:19'),
('50', '4', 'delete', 'compras', 'Eliminó permanentemente la orden de compra OC-20260513-0002', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 20:49:26'),
('51', '4', 'crear_conteo', 'conteos', 'Sesión \'test\' creada con 7 productos', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 20:50:09'),
('52', '4', 'cerrar_conteo', 'conteos', 'Sesión #2 cerrada', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 20:50:57'),
('53', '4', 'cerrar_conteo', 'conteos', 'Sesión #1 cerrada', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 20:51:51'),
('54', '4', 'aplicar_ajustes', 'conteos', 'Aplicados 2 ajustes del conteo #1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 20:51:53'),
('55', '4', 'crear_conteo', 'conteos', 'Sesión \'prueba\' creada con 7 productos', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 20:52:23'),
('56', '4', 'cerrar_conteo', 'conteos', 'Sesión #3 cerrada', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 20:53:04'),
('57', '4', 'delete', 'conteos', 'Eliminó permanentemente la sesión de conteo \'prueba\' (ID: 3)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 21:03:03'),
('58', '4', 'login_fallido', 'auth', 'Contraseña incorrecta para: josuexd123lc@gmail.com. Intentos restantes: 4', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 21:39:41'),
('59', '4', 'login_fallido', 'auth', 'Contraseña incorrecta para: josuexd123lc@gmail.com. Intentos restantes: 3', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 21:40:07'),
('60', '4', 'logout', 'auth', 'Cierre de sesión', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 21:41:05'),
('61', '1', 'login_exitoso', 'auth', 'Login exitoso desde IP: ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 21:51:57'),
('62', '4', 'login_exitoso', 'auth', 'Login exitoso desde IP: ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 21:52:31'),
('63', '1', 'actualizar_configuracion', 'configuracion', 'Configuraciones del sistema actualizadas: nombre_sistema, moneda_codigo, moneda_simbolo, zona_horaria, formato_fecha, stock_minimo_global, registros_por_pagina, permitir_stock_negativo, reorden_automatico, tema_defecto, sidebar_colapsable, densidad_compacta, animaciones, alertas_email, alertas_seguridad, intentos_login_max, tiempo_bloqueo_minutos, session_lifetime, retencion_logs, permitir_registro, rol_registro_publico, smtp_activo, smtp_host, smtp_port, smtp_encryption, smtp_auth, smtp_username, smtp_password, mail_from_address, mail_from_name', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 21:52:50'),
('64', '1', 'login_exitoso', 'auth', 'Login exitoso desde IP: ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 22:09:23'),
('65', '1', 'actualizar_configuracion', 'configuracion', 'Configuraciones del sistema actualizadas: nombre_sistema, moneda_codigo, moneda_simbolo, zona_horaria, formato_fecha, stock_minimo_global, registros_por_pagina, permitir_stock_negativo, reorden_automatico, tema_defecto, sidebar_colapsable, densidad_compacta, animaciones, alertas_email, alertas_seguridad, intentos_login_max, tiempo_bloqueo_minutos, session_lifetime, retencion_logs, permitir_registro, rol_registro_publico, smtp_activo, smtp_host, smtp_port, smtp_encryption, smtp_auth, smtp_username, smtp_password, mail_from_address, mail_from_name', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 22:09:58'),
('66', '1', 'actualizar_configuracion', 'configuracion', 'Configuraciones del sistema actualizadas: nombre_sistema, moneda_codigo, moneda_simbolo, zona_horaria, formato_fecha, stock_minimo_global, registros_por_pagina, permitir_stock_negativo, reorden_automatico, tema_defecto, sidebar_colapsable, densidad_compacta, animaciones, alertas_email, alertas_seguridad, intentos_login_max, tiempo_bloqueo_minutos, session_lifetime, retencion_logs, permitir_registro, rol_registro_publico, smtp_activo, smtp_host, smtp_port, smtp_encryption, smtp_auth, smtp_username, smtp_password, mail_from_address, mail_from_name', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 22:10:16'),
('67', '1', 'login_exitoso', 'auth', 'Login exitoso desde IP: ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 22:29:22'),
('68', '1', 'actualizar_configuracion', 'configuracion', 'Configuraciones del sistema actualizadas: nombre_sistema, moneda_codigo, moneda_simbolo, zona_horaria, formato_fecha, stock_minimo_global, registros_por_pagina, permitir_stock_negativo, reorden_automatico, tema_defecto, sidebar_colapsable, densidad_compacta, animaciones, alertas_email, alertas_seguridad, intentos_login_max, tiempo_bloqueo_minutos, session_lifetime, retencion_logs, permitir_registro, rol_registro_publico, smtp_activo, smtp_host, smtp_port, smtp_encryption, smtp_auth, smtp_username, smtp_password, mail_from_address, mail_from_name', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 22:31:20'),
('69', '1', 'actualizar_configuracion', 'configuracion', 'Configuraciones del sistema actualizadas: nombre_sistema, moneda_codigo, moneda_simbolo, zona_horaria, formato_fecha, stock_minimo_global, registros_por_pagina, permitir_stock_negativo, reorden_automatico, tema_defecto, sidebar_colapsable, densidad_compacta, animaciones, alertas_email, alertas_seguridad, intentos_login_max, tiempo_bloqueo_minutos, session_lifetime, retencion_logs, permitir_registro, rol_registro_publico, smtp_activo, smtp_host, smtp_port, smtp_encryption, smtp_auth, smtp_username, smtp_password, mail_from_address, mail_from_name', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 22:31:41'),
('70', '1', 'login_exitoso', 'auth', 'Login exitoso desde IP: ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 22:35:43'),
('71', '4', 'login_exitoso', 'auth', 'Login exitoso desde IP: ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 22:43:36'),
('72', '1', 'login_exitoso', 'auth', 'Login exitoso desde IP: ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 22:59:50'),
('73', '4', 'logout', 'auth', 'Cierre de sesión', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 23:07:43'),
('74', '4', 'login_exitoso', 'auth', 'Login exitoso desde IP: ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 23:07:48'),
('75', '4', 'login_exitoso', 'auth', 'Login exitoso desde IP: ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-14 11:40:03'),
('76', '4', 'crear_producto', 'productos', 'Producto creado: Paquete de Bolígrafos Azules (SKU: OFI-BOL-AZ, ID: 8)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-14 11:48:33'),
('77', '4', 'crear_producto', 'productos', 'Producto creado: Tóner para Impresora HP LaserJet (SKU: OFI-TON-HP, ID: 9)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-14 11:54:13'),
('78', '4', 'editar_producto', 'productos', 'Producto editado: Tóner para Impresora HP LaserJet (SKU: OFI-TON-HP, ID: 9)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-14 11:54:26'),
('79', '4', 'editar_producto', 'productos', 'Producto editado: Tóner para Impresora HP LaserJet (SKU: OFI-TON-HP, ID: 9)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-14 11:54:57'),
('80', '1', 'login_fallido', 'auth', 'Contraseña incorrecta para: admin@invsys.com. Intentos restantes: 4', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-14 13:03:16'),
('81', '1', 'login_exitoso', 'auth', 'Login exitoso desde IP: ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-14 13:03:35'),
('82', '4', 'create', 'requisiciones', 'Creó requisición REQ-20260514-0001 para depto ID 5', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-14 13:11:13'),
('83', '4', 'approve', 'requisiciones', 'Aprobó requisición ID 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-14 13:11:21'),
('84', '4', 'movimiento_ajuste', 'movimientos', 'Se registró un movimiento de 1 para Red Bull', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-14 13:22:52'),
('85', '4', 'movimiento_ajuste', 'movimientos', 'Se registró un movimiento de 99 para Red Bull', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-14 13:24:38'),
('86', '4', 'movimiento_ajuste', 'movimientos', 'Se registró un movimiento de 100 para Red Bull', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-14 13:25:09'),
('87', '4', 'create', 'requisiciones', 'Creó requisición REQ-20260514-0001 para depto ID 5', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-14 13:26:53'),
('88', '4', 'approve', 'requisiciones', 'Aprobó requisición ID 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-14 13:27:03'),
('89', '4', 'update', 'requisiciones', 'Despachó requisición REQ-20260514-0001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-14 13:27:08'),
('90', '4', 'delete', 'devoluciones', 'Eliminó permanentemente la devolución ID: 1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-14 13:46:53'),
('91', '4', 'delete', 'requisiciones', 'Eliminó permanentemente la requisición REQ-20260514-0001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-14 13:47:34'),
('92', '4', 'delete', 'devoluciones', 'Eliminó permanentemente la devolución ID: 3', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-14 13:47:39'),
('93', '4', 'login_exitoso', 'auth', 'Login exitoso desde IP: ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-14 15:26:54'),
('94', '4', 'exportar_movimientos_pdf', 'reportes', 'Exportación PDF de movimientos — 13/05/2026 a 14/05/2026 (12 registros)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-14 15:27:41'),
('95', '4', 'delete', 'departamentos', 'Eliminó el departamento: Administración y Finanzas', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-14 15:39:07'),
('96', '4', 'actualizar_configuracion', 'configuracion', 'Configuraciones del sistema actualizadas: nombre_sistema, moneda_codigo, moneda_simbolo, zona_horaria, formato_fecha, stock_minimo_global, registros_por_pagina, permitir_stock_negativo, reorden_automatico, tema_defecto, sidebar_colapsable, densidad_compacta, animaciones, alertas_email, alertas_seguridad, intentos_login_max, tiempo_bloqueo_minutos, session_lifetime, retencion_logs, permitir_registro, rol_registro_publico, smtp_activo, smtp_host, smtp_port, smtp_encryption, smtp_auth, smtp_username, smtp_password, mail_from_address, mail_from_name', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-14 15:56:16'),
('97', '4', 'test_smtp', 'configuracion', 'Correo de prueba enviado a josuexd123lc@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-14 15:56:27'),
('98', '4', 'login_exitoso', 'auth', 'Login exitoso desde IP: ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-15 12:14:43'),
('99', '4', 'crear_producto', 'productos', 'Producto creado: Kit Teclado y Mouse Inalámbrico Logitech MK295 (SKU: IT-AC-012, ID: 10)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-15 12:21:05'),
('100', '4', 'crear_producto', 'productos', 'Producto creado: Archivador de Palanca Lomo Ancho (SKU: OF-ARC-008, ID: 11)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-15 12:23:21'),
('101', '4', 'crear_categoria', 'categorias', 'Categoría creada: Insumos de Cafetería y Comedor (ID: 6)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-15 12:28:26'),
('102', '4', 'crear_producto', 'productos', 'Producto creado: Paquete de Agua Mineral Sin Gas (Botella 500ml) (SKU: CAF-AGU-001, ID: 12)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-15 12:31:14'),
('103', '4', 'movimiento_entrada', 'movimientos', 'Se registró un movimiento de 1 para Paquete de Agua Mineral Sin Gas (Botella 500ml)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-15 12:32:42'),
('104', '4', 'login_exitoso', 'auth', 'Login exitoso desde IP: ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-16 12:32:03'),
('105', '1', 'login_exitoso', 'auth', 'Login exitoso desde IP: ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-16 12:47:38'),
('106', '4', 'crear_producto', 'productos', 'Producto creado: Josue (SKU: JLC-001, ID: 13)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-16 12:56:46'),
('107', '4', 'movimiento_entrada', 'movimientos', 'Se registró un movimiento de 1 para Josue', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-16 12:57:43'),
('108', '1', 'login_exitoso', 'auth', 'Login exitoso desde IP: ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-16 13:16:46'),
('109', '1', 'login_exitoso', 'auth', 'Login exitoso desde IP: ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-16 13:21:18'),
('110', '4', 'update_lote', 'productos', 'Fecha de vencimiento actualizada para Lote #001-RB (ID: 18)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-16 13:33:41'),
('111', '4', 'update_lote', 'productos', 'Fecha de vencimiento actualizada para Lote #LOT 002 (ID: 19)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-16 13:41:56'),
('112', '4', 'exportar_reporte_completo_pdf', 'reportes', 'Exportación PDF de reporte completo (14 productos, valor: S/ 30,128)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-16 13:55:02'),
('113', '4', 'update_lote', 'productos', 'Fecha de vencimiento actualizada para Lote #001-RB (ID: 16)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-16 14:18:21'),
('114', '4', 'toggle_producto', 'productos', 'Producto desactivado: daaa (ID: 14)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-16 14:29:56'),
('115', '4', 'toggle_producto', 'productos', 'Producto activado: daaa (ID: 14)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-16 14:30:17'),
('116', '4', 'login_exitoso', 'auth', 'Login exitoso desde IP: ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-18 11:39:59'),
('117', '4', 'crear_backup', 'backups', 'Backup creado: invsys_backup_2026-05-18_11-51-45.sql (114.6 KB)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-18 11:51:46'),
('118', '4', 'descargar_backup', 'backups', 'Backup descargado: invsys_backup_2026-05-18_11-51-45.sql', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-18 11:51:52'),
('119', '4', 'eliminar_backup', 'backups', 'Backup eliminado: invsys_backup_2026-04-24_01-50-29.sql', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-18 11:51:59'),
('120', '4', 'crear_categoria', 'categorias', 'Categoría creada: Mobiliario (ID: 7)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-18 12:07:59'),
('121', '4', 'crear_categoria', 'categorias', 'Categoría creada: Alimentos y Bebidas (ID: 8)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-18 12:08:30'),
('122', '4', 'crear_categoria', 'categorias', 'Categoría creada: Repuestos Informáticos (ID: 9)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-18 12:08:45'),
('123', '4', 'crear_producto', 'productos', 'Producto creado: Silla Ergonómica Ejecutiva (SKU: MOB-001, ID: 15)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-18 12:11:44'),
('124', '4', 'crear_producto', 'productos', 'Producto creado: Café en Grano Tostado 1Kg (SKU: ALIM-001, ID: 16)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-18 12:14:56'),
('125', '4', 'update_lote', 'productos', 'Fecha de vencimiento actualizada para Lote #LOT 003 (ID: 20)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-18 12:15:12'),
('126', '4', 'crear_producto', 'productos', 'Producto creado: Disco Duro SSD 1TB Samsung M.2 (SKU: REP-001, ID: 17)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-18 12:18:54'),
('127', '4', 'login_exitoso', 'auth', 'Login exitoso desde IP: ::1', '::1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1', '2026-05-18 15:38:20'),
('128', '4', 'create', 'requisiciones', 'Creó requisición REQ-20260518-0001 para depto ID 2', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-18 15:44:06'),
('129', '4', 'approve', 'requisiciones', 'Aprobó requisición ID 2', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-18 15:44:16'),
('130', '4', 'update', 'requisiciones', 'Despachó requisición REQ-20260518-0001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-18 15:44:46'),
('131', '4', 'exportar_csv', 'productos', 'Exportación CSV: 17 productos exportados', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-18 16:26:51'),
('132', '4', 'exportar_csv', 'productos', 'Exportación CSV: 17 productos exportados', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-18 16:27:21'),
('133', '4', 'exportar_csv', 'productos', 'Exportación CSV: 17 productos exportados', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-18 16:28:45'),
('134', '4', 'exportar_csv', 'productos', 'Exportación CSV: 17 productos exportados', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-18 16:29:35'),
('135', '4', 'importar_csv', 'productos', 'Importación CSV: 1 importados, 0 omitidos', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-18 17:00:25'),
('136', '4', 'crear_producto', 'productos', 'Producto creado: Pc Aio Hp Proone 440 (SKU: AIO1, ID: 19)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-18 17:46:44'),
('137', '4', 'movimiento_entrada', 'movimientos', 'Se registró un movimiento de 3 para Pc Aio Hp Proone 440', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-18 17:54:12'),
('138', '4', 'movimiento_salida', 'movimientos', 'Se registró un movimiento de 2 para Pc Aio Hp Proone 440', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-18 17:56:33'),
('139', '4', 'crear_producto', 'productos', 'Producto creado: faaa (SKU: 002, ID: 20)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-18 17:57:45'),
('140', '4', 'crear_producto', 'productos', 'Producto creado: heeee (SKU: HE322, ID: 21)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-18 18:09:41'),
('141', '4', 'movimiento_salida', 'movimientos', 'Se registró un movimiento de 1 para Pc Aio Hp Proone 440', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-18 18:52:33'),
('142', '4', 'login_exitoso', 'auth', 'Login exitoso desde IP: ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-19 20:39:33'),
('143', '4', 'login_exitoso', 'auth', 'Login exitoso desde IP: ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-21 11:08:56'),
('144', '4', 'actualizar_logo', 'configuracion', 'Logo del sistema actualizado: logo_1779379872.png', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-21 11:11:12'),
('145', '4', 'actualizar_configuracion', 'configuracion', 'Configuraciones del sistema actualizadas: nombre_sistema, moneda_codigo, moneda_simbolo, zona_horaria, formato_fecha, stock_minimo_global, registros_por_pagina, permitir_stock_negativo, reorden_automatico, tema_defecto, sidebar_colapsable, densidad_compacta, animaciones, alertas_email, alertas_seguridad, intentos_login_max, tiempo_bloqueo_minutos, session_lifetime, retencion_logs, permitir_registro, rol_registro_publico, smtp_activo, smtp_host, smtp_port, smtp_encryption, smtp_auth, smtp_username, smtp_password, mail_from_address, mail_from_name', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-21 11:11:12'),
('146', '4', 'actualizar_logo', 'configuracion', 'Logo del sistema actualizado: logo_1779380263.png', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-21 11:17:43'),
('147', '4', 'actualizar_configuracion', 'configuracion', 'Configuraciones del sistema actualizadas: nombre_sistema, moneda_codigo, moneda_simbolo, zona_horaria, formato_fecha, stock_minimo_global, registros_por_pagina, permitir_stock_negativo, reorden_automatico, tema_defecto, sidebar_colapsable, densidad_compacta, animaciones, alertas_email, alertas_seguridad, intentos_login_max, tiempo_bloqueo_minutos, session_lifetime, retencion_logs, permitir_registro, rol_registro_publico, smtp_activo, smtp_host, smtp_port, smtp_encryption, smtp_auth, smtp_username, smtp_password, mail_from_address, mail_from_name', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-21 11:17:43'),
('148', '4', 'update', 'compras', 'Recibió la orden de compra OC-20260513-0004', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-21 11:19:09'),
('149', '4', 'exportar_movimientos_csv', 'reportes', 'Exportación CSV de movimientos (24 registros)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-21 11:46:31'),
('150', '4', 'movimiento_entrada', 'movimientos', 'Se registró un movimiento de 10 para Disco Duro SSD 1TB Samsung M.2', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-21 11:58:52'),
('151', '4', 'exportar_reporte_completo_pdf', 'reportes', 'Exportación PDF de reporte completo (19 productos, valor: S/ 53,128)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-21 12:16:34'),
('152', '4', 'export', 'reportes', 'Exportó Análisis ABC a CSV', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-21 12:40:47'),
('153', '4', 'login_exitoso', 'auth', 'Login exitoso desde IP: ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-21 18:56:18'),
('154', '4', 'logout', 'auth', 'Cierre de sesión', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-21 18:56:32'),
('155', '4', 'login_exitoso', 'auth', 'Login exitoso desde IP: ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-21 18:56:41'),
('156', '4', 'login_exitoso', 'auth', 'Login exitoso desde IP: ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-24 14:35:56'),
('157', '4', 'login_exitoso', 'auth', 'Login exitoso desde IP: ::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-24 18:40:45');

-- -----------------------------------------------------
-- TABLA: lotes
-- -----------------------------------------------------
DROP TABLE IF EXISTS `lotes`;
CREATE TABLE `lotes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `producto_id` int(10) unsigned NOT NULL,
  `numero_lote` varchar(50) NOT NULL,
  `cantidad_inicial` int(11) NOT NULL,
  `stock_actual` int(11) NOT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `proveedor_id` int(10) unsigned DEFAULT NULL,
  `estado` enum('disponible','agotado','vencido','aislado') NOT NULL DEFAULT 'disponible',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_lotes_producto_numero` (`producto_id`,`numero_lote`),
  KEY `idx_lotes_producto` (`producto_id`),
  KEY `idx_lotes_proveedor` (`proveedor_id`),
  KEY `idx_lotes_vencimiento` (`fecha_vencimiento`),
  KEY `idx_lotes_estado` (`estado`),
  CONSTRAINT `fk_lotes_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_lotes_proveedor` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `lotes` (`id`, `producto_id`, `numero_lote`, `cantidad_inicial`, `stock_actual`, `fecha_vencimiento`, `proveedor_id`, `estado`, `created_at`, `updated_at`) VALUES
('1', '4', 'L-DET-1025', '50', '14', '2026-12-15', '3', 'disponible', '2026-04-20 23:48:03', '2026-04-26 18:54:34'),
('2', '5', 'L-GLA-0824', '100', '13', '2027-06-30', '3', 'disponible', '2026-04-20 23:48:03', '2026-04-26 18:58:50'),
('3', '6', '001', '5', '4', '2026-04-22', NULL, 'disponible', '2026-04-20 19:23:04', '2026-05-04 20:49:58'),
('5', '4', 'L-001', '15', '15', '2026-12-31', NULL, 'disponible', '2026-04-23 18:32:31', NULL),
('6', '5', '001', '2', '2', '2026-04-24', '1', 'disponible', '2026-04-23 18:39:19', NULL),
('7', '4', 'lote 002', '10', '10', '2026-05-30', '3', 'disponible', '2026-05-04 13:50:22', NULL),
('8', '4', '002', '5', '5', '2026-05-25', '3', 'disponible', '2026-05-04 14:02:10', NULL),
('13', '4', '003', '10', '10', '2026-05-20', '3', 'disponible', '2026-05-05 16:13:53', NULL),
('14', '5', '004', '1', '1', '2026-05-06', '2', 'disponible', '2026-05-05 16:29:26', NULL),
('15', '4', '005', '1', '1', '2026-05-29', '3', 'disponible', '2026-05-11 15:58:09', NULL),
('16', '7', '001-RB', '90', '90', '2026-05-26', '2', 'disponible', '2026-05-13 19:54:36', '2026-05-16 14:18:21'),
('17', '12', '001 A', '1', '1', '2026-05-30', '2', 'disponible', '2026-05-15 12:32:42', NULL),
('18', '13', '001-RB', '1', '1', '2026-05-30', NULL, 'disponible', '2026-05-16 12:57:43', '2026-05-16 13:33:41'),
('19', '14', 'LOT 002', '10', '10', '2026-05-20', NULL, 'disponible', '2026-05-16 13:35:11', '2026-05-16 13:41:56'),
('20', '16', 'LOT 003', '8', '8', '2026-06-22', NULL, 'disponible', '2026-05-18 12:14:56', '2026-05-18 12:15:12');

-- -----------------------------------------------------
-- TABLA: movimientos
-- -----------------------------------------------------
DROP TABLE IF EXISTS `movimientos`;
CREATE TABLE `movimientos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `producto_id` int(10) unsigned NOT NULL,
  `usuario_id` int(10) unsigned NOT NULL,
  `lote_id` int(10) unsigned DEFAULT NULL,
  `proveedor_id` int(10) unsigned DEFAULT NULL,
  `departamento_id` int(10) unsigned DEFAULT NULL,
  `destino` varchar(150) DEFAULT NULL,
  `tipo` enum('entrada','salida','ajuste','transferencia','devolucion') NOT NULL,
  `cantidad` int(11) NOT NULL,
  `stock_anterior` int(11) NOT NULL,
  `stock_nuevo` int(11) NOT NULL,
  `referencia` varchar(100) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_movimientos_producto` (`producto_id`),
  KEY `idx_movimientos_usuario` (`usuario_id`),
  KEY `idx_movimientos_lote` (`lote_id`),
  KEY `idx_movimientos_proveedor` (`proveedor_id`),
  KEY `idx_movimientos_tipo` (`tipo`),
  KEY `idx_movimientos_fecha` (`created_at`),
  KEY `fk_movimientos_departamento` (`departamento_id`),
  CONSTRAINT `fk_movimientos_departamento` FOREIGN KEY (`departamento_id`) REFERENCES `departamentos` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_movimientos_lote` FOREIGN KEY (`lote_id`) REFERENCES `lotes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_movimientos_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`),
  CONSTRAINT `fk_movimientos_proveedor` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_movimientos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `movimientos` (`id`, `producto_id`, `usuario_id`, `lote_id`, `proveedor_id`, `departamento_id`, `destino`, `tipo`, `cantidad`, `stock_anterior`, `stock_nuevo`, `referencia`, `observaciones`, `created_at`) VALUES
('1', '7', '4', '16', '2', NULL, NULL, 'entrada', '90', '10', '100', 'MAY-2026', '', '2026-05-13 19:54:36'),
('2', '6', '4', NULL, NULL, NULL, 'Reduccion', 'salida', '5', '10', '5', 'OCT-2026', '', '2026-05-13 19:57:03'),
('3', '4', '4', NULL, '2', NULL, NULL, 'entrada', '5', '15', '20', 'OC-20260513-0003', 'Recepción de Orden de Compra', '2026-05-13 20:02:31'),
('4', '6', '4', NULL, '2', NULL, NULL, 'entrada', '10', '5', '15', 'OC-20260513-0003', 'Recepción de Orden de Compra', '2026-05-13 20:02:31'),
('5', '2', '4', NULL, NULL, NULL, 'Transferencia a Almacén Principal (A1)', 'transferencia', '50', '50', '50', 'TRANSF-20260513200937', 'Desde: Bodega Secundaria (B2) -> Hacia: Almacén Principal (A1)', '2026-05-13 20:09:37'),
('6', '7', '4', NULL, NULL, NULL, NULL, 'ajuste', '1', '100', '99', 'CONTEO-1', 'Ajuste automático por conteo físico: MAY-2026', '2026-05-13 20:51:53'),
('7', '4', '4', NULL, NULL, NULL, NULL, 'ajuste', '5', '20', '40', 'CONTEO-1', 'Ajuste automático por conteo físico: MAY-2026', '2026-05-13 20:51:53'),
('8', '7', '4', NULL, NULL, NULL, NULL, 'ajuste', '98', '99', '1', 'MAY-2026', '', '2026-05-14 13:22:52'),
('9', '7', '4', NULL, NULL, NULL, NULL, 'ajuste', '98', '1', '99', 'MAY-2026', '', '2026-05-14 13:24:38'),
('10', '7', '4', NULL, NULL, NULL, NULL, 'ajuste', '1', '99', '100', 'MAY-2026', '', '2026-05-14 13:25:09'),
('11', '2', '4', NULL, NULL, NULL, NULL, 'salida', '1', '50', '49', 'REQ-20260514-0001', 'Despacho de requisición', '2026-05-14 13:27:08'),
('12', '2', '4', NULL, NULL, NULL, NULL, 'devolucion', '1', '0', '0', NULL, 'Devolución de Administración y Finanzas. Motivo: devolucion | Estado: bueno', '2026-05-14 13:29:06'),
('13', '12', '4', '17', '2', NULL, NULL, 'entrada', '1', '30', '31', 'MAY-2026', '', '2026-05-15 12:32:42'),
('14', '13', '4', '18', NULL, NULL, NULL, 'entrada', '1', '0', '1', '', '', '2026-05-16 12:57:43'),
('15', '15', '4', NULL, NULL, NULL, NULL, 'ajuste', '10', '0', '10', 'Stock Inicial', 'Ajuste automático por stock inicial al crear producto.', '2026-05-18 12:11:44'),
('16', '16', '4', '20', NULL, NULL, NULL, 'ajuste', '8', '0', '8', 'Stock Inicial', 'Ajuste automático por stock inicial al crear producto.', '2026-05-18 12:14:56'),
('17', '17', '4', NULL, NULL, NULL, NULL, 'ajuste', '10', '0', '10', 'Stock Inicial', 'Ajuste automático por stock inicial al crear producto.', '2026-05-18 12:18:54'),
('18', '17', '4', NULL, NULL, '2', NULL, 'salida', '1', '10', '9', 'REQ-20260518-0001', 'Despacho de requisición', '2026-05-18 15:44:46'),
('19', '17', '4', NULL, NULL, '2', NULL, 'devolucion', '1', '0', '0', NULL, 'Devolución de Tecnología de la Información. Motivo: prestamo | Estado: bueno', '2026-05-18 15:46:00'),
('20', '19', '4', NULL, NULL, NULL, NULL, 'ajuste', '2', '0', '2', 'Stock Inicial', 'Ajuste automático por stock inicial al crear producto.', '2026-05-18 17:46:44'),
('22', '19', '4', NULL, NULL, NULL, NULL, 'entrada', '3', '2', '5', '', 'NN', '2026-05-18 17:54:12'),
('23', '19', '4', NULL, NULL, NULL, '', 'salida', '2', '5', '3', '', '', '2026-05-18 17:56:33'),
('26', '19', '4', NULL, NULL, NULL, '', 'salida', '1', '3', '2', 'salida a produccion 001', '', '2026-05-18 18:52:33'),
('27', '6', '4', NULL, '2', NULL, NULL, 'entrada', '5', '15', '20', 'OC-20260513-0004', 'Recepción de Orden de Compra', '2026-05-21 11:19:09'),
('28', '17', '4', NULL, NULL, NULL, NULL, 'entrada', '10', '10', '20', '', '', '2026-05-21 11:58:52');

-- -----------------------------------------------------
-- TABLA: numeros_serie
-- -----------------------------------------------------
DROP TABLE IF EXISTS `numeros_serie`;
CREATE TABLE `numeros_serie` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `producto_id` int(10) unsigned NOT NULL,
  `numero_serie` varchar(100) NOT NULL,
  `estado` enum('disponible','asignado','en_reparacion','dado_de_baja') NOT NULL DEFAULT 'disponible',
  `movimiento_entrada_id` int(10) unsigned DEFAULT NULL,
  `movimiento_salida_id` int(10) unsigned DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_producto_serie` (`producto_id`,`numero_serie`),
  KEY `idx_serie_estado` (`estado`),
  KEY `fk_serie_mov_entrada` (`movimiento_entrada_id`),
  KEY `fk_serie_mov_salida` (`movimiento_salida_id`),
  CONSTRAINT `fk_serie_mov_entrada` FOREIGN KEY (`movimiento_entrada_id`) REFERENCES `movimientos` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_serie_mov_salida` FOREIGN KEY (`movimiento_salida_id`) REFERENCES `movimientos` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_serie_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `numeros_serie` (`id`, `producto_id`, `numero_serie`, `estado`, `movimiento_entrada_id`, `movimiento_salida_id`, `notas`, `created_at`, `updated_at`) VALUES
('1', '19', '322', 'asignado', '20', '23', NULL, '2026-05-18 17:46:44', '2026-05-18 17:56:33'),
('2', '19', '3221', 'asignado', '20', '23', NULL, '2026-05-18 17:46:44', '2026-05-18 17:56:33'),
('3', '19', 'jlc001', 'disponible', '22', NULL, NULL, '2026-05-18 17:54:12', NULL),
('4', '19', 'jcl002', 'disponible', '22', NULL, NULL, '2026-05-18 17:54:12', NULL),
('5', '19', 'jlc003', 'asignado', '22', '26', NULL, '2026-05-18 17:54:12', '2026-05-18 18:52:33');

-- -----------------------------------------------------
-- TABLA: orden_compra_detalles
-- -----------------------------------------------------
DROP TABLE IF EXISTS `orden_compra_detalles`;
CREATE TABLE `orden_compra_detalles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `orden_compra_id` int(10) unsigned NOT NULL,
  `producto_id` int(10) unsigned NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_orden_compra_id` (`orden_compra_id`),
  KEY `idx_producto_id` (`producto_id`),
  CONSTRAINT `fk_ocd_orden` FOREIGN KEY (`orden_compra_id`) REFERENCES `ordenes_compra` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ocd_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`),
  CONSTRAINT `fk_orden_compra` FOREIGN KEY (`orden_compra_id`) REFERENCES `ordenes_compra` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `orden_compra_detalles` (`id`, `orden_compra_id`, `producto_id`, `cantidad`, `precio_unitario`, `subtotal`, `created_at`) VALUES
('3', '3', '4', '5', '75.00', '375.00', '2026-05-04 14:01:54'),
('12', '3', '6', '10', '0.00', '0.00', '2026-05-13 20:02:18'),
('13', '4', '6', '5', '80.00', '400.00', '2026-05-13 20:25:06'),
('14', '5', '2', '2', '25.00', '50.00', '2026-05-13 20:39:37');

-- -----------------------------------------------------
-- TABLA: ordenes_compra
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ordenes_compra`;
CREATE TABLE `ordenes_compra` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `numero_orden` varchar(50) NOT NULL,
  `proveedor_id` int(10) unsigned NOT NULL,
  `usuario_id` int(10) unsigned NOT NULL,
  `estado` enum('borrador','pendiente','aprobada','recibida','cancelada') DEFAULT 'borrador',
  `fecha_emision` date NOT NULL,
  `fecha_esperada` date DEFAULT NULL,
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `notas` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_numero_orden` (`numero_orden`),
  KEY `idx_proveedor_id` (`proveedor_id`),
  KEY `idx_usuario_id` (`usuario_id`),
  CONSTRAINT `fk_oc_proveedor` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`),
  CONSTRAINT `fk_oc_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `ordenes_compra` (`id`, `numero_orden`, `proveedor_id`, `usuario_id`, `estado`, `fecha_emision`, `fecha_esperada`, `total`, `notas`, `created_at`, `updated_at`, `deleted_at`) VALUES
('3', 'OC-20260513-0003', '2', '4', 'recibida', '2026-05-13', '2026-05-13', '0.00', '', '2026-05-13 20:02:18', '2026-05-13 20:02:31', NULL),
('4', 'OC-20260513-0004', '2', '4', 'recibida', '2026-05-13', '2026-05-13', '400.00', '', '2026-05-13 20:25:06', '2026-05-21 11:19:09', NULL),
('5', 'OC-20260513-0005', '2', '4', 'cancelada', '2026-05-13', '2026-05-13', '50.00', '', '2026-05-13 20:39:37', '2026-05-13 20:39:46', NULL);

-- -----------------------------------------------------
-- TABLA: permisos
-- -----------------------------------------------------
DROP TABLE IF EXISTS `permisos`;
CREATE TABLE `permisos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `modulo` varchar(50) NOT NULL,
  `accion` varchar(50) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_permisos_modulo_accion` (`modulo`,`accion`),
  KEY `idx_permisos_modulo` (`modulo`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `permisos` (`id`, `modulo`, `accion`, `descripcion`, `created_at`) VALUES
('1', 'dashboard', 'ver', 'Ver dashboard principal', '2026-04-20 23:48:02'),
('2', 'productos', 'ver', 'Ver listado de productos', '2026-04-20 23:48:02'),
('3', 'productos', 'crear', 'Crear nuevos productos', '2026-04-20 23:48:02'),
('4', 'productos', 'editar', 'Editar productos existentes', '2026-04-20 23:48:02'),
('5', 'productos', 'eliminar', 'Eliminar productos', '2026-04-20 23:48:02'),
('6', 'categorias', 'ver', 'Ver categorías', '2026-04-20 23:48:02'),
('7', 'categorias', 'crear', 'Crear categorías', '2026-04-20 23:48:02'),
('8', 'categorias', 'editar', 'Editar categorías', '2026-04-20 23:48:02'),
('9', 'categorias', 'eliminar', 'Eliminar categorías', '2026-04-20 23:48:02'),
('10', 'movimientos', 'ver', 'Ver movimientos de inventario', '2026-04-20 23:48:02'),
('11', 'movimientos', 'crear', 'Registrar movimientos', '2026-04-20 23:48:02'),
('12', 'alertas', 'ver', 'Ver alertas del sistema', '2026-04-20 23:48:02'),
('13', 'alertas', 'gestionar', 'Marcar alertas como leídas', '2026-04-20 23:48:02'),
('14', 'reportes', 'ver', 'Ver reportes', '2026-04-20 23:48:02'),
('15', 'reportes', 'exportar', 'Exportar reportes', '2026-04-20 23:48:02'),
('16', 'usuarios', 'ver', 'Ver usuarios', '2026-04-20 23:48:02'),
('17', 'usuarios', 'crear', 'Crear usuarios', '2026-04-20 23:48:02'),
('18', 'usuarios', 'editar', 'Editar usuarios', '2026-04-20 23:48:02'),
('19', 'usuarios', 'eliminar', 'Eliminar usuarios', '2026-04-20 23:48:02'),
('20', 'configuracion', 'ver', 'Ver configuración', '2026-04-20 23:48:02'),
('21', 'configuracion', 'editar', 'Editar configuración', '2026-04-20 23:48:02'),
('22', 'seguridad', 'ver', 'Ver logs de seguridad', '2026-04-20 23:48:02'),
('23', 'seguridad', 'gestionar', 'Gestionar seguridad', '2026-04-20 23:48:02'),
('24', 'proveedores', 'ver', 'Ver proveedores', '2026-04-23 17:42:34'),
('25', 'proveedores', 'crear', 'Crear proveedores', '2026-04-23 17:42:34'),
('26', 'proveedores', 'editar', 'Editar proveedores', '2026-04-23 17:42:34'),
('27', 'proveedores', 'eliminar', 'Eliminar proveedores', '2026-04-23 17:42:34'),
('28', 'ubicaciones', 'ver', 'Ver ubicaciones', '2026-04-23 17:42:34'),
('29', 'ubicaciones', 'crear', 'Crear ubicaciones', '2026-04-23 17:42:34'),
('30', 'ubicaciones', 'editar', 'Editar ubicaciones', '2026-04-23 17:42:34'),
('31', 'ubicaciones', 'eliminar', 'Eliminar ubicaciones', '2026-04-23 17:42:34'),
('32', 'departamentos', 'ver', 'Ver cat├ílogo de departamentos', '2026-04-26 18:19:41'),
('33', 'departamentos', 'crear', 'Crear y editar departamentos', '2026-04-26 18:19:41'),
('34', 'requisiciones', 'ver', 'Ver historial de requisiciones', '2026-04-26 18:19:41'),
('35', 'requisiciones', 'crear', 'Crear nuevas requisiciones', '2026-04-26 18:19:41'),
('36', 'requisiciones', 'despachar', 'Aprobar y despachar requisiciones (salida de stock)', '2026-04-26 18:19:41'),
('37', 'devoluciones', 'ver', 'Ver historial de devoluciones', '2026-04-28 16:58:58'),
('38', 'devoluciones', 'crear', 'Registrar nuevas devoluciones', '2026-04-28 16:58:58'),
('39', 'devoluciones', 'aprobar', 'Aprobar o rechazar devoluciones', '2026-04-28 16:58:58'),
('40', 'requisiciones', 'aprobar', 'Aprobar requisiciones internas', '2026-05-05 12:44:22'),
('41', 'compras', 'ver', 'Ver órdenes de compra', '2026-05-05 12:44:22'),
('42', 'compras', 'crear', 'Crear órdenes de compra', '2026-05-05 12:44:22'),
('43', 'compras', 'aprobar', 'Aprobar órdenes de compra', '2026-05-05 12:44:22');

-- -----------------------------------------------------
-- TABLA: producto_proveedor
-- -----------------------------------------------------
DROP TABLE IF EXISTS `producto_proveedor`;
CREATE TABLE `producto_proveedor` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `producto_id` int(10) unsigned NOT NULL,
  `proveedor_id` int(10) unsigned NOT NULL,
  `codigo_proveedor` varchar(50) DEFAULT NULL COMMENT 'C??digo del producto en cat??logo del proveedor',
  `costo` decimal(12,2) DEFAULT NULL,
  `tiempo_entrega_dias` int(10) unsigned DEFAULT NULL COMMENT 'D??as estimados de entrega',
  `es_preferido` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Proveedor preferido para este producto',
  `notas` text DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_prod_prov` (`producto_id`,`proveedor_id`),
  KEY `idx_pp_producto` (`producto_id`),
  KEY `idx_pp_proveedor` (`proveedor_id`),
  KEY `idx_pp_preferido` (`es_preferido`),
  CONSTRAINT `fk_pp_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pp_proveedor` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `producto_proveedor` (`id`, `producto_id`, `proveedor_id`, `codigo_proveedor`, `costo`, `tiempo_entrega_dias`, `es_preferido`, `notas`, `activo`, `created_at`, `updated_at`) VALUES
('1', '1', '1', 'HP-PB450-G8', '16500.00', '5', '1', NULL, '1', '2026-04-26 15:01:10', NULL),
('2', '2', '1', 'DELL-P2422H', '4500.00', '5', '1', NULL, '1', '2026-04-26 15:01:10', NULL),
('3', '3', '2', 'RESMA-A4-75G', '95.00', '2', '1', NULL, '1', '2026-04-26 15:01:10', NULL),
('4', '4', '3', 'DET-IND-5L', '75.00', '3', '1', NULL, '1', '2026-04-26 15:01:10', NULL),
('5', '5', '3', 'GLAT-M-100', '120.00', '3', '1', NULL, '1', '2026-04-26 15:01:10', NULL),
('6', '1', '3', NULL, '0.00', '0', '0', NULL, '0', '2026-04-26 15:28:23', '2026-05-04 21:05:43'),
('7', '7', '2', NULL, '0.00', '0', '0', NULL, '0', '2026-04-26 15:32:50', '2026-04-26 15:36:03');

-- -----------------------------------------------------
-- TABLA: productos
-- -----------------------------------------------------
DROP TABLE IF EXISTS `productos`;
CREATE TABLE `productos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `sku` varchar(16) NOT NULL,
  `codigo_barras` varchar(50) DEFAULT NULL COMMENT 'C??digo de barras EAN-13, UPC, etc.',
  `categoria_id` int(10) unsigned DEFAULT NULL,
  `unidad_medida` varchar(20) NOT NULL DEFAULT 'Unidad',
  `ubicacion_id` int(10) unsigned DEFAULT NULL,
  `costo` decimal(12,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) NOT NULL DEFAULT 0,
  `stock_minimo` int(10) unsigned NOT NULL DEFAULT 5,
  `imagen` varchar(255) DEFAULT NULL COMMENT 'Nombre del archivo de imagen del producto',
  `es_perecedero` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Indica si el producto exige lotes con vencimiento',
  `requiere_serie` tinyint(1) NOT NULL DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_productos_sku` (`sku`),
  UNIQUE KEY `uk_productos_barcode` (`codigo_barras`),
  KEY `idx_productos_categoria` (`categoria_id`),
  KEY `idx_productos_ubicacion` (`ubicacion_id`),
  KEY `idx_productos_stock` (`stock`),
  KEY `idx_productos_activo` (`activo`),
  KEY `idx_productos_barcode` (`codigo_barras`),
  CONSTRAINT `fk_productos_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_productos_ubicacion` FOREIGN KEY (`ubicacion_id`) REFERENCES `ubicaciones` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `productos` (`id`, `nombre`, `descripcion`, `sku`, `codigo_barras`, `categoria_id`, `unidad_medida`, `ubicacion_id`, `costo`, `stock`, `stock_minimo`, `imagen`, `es_perecedero`, `requiere_serie`, `activo`, `created_at`, `updated_at`) VALUES
('1', 'Laptop Dell Latitude 3420', 'Laptop empresarial 14 pulgadas, i5 11va Gen, 8GB RAM, 256GB SSD', 'LAP-001', '1234567890123', '1', 'Unidad', '1', '850.00', '20', '5', 'prod_6a0513b86c47b_1778717624.jpg', '0', '0', '1', '2026-05-13 15:11:35', '2026-05-13 19:13:44'),
('2', 'Cajas de Papel Bond A4', 'Caja con 10 resmas de papel bond blanco 75g', 'OFI-001', '1234567890124', '2', 'Caja', '1', '25.00', '50', '10', 'prod_6a05128b2aa12_1778717323.jpg', '0', '0', '1', '2026-05-13 15:11:35', '2026-05-14 13:29:06'),
('3', 'Desinfectante Multiusos 5L', 'Galón de desinfectante aroma lavanda para pisos y superficies', 'LIM-001', '1234567890125', '3', 'Unidad', '1', '12.00', '30', '8', 'prod_6a05121199e47_1778717201.jpg', '0', '0', '1', '2026-05-13 15:11:35', '2026-05-13 19:06:41'),
('4', 'Taladro Inalámbrico 20V', 'Taladro percutor con 2 baterías recargables y estuche', 'HER-001', '1234567890126', '4', 'Unidad', '3', '75.00', '40', '3', 'prod_6a0511d8b211a_1778717144.jpg', '0', '0', '1', '2026-05-13 15:11:35', '2026-05-13 20:51:53'),
('5', 'Casco de Seguridad Amarillo', 'Casco de policarbonato con ajuste rachet, dieléctrico', 'SEG-001', '1234567890127', '5', 'Unidad', '1', '15.00', '40', '15', 'prod_6a0511482dd99_1778717000.jpg', '0', '0', '1', '2026-05-13 15:11:35', '2026-05-13 19:03:20'),
('6', 'Mouse Inalámbrico Logitech M280', 'Mouse inalámbrico ergonómico diseñado exclusivamente para usuarios diestros', 'MOU-LOG-01', NULL, '1', 'Unidad', '1', '80.00', '20', '5', 'prod_6a05172fe28bc_1778718511.png', '0', '0', '1', '2026-05-13 19:28:31', '2026-05-21 11:19:09'),
('7', 'Red Bull', 'Contenido: 250ml | Categoría: Beverages and beverages preparations, Beverages, Carbonated drinks', '9002490100070', NULL, NULL, 'Unidad', NULL, '10.00', '100', '5', 'prod_6a051c997dd73_1778719897.jpg', '1', '0', '1', '2026-05-13 19:51:37', '2026-05-14 13:25:09'),
('8', 'Paquete de Bolígrafos Azules', 'Bolígrafo de calidad y larga duración.', 'OFI-BOL-AZ', NULL, '2', 'Paquete', '1', '25.00', '10', '5', 'prod_6a05fce188150_1778777313.jpg', '0', '0', '1', '2026-05-14 11:48:33', NULL),
('9', 'Tóner para Impresora HP LaserJet', 'Cartucho de Toner HP W1510X original de color negro con rendimiento de 9700 páginas de impresión.', 'OFI-TON-HP', NULL, '2', 'Unidad', '1', '200.00', '10', '5', 'prod_6a05fe4248eb7_1778777666.jpg', '0', '0', '1', '2026-05-14 11:54:13', '2026-05-14 11:54:57'),
('10', 'Kit Teclado y Mouse Inalámbrico Logitech MK295', 'Combo de teclado y mouse inalámbrico silencioso con tecnología SilentTouch y conexión USB de 2.4 GHz. Batería de larga duración.', 'IT-AC-012', NULL, '1', 'Unidad', '1', '120.00', '20', '5', 'prod_6a0756013387e_1778865665.jpg', '0', '0', '1', '2026-05-15 12:21:05', NULL),
('11', 'Archivador de Palanca Lomo Ancho', 'Archivador tamaño oficio de cartón forrado en PVC color negro, lomo ancho de 8cm con mecanismo de palanca metálico y filo reforzado.', 'OF-ARC-008', NULL, '2', 'Unidad', '4', '20.00', '20', '5', 'prod_6a0756896f01c_1778865801.jpg', '0', '0', '1', '2026-05-15 12:23:21', NULL),
('12', 'Paquete de Agua Mineral Sin Gas (Botella 500ml)', 'Paquete de 12 botellas de agua mineral sin gas de 500ml cada una. Marca San Mateo. Ideal para salas de directorio y reuniones de gerencia.', 'CAF-AGU-001', NULL, '6', 'Paquete', NULL, '18.00', '31', '5', 'prod_6a0758629cdef_1778866274.jpg', '1', '0', '1', '2026-05-15 12:31:14', '2026-05-15 12:32:42'),
('13', 'Josue', '', 'JLC-001', NULL, NULL, 'Unidad', '1', '10.00', '1', '5', NULL, '1', '0', '1', '2026-05-16 12:56:46', '2026-05-16 12:57:43'),
('14', 'daaa', '', '0051G1', NULL, NULL, 'Unidad', NULL, '10.00', '10', '5', 'prod_6a08b8df923c6_1778956511.png', '1', '0', '1', '2026-05-16 13:35:11', '2026-05-16 14:30:17'),
('15', 'Silla Ergonómica Ejecutiva', 'Silla de oficina con soporte lumbar ajustable, color negro y ruedas giratorias.', 'MOB-001', NULL, '7', 'Unidad', '1', '400.00', '10', '5', 'prod_6a0b4850c47a3_1779124304.jpg', '0', '0', '1', '2026-05-18 12:11:44', NULL),
('16', 'Café en Grano Tostado 1Kg', 'Bolsa de café de especialidad para las máquinas de espresso de la oficina.', 'ALIM-001', NULL, '8', 'Paquete', '5', '50.00', '8', '5', 'prod_6a0b491055957_1779124496.jpg', '1', '0', '1', '2026-05-18 12:14:56', NULL),
('17', 'Disco Duro SSD 1TB Samsung M.2', 'Unidad de estado sólido NVMe para actualización y reparación de laptops.', 'REP-001', NULL, '9', 'Unidad', '1', '500.00', '20', '5', 'prod_6a0b49fe84325_1779124734.jpg', '0', '0', '1', '2026-05-18 12:18:54', '2026-05-21 11:58:52'),
('18', 'joss', '', 'lcs322', NULL, NULL, 'Unidad', NULL, '10.00', '20', '10', NULL, '0', '0', '1', '2026-05-18 17:00:25', NULL),
('19', 'Pc Aio Hp Proone 440', 'G9 I7 12700t Ram 32gb, Ssd 1tb, W11 Pro Negro.', 'AIO1', NULL, '1', 'Unidad', '1', '4000.00', '2', '5', 'prod_6a0b96d4b5bf3_1779144404.jpg', '0', '1', '1', '2026-05-18 17:46:44', '2026-05-18 18:52:33');

-- -----------------------------------------------------
-- TABLA: proveedores
-- -----------------------------------------------------
DROP TABLE IF EXISTS `proveedores`;
CREATE TABLE `proveedores` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `ruc_dni` varchar(20) DEFAULT NULL,
  `contacto` varchar(100) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_proveedores_ruc_dni` (`ruc_dni`),
  KEY `idx_proveedores_activo` (`activo`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `proveedores` (`id`, `nombre`, `ruc_dni`, `contacto`, `telefono`, `email`, `direccion`, `activo`, `created_at`, `updated_at`) VALUES
('1', 'TechCorp S.A.', '20555555551', 'Juan Pérez', '555-0101', 'juan@techcorp.com', 'Av. Tecnológica 123', '1', '2026-05-13 15:11:35', NULL),
('2', 'Distribuidora Global', '20555555552', 'Pedro Ruiz', '555-0102', 'ventas@globaldist.com', 'Calle Comercio 45', '1', '2026-05-13 15:11:35', NULL),
('3', 'Insumos Médicos Plus', '20555555553', 'Sofia Castro', '555-0103', 'contacto@implus.com', 'Blvd. Salud 789', '1', '2026-05-13 15:11:35', NULL),
('4', 'Oficina Total E.I.R.L.', '20555555554', 'Javier Peña', '555-0104', 'ventas@ofitotal.com', 'Plaza Central L-5', '1', '2026-05-13 15:11:35', NULL),
('5', 'Ferretería Industrial', '20555555555', 'Raúl Sánchez', '555-0105', 'cotizaciones@ferreind.com', 'Zona Industrial 9', '1', '2026-05-13 15:11:35', NULL);

-- -----------------------------------------------------
-- TABLA: requisicion_detalles
-- -----------------------------------------------------
DROP TABLE IF EXISTS `requisicion_detalles`;
CREATE TABLE `requisicion_detalles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `requisicion_id` int(10) unsigned NOT NULL,
  `producto_id` int(10) unsigned NOT NULL,
  `cantidad_solicitada` int(11) NOT NULL,
  `cantidad_despachada` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_req_det_requisicion` (`requisicion_id`),
  KEY `idx_req_det_producto` (`producto_id`),
  CONSTRAINT `fk_req_det_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`),
  CONSTRAINT `fk_req_det_requisicion` FOREIGN KEY (`requisicion_id`) REFERENCES `requisiciones` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `requisicion_detalles` (`id`, `requisicion_id`, `producto_id`, `cantidad_solicitada`, `cantidad_despachada`, `created_at`) VALUES
('2', '2', '17', '1', '1', '2026-05-18 15:44:06');

-- -----------------------------------------------------
-- TABLA: requisiciones
-- -----------------------------------------------------
DROP TABLE IF EXISTS `requisiciones`;
CREATE TABLE `requisiciones` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `numero_requisicion` varchar(50) NOT NULL,
  `departamento_id` int(10) unsigned NOT NULL,
  `usuario_id` int(10) unsigned NOT NULL COMMENT 'Usuario que cre├│ la requisici├│n',
  `estado` enum('borrador','pendiente','aprobada','despachada','cancelada') DEFAULT 'borrador',
  `fecha_solicitud` date NOT NULL,
  `fecha_despacho` datetime DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_requisiciones_numero` (`numero_requisicion`),
  KEY `idx_requisiciones_departamento` (`departamento_id`),
  KEY `idx_requisiciones_usuario` (`usuario_id`),
  KEY `idx_requisiciones_estado` (`estado`),
  CONSTRAINT `fk_requisiciones_departamento` FOREIGN KEY (`departamento_id`) REFERENCES `departamentos` (`id`),
  CONSTRAINT `fk_requisiciones_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `requisiciones` (`id`, `numero_requisicion`, `departamento_id`, `usuario_id`, `estado`, `fecha_solicitud`, `fecha_despacho`, `notas`, `created_at`, `updated_at`) VALUES
('2', 'REQ-20260518-0001', '2', '4', 'despachada', '2026-05-18', '2026-05-18 15:44:46', '', '2026-05-18 15:44:06', '2026-05-18 15:44:46');

-- -----------------------------------------------------
-- TABLA: rol_permiso
-- -----------------------------------------------------
DROP TABLE IF EXISTS `rol_permiso`;
CREATE TABLE `rol_permiso` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rol_id` int(10) unsigned NOT NULL,
  `permiso_id` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_rol_permiso` (`rol_id`,`permiso_id`),
  KEY `fk_rp_permiso` (`permiso_id`),
  CONSTRAINT `fk_rp_permiso` FOREIGN KEY (`permiso_id`) REFERENCES `permisos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rp_rol` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `rol_permiso` (`id`, `rol_id`, `permiso_id`, `created_at`) VALUES
('1', '1', '12', '2026-04-20 23:48:02'),
('2', '1', '13', '2026-04-20 23:48:02'),
('3', '1', '6', '2026-04-20 23:48:02'),
('4', '1', '7', '2026-04-20 23:48:02'),
('5', '1', '8', '2026-04-20 23:48:02'),
('6', '1', '9', '2026-04-20 23:48:02'),
('7', '1', '20', '2026-04-20 23:48:02'),
('8', '1', '21', '2026-04-20 23:48:02'),
('9', '1', '1', '2026-04-20 23:48:02'),
('10', '1', '10', '2026-04-20 23:48:02'),
('11', '1', '11', '2026-04-20 23:48:02'),
('12', '1', '2', '2026-04-20 23:48:02'),
('13', '1', '3', '2026-04-20 23:48:02'),
('14', '1', '4', '2026-04-20 23:48:02'),
('15', '1', '5', '2026-04-20 23:48:02'),
('16', '1', '14', '2026-04-20 23:48:02'),
('17', '1', '15', '2026-04-20 23:48:02'),
('18', '1', '22', '2026-04-20 23:48:02'),
('19', '1', '23', '2026-04-20 23:48:02'),
('20', '1', '16', '2026-04-20 23:48:02'),
('21', '1', '17', '2026-04-20 23:48:02'),
('22', '1', '18', '2026-04-20 23:48:02'),
('23', '1', '19', '2026-04-20 23:48:02'),
('32', '2', '12', '2026-04-20 23:48:02'),
('38', '2', '1', '2026-04-20 23:48:02'),
('39', '2', '10', '2026-04-20 23:48:02'),
('40', '2', '11', '2026-04-20 23:48:02'),
('41', '2', '2', '2026-04-20 23:48:02'),
('47', '3', '12', '2026-04-20 23:48:02'),
('48', '3', '1', '2026-04-20 23:48:02'),
('49', '3', '11', '2026-04-20 23:48:02'),
('50', '3', '10', '2026-04-20 23:48:02'),
('51', '3', '2', '2026-04-20 23:48:02'),
('52', '1', '24', '2026-04-23 17:42:34'),
('53', '1', '25', '2026-04-23 17:42:34'),
('54', '1', '26', '2026-04-23 17:42:34'),
('55', '1', '27', '2026-04-23 17:42:34'),
('56', '1', '28', '2026-04-23 17:42:34'),
('57', '1', '29', '2026-04-23 17:42:34'),
('58', '1', '30', '2026-04-23 17:42:34'),
('59', '1', '31', '2026-04-23 17:42:34'),
('75', '1', '32', '2026-04-26 18:19:41'),
('76', '1', '33', '2026-04-26 18:19:41'),
('77', '1', '34', '2026-04-26 18:19:41'),
('78', '1', '35', '2026-04-26 18:19:41'),
('79', '1', '36', '2026-04-26 18:19:41'),
('84', '2', '34', '2026-04-26 18:19:41'),
('85', '2', '35', '2026-04-26 18:19:41'),
('86', '2', '36', '2026-04-26 18:19:41'),
('87', '1', '37', '2026-04-28 16:58:58'),
('88', '1', '38', '2026-04-28 16:58:58'),
('89', '1', '39', '2026-04-28 16:58:58'),
('90', '2', '37', '2026-04-28 16:58:58'),
('91', '2', '38', '2026-04-28 16:58:58'),
('92', '2', '39', '2026-04-28 16:58:58'),
('93', '3', '38', '2026-04-28 16:58:58'),
('94', '3', '37', '2026-04-28 16:58:58'),
('95', '1', '40', '2026-05-05 12:44:22'),
('96', '1', '41', '2026-05-05 12:44:22'),
('97', '1', '42', '2026-05-05 12:44:22'),
('98', '1', '43', '2026-05-05 12:44:22'),
('99', '2', '40', '2026-05-05 16:07:34'),
('100', '2', '41', '2026-05-05 16:07:34');

-- -----------------------------------------------------
-- TABLA: roles
-- -----------------------------------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_roles_nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `roles` (`id`, `nombre`, `descripcion`, `created_at`, `updated_at`) VALUES
('1', 'Admin', 'Administrador del sistema con acceso total', '2026-04-20 23:48:02', NULL),
('2', 'Supervisor', 'Supervisor con acceso a reportes y gestión', '2026-04-20 23:48:02', '2026-04-25 13:36:34'),
('3', 'Operador', 'Operador con acceso básico a inventario', '2026-04-20 23:48:02', '2026-04-25 13:36:34');

-- -----------------------------------------------------
-- TABLA: sesiones
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sesiones`;
CREATE TABLE `sesiones` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` int(10) unsigned NOT NULL,
  `token` varchar(255) NOT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `inicio` datetime NOT NULL DEFAULT current_timestamp(),
  `ultimo_acceso` datetime NOT NULL DEFAULT current_timestamp(),
  `activa` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_sesiones_usuario` (`usuario_id`),
  KEY `idx_sesiones_token` (`token`),
  KEY `idx_sesiones_activa` (`activa`),
  CONSTRAINT `fk_sesiones_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=119 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `sesiones` (`id`, `usuario_id`, `token`, `ip`, `user_agent`, `inicio`, `ultimo_acceso`, `activa`) VALUES
('1', '1', '548bea8e2ea807b7fd909d383accabac9b8a36c9726da316d8178886825fc689', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 19:19:03', '2026-04-20 19:19:03', '0'),
('2', '1', 'd3c739f6db734af77102abafa05196cc603eec1edddb1eff1710ce4aac5710ef', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 19:29:42', '2026-04-20 19:29:42', '0'),
('3', '1', 'bb925cf106d43593ab4d67f8819f04e20fcd78acc333ef5f446fff4295551f8b', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 19:52:58', '2026-04-20 19:52:58', '1'),
('4', '1', 'c93139d85c221015b6d2bde63cb33090e0d68ee56ece7062ceea1a435d4ae0b5', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 17:44:24', '2026-04-23 17:44:24', '1'),
('5', '1', 'fbaac6313c5af7e7ae3f6f7d2fcae654ba5fff2a89e567443643e50a2f07936e', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 18:22:27', '2026-04-23 18:22:27', '1'),
('6', '1', '9358c9bbb6514aeb75c6c1f0fc66bb227ddeba6a178ce67829b6a5b9ba5e7df6', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 18:31:26', '2026-04-23 18:31:26', '1'),
('7', '1', '59c6f3d553511adf0983f681e8dfbab037dfef34a639ef063adbfd39da15eb7c', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 18:50:07', '2026-04-23 18:50:07', '1'),
('8', '1', 'afd75776369fe56d457ea65a7aa6f07182491973367fd7fc636f8983d87e96c2', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 19:14:13', '2026-04-23 19:14:13', '1'),
('9', '1', '0c9422f423e84b12b8f3dcd8432ea9e561c7bbaf9a42a52654dc6665c293940b', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 19:57:25', '2026-04-23 19:57:25', '1'),
('10', '1', '3124ca3a4fdb52d442bf6eb67f4740438dce9e613deedb9343ebb87564ab7d80', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 13:02:17', '2026-04-24 13:02:17', '1'),
('11', '1', '8a9de94367b144a87242464b6e64aa463fc3a19d32d20228ea556cc2cfffbd98', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 13:48:17', '2026-04-24 13:48:17', '1'),
('12', '1', '2b73fcfcda2ffee0fdbb6b648e2c2c8d4ca43e770c9a63d206c7a1cc69075b95', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 14:00:10', '2026-04-24 14:00:10', '1'),
('13', '1', 'c8557ad58f5fa279360c165e07814b6d4efd17aefcef1fe5fe4a2291eb3ce3fa', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 14:28:27', '2026-04-24 14:28:27', '1'),
('14', '1', 'b66faf82d6a56e5c2cbad46274adfbf5c3bcb82f71ccbb2d45dad3ba0325025f', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 14:39:03', '2026-04-24 14:39:03', '1'),
('15', '1', '1031bd4fb037a554e30ae9d599aa0646c2ad10a09db030b523f3f07f1befd009', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 14:53:04', '2026-04-24 14:53:04', '1'),
('16', '1', '6a5cccb12f3de77d1166fab92d16d773c4beaae898b7dd42f62700f72032b201', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 15:25:47', '2026-04-24 15:25:47', '1'),
('17', '1', '700725e05be0ac5742fd55123234a586aaa2ea9fb852bc7c41c973698433eaaf', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 18:45:50', '2026-04-24 18:45:50', '1'),
('18', '1', '68ebf3674646c1d0f7fa43ac059dbb90cee563ba6f2d6070932e25431cdc1e1a', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 19:14:02', '2026-04-24 19:14:02', '1'),
('19', '4', '8e3ac2c48a79c653d08e219c77deb586daa93493253da4a52363fd64841c5370', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-25 12:51:22', '2026-04-25 12:51:22', '0'),
('20', '1', 'ebfab1435349afc19f0ae201f13e8175029aeacdde951cea0c0015c8492f93cf', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-25 12:59:23', '2026-04-25 12:59:23', '1'),
('21', '4', '4d24e0d4d16f6f81e17501a0bbc1eecd532be124590087df11e63788523d1789', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-25 13:22:00', '2026-04-25 13:22:00', '0'),
('22', '1', '2981e6bf15300dda57be98337389112b38cb3646a8af4bae302bcca0a5033230', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-25 13:38:25', '2026-04-25 13:38:25', '1'),
('23', '4', '654a839625f7a810a2d57f8d9fb94bf19e93734d6b96f78fb00bfb7fb1f46825', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-25 18:20:25', '2026-04-25 18:20:25', '0'),
('24', '1', '307b98e4ebae6ab4e5373b92eafc2574bb763e69495b4c490895ad6cff0baab0', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-25 18:31:44', '2026-04-25 18:31:44', '1'),
('25', '1', '7543eec7c66cae7b7b3919c891467c2e451e072a44b1a8be42e5d50e335fd526', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-25 19:04:42', '2026-04-25 19:04:42', '1'),
('26', '1', '38a2ea0057f145feb636741a1813e79fbe118eb356f1c58fda110f05e4e77ab5', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-25 19:54:02', '2026-04-25 19:54:02', '1'),
('27', '1', '306ba383c7b14db3c8f329652e121073807ef7f47c1df5b2fd44c18f2c2bb718', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-25 20:29:36', '2026-04-25 20:29:36', '1'),
('28', '1', 'fa14eca614d0226c9371e56c5aec83dd9800ad3f34cbd8f6d497e6fa47216908', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-25 21:11:49', '2026-04-25 21:11:49', '1'),
('29', '1', '28b070e53f41589943342b258677a2ec233937e576ff6f86a4688e87d2423658', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-25 21:40:12', '2026-04-25 21:40:12', '1'),
('30', '1', 'a9c3a03e32c7fdc2737795bb8fe229b18a0f6f624ef36fcbbf9596c58cd1c25e', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 15:02:24', '2026-04-26 15:02:24', '1'),
('31', '4', 'e4a7bde033f4cf294cd9e249dba524df71541940270539aa3e4e70281953ecd6', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 15:03:54', '2026-04-26 15:03:54', '0'),
('32', '6', '023dcc545592bb3e65bfcafa1b92c4cf47c54e1724e3403829b14d72996f9245', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 15:26:15', '2026-04-26 15:26:15', '0'),
('33', '1', '3a0603759b0892198aff6c6f04439be124c79ee42fd49c7d5be89a9e1cff4a84', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 15:27:25', '2026-04-26 15:27:25', '1'),
('34', '1', '34ef3b47172f74b001d90f50f7bdc701014a38ec10e8ad931af82c9512c9953a', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 15:44:25', '2026-04-26 15:44:25', '1'),
('35', '1', 'c6e176e70aa8301c16e666c668bf3ca2cf3a53bc45288b00efdc4de0261b290a', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 16:08:30', '2026-04-26 16:08:30', '1'),
('36', '1', 'a678e312e39edaec61a15526604eeac59237ed5e044b8202e7c9ea5bb6c01daa', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 17:30:27', '2026-04-26 17:30:27', '1'),
('37', '1', 'a1133ecdfd88f9095edad9717b5ade9e55c7a8392d9ecb702e0190f9cbc72969', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 18:28:27', '2026-04-26 18:28:27', '1'),
('38', '1', '431a22639b9015c444ca3dcfb78644b774c908da0fcdaffaaaa8db7146a2aa06', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 18:32:56', '2026-04-26 18:32:56', '1'),
('39', '1', '690dcad5374d67edfcc7d8fa2181e5fe9c6a2c5c8be0091c7bf37170e5b70904', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-26 18:45:56', '2026-04-26 18:45:56', '1'),
('40', '4', '75317a9a60a472166ee2eeb4572bf9b40de2a0d1212593e71c604afc9f700b06', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 12:06:46', '2026-04-27 12:06:46', '0'),
('41', '1', '44ad1e07fc6f2ec94a635dfcee4a6033e1402fd6508ecefba07f71df8a729f1a', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-27 12:33:45', '2026-04-27 12:33:45', '1'),
('42', '4', 'e5c42888e6e513f3a6f95a11568e5c41c2909d5668a72aab320e79054459a26e', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:01:19', '2026-04-28 12:01:19', '0'),
('43', '1', '0096ba189ee6244fc1019c5ef811cba86ed90f4644000f59b7370d5b22f8cc1d', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:05:34', '2026-04-28 12:05:34', '1'),
('44', '1', 'aaf1481e8e03239118a7ef5170942d15e0321011cf088261b906102dcc8a5686', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 12:37:11', '2026-04-28 12:37:11', '1'),
('45', '4', 'e3380fbbf62e03da6dc0375e3df590ce7e66f51169a60b35eca3ae0450c4e8fa', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 16:27:08', '2026-04-28 16:27:08', '0'),
('46', '1', '969649bf6a7b27460eb03ea33aa1096ae737853bd06cc57368e7e840b66c4742', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 17:30:04', '2026-04-28 17:30:04', '1'),
('47', '1', '83f500ab0b44f52fbb4da7ecd255f55fb21b8634e2c4241a96dc36ce2156d8fd', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-28 17:59:33', '2026-04-28 17:59:33', '1'),
('48', '4', '6592fd3e09aac615bbcaa99b31b5f9d3d86ce76ae3d71ca71151398c9393b3b3', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-29 12:48:32', '2026-04-29 12:48:32', '0'),
('49', '4', '192982a38c977d83db98605ab5945ecd57bd8a6cd561055a7a4213f4d942af59', '::1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1', '2026-04-29 19:29:55', '2026-04-29 19:29:55', '0'),
('50', '4', '90d48bb39c65858aaeb60fb6fb442147c549b3cadb9e39283aaca9c42c9126c5', '::1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1', '2026-04-30 11:33:28', '2026-04-30 11:33:28', '0'),
('51', '4', '5cd0aa77f476ad9ce616a70ba61fd7786254d19de18371395e1be83808cf0481', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-04 13:00:30', '2026-05-04 13:00:30', '0'),
('52', '4', '68e20f124b7697803f23811b516a14cff909cb31c550427ad342bec3f52fb9f9', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-04 13:46:22', '2026-05-04 13:46:22', '0'),
('53', '4', '450347f491cfa2cc16fa6ffe8f60395c166f027ec2202853d9b16b579419a13d', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-04 13:49:14', '2026-05-04 13:49:14', '0'),
('54', '4', '4ac2d5e72867126ced412f1a7856f9f1389d077815f69dfe2323df783dc4ac46', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-04 20:46:09', '2026-05-04 20:46:09', '0'),
('55', '4', '2817f4d2aff16d7a057e8811cd8503f89917adb6c535e0dc0152691f7d954d7e', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-04 21:05:23', '2026-05-04 21:05:23', '0'),
('56', '4', 'ba79558ad3ece3f1bcf37120756b6765aaa069fc1dac454e3145e0a0c56ab479', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-05 11:48:06', '2026-05-05 11:48:06', '0'),
('57', '1', '0a050805f2e752c2c8039fcc90cad2f9600a1de0033ecb4c5b40647f6c7a40ac', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-05 11:59:20', '2026-05-05 11:59:20', '1'),
('58', '1', 'd0b60185a8de2a334404b3122ebc26d823d0ad7c7a5c8ca186b37ec2c6509c5e', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-05 12:45:43', '2026-05-05 12:45:43', '1'),
('59', '4', 'e28baf2c23fba093b87cdea82f868c71a8b2f84dd6fd4460e91e5a1d310ddec9', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-05 15:23:39', '2026-05-05 15:23:39', '0'),
('60', '1', '6a9e1c8482a67c071fc2abac6567644e17d75ed9f5d9840d57c3ca19d7ae5ed4', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-05 15:37:23', '2026-05-05 15:37:23', '1'),
('61', '6', '113b703a2bbbdd6e2b21391edc783f8efaec53414eb4a1a3d9bfaa130dd88f4e', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-05 15:49:21', '2026-05-05 15:49:21', '0'),
('62', '4', '382bc23f2549325037db00594aed7ab3ed440342b1909f6e4b78b8ba678f4322', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-05 15:50:51', '2026-05-05 15:50:51', '0'),
('63', '2', '40d4b99e76abb7a3feb8fc5a9abc9b58d9978c69b7a9f59ef006d19178feba8f', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-05 16:01:56', '2026-05-05 16:01:56', '0'),
('64', '4', 'db3c321459b030f42fe141fbe547691ca842eec4f22d91a1e3f3d0c797b1c6ab', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-05 16:02:47', '2026-05-05 16:02:47', '0'),
('65', '6', 'eaed9ac65c12dfb9c4212a551494a9e5bee1c40ca1ce8df325d82c4532c2ec83', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-05 16:12:06', '2026-05-05 16:12:06', '0'),
('66', '2', 'b51f696c6a4dd481c725e1c79c5c09a433d6f999c69a1a2b6c71e5ba36d5e9e3', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-05 16:12:38', '2026-05-05 16:12:38', '0'),
('67', '2', 'f11c256430163e65354772b1c9895748be1f96bd9ff26c8ff52730f761dd20ed', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-05 16:27:31', '2026-05-05 16:27:31', '0'),
('68', '2', '2a7d9d20e65bf9e3d5baa900d133734b36d83c1d0509e10e4302cb15b48d1b77', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-05 16:49:34', '2026-05-05 16:49:34', '0'),
('69', '2', 'f1c8af0ca51723d26d397815976b049edeec2dad3c632cd4248248ddd1d35762', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-05 16:51:17', '2026-05-05 16:51:17', '0'),
('70', '2', 'f18b3c0ccaaf74bf38d0207832357f5d0f6f6fdd4c48a18b5ef2d369b615e96d', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-05 17:02:50', '2026-05-05 17:02:50', '0'),
('71', '2', 'ec75677aea1c0fee8614b72c70d48bbf4d0c1945a62ba606f98cd72c09229e4d', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-05 17:11:54', '2026-05-05 17:11:54', '0'),
('72', '4', '3035ce5df34b08d2480f89d9dbde60b9cd6b0d8d72ce92010a0d34a87e1018a9', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-05 17:12:37', '2026-05-05 17:12:37', '0'),
('73', '2', 'acffc7756bc22a6f10c406edd3c899b626d2a685ccf7172c120a791370744f70', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-05 17:21:41', '2026-05-05 17:21:41', '1'),
('74', '4', 'ee8f054d7ec345db3703b73c7dce12d83267ee41812abe21a07a0f2ffebd2711', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 11:02:08', '2026-05-06 11:02:08', '0'),
('75', '1', '7f617ca8a8ae93f57cb29cfe335799638fc3af59b204cb69931be89445268fd9', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 12:05:00', '2026-05-06 12:05:00', '1'),
('76', '4', 'db2c87d2396624af37c6a6d83aef19b7dc88a07ee10253cc879521589871b03d', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 17:26:09', '2026-05-06 17:26:09', '0'),
('77', '1', '9f2aa8ed7ada52ae22e4fa3964bf0db01e7bba1310c4d2f0e926d36eb8157ce9', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 18:14:21', '2026-05-06 18:14:21', '1'),
('78', '1', 'e519f7d64dc2a698aad795d4bd7d23c5b45f45924d075a6fe6c0887776e238eb', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 18:22:40', '2026-05-06 18:22:40', '1'),
('79', '1', '4254c5e8afebe3d6a1c27ae2741faeecb020c22c1529307f0d6a561083af1185', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 19:47:30', '2026-05-06 19:47:30', '1'),
('80', '1', 'a65ba9ce1601e47f6bc86fa86dccd68d6c8600d7e69a3f752d750fc2a8bdb545', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 19:50:54', '2026-05-06 19:50:54', '1'),
('81', '1', '8d7e6314f10f38e341a1fd5c54995b0a2c25aa46f2a3b05060c47d917e93877b', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 20:17:17', '2026-05-06 20:17:17', '1'),
('82', '4', '5a6d527929a7d1e2c732dfe1eb634ab1efab744af0a2a6d6fbaefbf1a6169cf4', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 20:44:43', '2026-05-06 20:44:43', '0'),
('83', '4', '881f02e572d68879e1943babeb56ec415b03c7f3282ed5b769f51ed8ce09ae3e', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 14:04:34', '2026-05-08 14:04:34', '0'),
('84', '4', '07b87394e5ff4eb75bc749593160bd96836aa269858d1b2761c4c7654ed04c21', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-09 17:10:09', '2026-05-09 17:10:09', '0'),
('85', '4', '8fde0e15c6c197ffc8178763925d90279d6fd8c993a938c213d085674df497aa', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-11 14:20:44', '2026-05-11 14:20:44', '0'),
('86', '6', '68087b83eb7b460fed94f027a8cee9e5a4ddf089a2331214a71730ba2cad41d8', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-11 14:32:37', '2026-05-11 14:32:37', '0'),
('87', '6', '16e0755e796a7aa5947cf0fc5f3e8f5939b662a65c1d237b2159258789e72f99', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-11 15:05:33', '2026-05-11 15:05:33', '0'),
('88', '4', '37b6f7c6c5417d698192f4224fe66bc741983147f180f41fc52f98707fdb4c4c', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-11 15:53:00', '2026-05-11 15:53:00', '0'),
('89', '3', 'e9858ccd144c7215ac88d28b924d87fc4a666f483aac94cf12dd5c317ed0eee4', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-11 15:54:46', '2026-05-11 15:54:46', '0'),
('90', '4', '927831b1622f8afbf52c8224a08c4693d04368e77264598b986ca176812ec835', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-11 15:55:54', '2026-05-11 15:55:54', '0'),
('91', '4', 'ebab5d22ce3e3a0f22aa16bb0147f65cb0082279c27210af04110c9a26b546ee', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-12 15:43:53', '2026-05-12 15:43:53', '0'),
('92', '1', '2d0bb8037f1081cdb431246a1284c7c578ab69661179dbf48c47578e89c87c72', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-12 17:37:21', '2026-05-12 17:37:21', '1'),
('93', '4', 'c12c9941d159a66999df1ac7328772ed1db05330efe8c434acdd6d86e760e63e', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 12:35:24', '2026-05-13 12:35:24', '0'),
('94', '4', 'bb5493c2011ef039ee7500f20123d225911295ad92f607c6c0f89b3c989d619b', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 18:57:12', '2026-05-13 18:57:12', '0'),
('95', '1', '21a8555d06c7676b1ac26144389e1569f2e1fa8e4b55189b92bc79abb7a62a4b', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 21:51:57', '2026-05-13 21:51:57', '1'),
('96', '4', '54d51a47cfc6464450a88da08cabe48c2aac775acf51341492eff87041005593', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 21:52:31', '2026-05-13 21:52:31', '0'),
('97', '1', 'a7cfb588ad0b17535e09df911b2dacb34e224f8d3e68b5931083fb72b15d23ad', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 22:09:23', '2026-05-13 22:09:23', '1'),
('98', '1', 'e1aa0f5ff325e5a0fc396e975688fd7ccd6204ec3287600475fa1ee29942bebe', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 22:29:22', '2026-05-13 22:29:22', '1'),
('99', '1', 'd0125db56dde9ccd6c9bfbda5b27a37712b4b79cc70e42353e3c5a15b4a999ae', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 22:35:43', '2026-05-13 22:35:43', '1'),
('100', '4', '087707a08997a2d3f9928de1e1f8526c4c76ed952f91de78d870c2629475a295', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 22:43:36', '2026-05-13 22:43:36', '0'),
('101', '1', '31a17aa83895a853c86d5a74fd9091cc2d7266d4fe830414c44518b52051bd89', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 22:59:50', '2026-05-13 22:59:50', '1'),
('102', '4', 'b53c3095206a82a1ed105d914ada8eee02c2fefff62480cde9da70fdc8cc9994', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 23:07:48', '2026-05-13 23:07:48', '0'),
('103', '4', '83d9b94ab4b1d94a7e1ca12f80698ee32767cc2a7acbbbc8e87ca9b38cb4ed98', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-14 11:40:03', '2026-05-14 11:40:03', '0'),
('104', '1', 'f21a656f9ddcf746a731b6b8e3d6eb2915417dd849344a9fbd7c5a2a5213a364', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-14 13:03:35', '2026-05-14 13:03:35', '1'),
('105', '4', 'c148ecffa56b2c1cbd50a4d929940bd3560bef32c7da75aa6230c33d2d02e3eb', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-14 15:26:54', '2026-05-14 15:26:54', '0'),
('106', '4', '9d8774352f9bd44c4d1f9f2b316ebe65e482c519072fe12f8af7149e32df6050', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-15 12:14:43', '2026-05-15 12:14:43', '0'),
('107', '4', '3afec0c6f997199ac4d63b9c9402907f1191698e7e40e06c746d6520c400581f', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-16 12:32:03', '2026-05-16 12:32:03', '0'),
('108', '1', '2fd94ca8cdf0cb665ac1c025455adce65039e435715dbbb311e918c08bf276f0', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-16 12:47:38', '2026-05-16 12:47:38', '1'),
('109', '1', '61585737f1a0d7ab15d0267629f5d0e2912c61f8432a81f88e256f579421e863', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-16 13:16:46', '2026-05-16 13:16:46', '1'),
('110', '1', '9ff50edfc0d58e6ce60e27e07fd3919e7de59076e1ab93b5904297dc21eacea0', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-16 13:21:18', '2026-05-16 13:21:18', '1'),
('111', '4', 'd90db61af219151ebc985c708f6abdaf4cf5fa28e2fd598a31ebf5e689cc2a4f', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-18 11:39:59', '2026-05-18 11:39:59', '0'),
('112', '4', 'd4f7fa8d0e9f5ff56e112e572163db92906f3209fb0e0143669ad58c28df1340', '::1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1', '2026-05-18 15:38:20', '2026-05-18 15:38:20', '0'),
('113', '4', 'c2d92462a54bed8e160f341a37b2f15415f64601303cf3c505b2e774959e87e6', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-19 20:39:33', '2026-05-19 20:39:33', '0'),
('114', '4', '794d0ce57667066d74582416d01c0650f0cc1406b623f29a37a0a574957bbba4', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-21 11:08:56', '2026-05-21 11:08:56', '0'),
('115', '4', '95a43ae23cd2b1b5a78bbce3b30d0c206b6ad8d48f1b7d65f88f1f4b1a477a7b', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-21 18:56:18', '2026-05-21 18:56:18', '0'),
('116', '4', '1ed384199a81283c731d3c73b755cc7bffd1feb95040f6d9fa2f46273ab655b6', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-21 18:56:41', '2026-05-21 18:56:41', '1'),
('117', '4', '1a671f7b948556c2ac729ab69204369f3f3a496c341e8c3fcf53518f79e053a2', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-24 14:35:56', '2026-05-24 14:35:56', '1'),
('118', '4', '8d2140458756e9a8671b7d3488d6aa95946a1d5658fb88e50f5c1ed8fd798e72', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-24 18:40:45', '2026-05-24 18:40:45', '1');

-- -----------------------------------------------------
-- TABLA: ubicaciones
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ubicaciones`;
CREATE TABLE `ubicaciones` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `activa` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_ubicaciones_nombre` (`nombre`),
  KEY `idx_ubicaciones_activa` (`activa`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `ubicaciones` (`id`, `nombre`, `descripcion`, `activa`, `created_at`, `updated_at`) VALUES
('1', 'Almacén Principal (A1)', 'Zona principal de almacenamiento techado', '1', '2026-05-13 15:11:35', NULL),
('2', 'Bodega Secundaria (B2)', 'Espacio para inventario de baja rotación', '1', '2026-05-13 15:11:35', NULL),
('3', 'Cuarto de Herramientas', 'Área de seguridad para equipos valiosos', '1', '2026-05-13 15:11:35', NULL),
('4', 'Punto de Venta Norte', 'Exhibición para atención al cliente', '1', '2026-05-13 15:11:35', NULL),
('5', 'Zona Fría (C1)', 'Almacenamiento con control de temperatura', '1', '2026-05-13 15:11:35', NULL);

-- -----------------------------------------------------
-- TABLA: user_settings
-- -----------------------------------------------------
DROP TABLE IF EXISTS `user_settings`;
CREATE TABLE `user_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` int(10) unsigned NOT NULL,
  `clave` varchar(100) NOT NULL,
  `valor` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_settings` (`usuario_id`,`clave`),
  CONSTRAINT `fk_us_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `user_settings` (`id`, `usuario_id`, `clave`, `valor`, `created_at`, `updated_at`) VALUES
('1', '1', 'tema', 'light', '2026-04-20 23:48:03', '2026-05-16 13:04:48'),
('2', '2', 'tema', 'light', '2026-04-20 23:48:03', NULL),
('3', '3', 'tema', 'light', '2026-04-20 23:48:03', NULL),
('4', '4', 'tema', 'light', '2026-04-25 13:15:34', '2026-05-24 18:44:34');

-- -----------------------------------------------------
-- TABLA: usuarios
-- -----------------------------------------------------
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `rol_id` int(10) unsigned NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `intentos_fallidos` int(10) unsigned NOT NULL DEFAULT 0,
  `bloqueado_hasta` datetime DEFAULT NULL,
  `ultimo_login` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_usuarios_email` (`email`),
  KEY `idx_usuarios_rol` (`rol_id`),
  KEY `idx_usuarios_activo` (`activo`),
  CONSTRAINT `fk_usuarios_rol` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `telefono`, `password`, `rol_id`, `activo`, `intentos_fallidos`, `bloqueado_hasta`, `ultimo_login`, `created_at`, `updated_at`) VALUES
('1', 'Administrador', 'admin@invsys.com', NULL, '$2y$10$.74.6ymWfm3gn7QmGRGzReb3EGMBptyIiI6QvQ1nE3SscBfNQ.lLG', '1', '1', '0', NULL, '2026-05-16 13:21:18', '2026-04-20 23:48:02', '2026-05-16 13:21:18'),
('2', 'Supervisor Demo', 'supervisor@invsys.com', NULL, '$2y$10$.74.6ymWfm3gn7QmGRGzReb3EGMBptyIiI6QvQ1nE3SscBfNQ.lLG', '2', '1', '0', NULL, '2026-05-05 17:21:41', '2026-04-20 23:48:02', '2026-05-05 17:21:41'),
('3', 'Operador Demo', 'operador@invsys.com', NULL, '$2y$10$.74.6ymWfm3gn7QmGRGzReb3EGMBptyIiI6QvQ1nE3SscBfNQ.lLG', '3', '1', '0', NULL, '2026-05-11 15:54:46', '2026-04-20 23:48:02', '2026-05-11 15:54:46'),
('4', 'josue lopez', 'josuexd123lc@gmail.com', '+51931993019', '$2y$10$X5SFy9DJK.0e1./5MnmOBO75IuPpVO90ZFx5HiZ.GmgV1uoOMhV/u', '1', '1', '0', NULL, '2026-05-24 18:40:45', '2026-04-24 19:44:20', '2026-05-24 18:40:45'),
('6', 'Test User', 'test@test.com', NULL, '$2y$10$ZZ6ajBG3hbkdzQQ70vgsmexTgCKBRMkqVsSrAfx63eGDOx6nFrX72', '3', '0', '0', NULL, '2026-05-11 15:05:33', '2026-04-26 15:25:26', '2026-05-13 12:49:59');

SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
COMMIT;

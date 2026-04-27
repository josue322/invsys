-- =====================================================
-- Migración: Fases 6 y 7 (Departamentos y Requisiciones)
-- =====================================================

USE `invsys_db`;

-- 1. Crear tabla departamentos
CREATE TABLE IF NOT EXISTS `departamentos` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(150) NOT NULL,
    `responsable` VARCHAR(150) DEFAULT NULL,
    `centro_costo` VARCHAR(50) DEFAULT NULL,
    `telefono` VARCHAR(50) DEFAULT NULL,
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_departamentos_nombre` (`nombre`),
    INDEX `idx_departamentos_activo` (`activo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Modificar la tabla movimientos para añadir departamento_id
-- (No hacemos la foreign key restrictiva aún si hay movimientos viejos, pero sí definimos la columna)
ALTER TABLE `movimientos` 
ADD COLUMN `departamento_id` INT UNSIGNED DEFAULT NULL AFTER `proveedor_id`,
ADD CONSTRAINT `fk_movimientos_departamento` FOREIGN KEY (`departamento_id`) REFERENCES `departamentos`(`id`) ON DELETE SET NULL;

-- 3. Crear tabla requisiciones
CREATE TABLE IF NOT EXISTS `requisiciones` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `numero_requisicion` VARCHAR(50) NOT NULL,
    `departamento_id` INT UNSIGNED NOT NULL,
    `usuario_id` INT UNSIGNED NOT NULL COMMENT 'Usuario que creó la requisición',
    `estado` ENUM('borrador', 'pendiente', 'despachada', 'cancelada') NOT NULL DEFAULT 'borrador',
    `fecha_solicitud` DATE NOT NULL,
    `fecha_despacho` DATETIME DEFAULT NULL,
    `notas` TEXT DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_requisiciones_numero` (`numero_requisicion`),
    INDEX `idx_requisiciones_departamento` (`departamento_id`),
    INDEX `idx_requisiciones_usuario` (`usuario_id`),
    INDEX `idx_requisiciones_estado` (`estado`),
    CONSTRAINT `fk_requisiciones_departamento` FOREIGN KEY (`departamento_id`) REFERENCES `departamentos`(`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_requisiciones_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Crear tabla requisicion_detalles
CREATE TABLE IF NOT EXISTS `requisicion_detalles` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `requisicion_id` INT UNSIGNED NOT NULL,
    `producto_id` INT UNSIGNED NOT NULL,
    `cantidad_solicitada` INT NOT NULL,
    `cantidad_despachada` INT NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_req_det_requisicion` (`requisicion_id`),
    INDEX `idx_req_det_producto` (`producto_id`),
    CONSTRAINT `fk_req_det_requisicion` FOREIGN KEY (`requisicion_id`) REFERENCES `requisiciones`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_req_det_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Insertar permisos básicos
INSERT IGNORE INTO `permisos` (`modulo`, `accion`, `descripcion`) VALUES
('departamentos', 'ver', 'Ver catálogo de departamentos'),
('departamentos', 'crear', 'Crear y editar departamentos'),
('requisiciones', 'ver', 'Ver historial de requisiciones'),
('requisiciones', 'crear', 'Crear nuevas requisiciones'),
('requisiciones', 'despachar', 'Aprobar y despachar requisiciones (salida de stock)');

-- 6. Asignar permisos al rol Administrador (ID 1) y Almacenero (ID 2 - si existe)
INSERT IGNORE INTO `rol_permiso` (`rol_id`, `permiso_id`)
SELECT 1, id FROM `permisos` WHERE `modulo` IN ('departamentos', 'requisiciones');

INSERT IGNORE INTO `rol_permiso` (`rol_id`, `permiso_id`)
SELECT 2, id FROM `permisos` WHERE `modulo` IN ('departamentos', 'requisiciones');

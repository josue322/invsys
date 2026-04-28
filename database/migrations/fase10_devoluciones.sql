-- =====================================================
-- Migración: Fase 10 (Devoluciones - Logística Inversa)
-- =====================================================

USE `invsys_db`;

-- 1. Modificar tabla movimientos para aceptar el tipo 'devolucion'
ALTER TABLE `movimientos` 
MODIFY COLUMN `tipo` ENUM('entrada','salida','ajuste','transferencia','devolucion') NOT NULL;

-- 2. Crear tabla devoluciones
CREATE TABLE IF NOT EXISTS `devoluciones` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `numero_devolucion` VARCHAR(50) NOT NULL,
    `departamento_id` INT UNSIGNED NOT NULL,
    `requisicion_id` INT UNSIGNED DEFAULT NULL COMMENT 'Opcional: Requisición de la que provienen',
    `usuario_id` INT UNSIGNED NOT NULL COMMENT 'Usuario que registró la devolución',
    `estado` ENUM('pendiente', 'aprobada', 'rechazada') NOT NULL DEFAULT 'pendiente',
    `fecha_solicitud` DATE NOT NULL,
    `fecha_procesamiento` DATETIME DEFAULT NULL,
    `notas` TEXT DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_devoluciones_numero` (`numero_devolucion`),
    INDEX `idx_devoluciones_departamento` (`departamento_id`),
    INDEX `idx_devoluciones_usuario` (`usuario_id`),
    INDEX `idx_devoluciones_estado` (`estado`),
    CONSTRAINT `fk_devoluciones_departamento` FOREIGN KEY (`departamento_id`) REFERENCES `departamentos`(`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_devoluciones_requisicion` FOREIGN KEY (`requisicion_id`) REFERENCES `requisiciones`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_devoluciones_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Crear tabla devolucion_detalles
CREATE TABLE IF NOT EXISTS `devolucion_detalles` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `devolucion_id` INT UNSIGNED NOT NULL,
    `producto_id` INT UNSIGNED NOT NULL,
    `lote_id` INT UNSIGNED DEFAULT NULL,
    `cantidad` INT NOT NULL,
    `motivo` VARCHAR(100) NOT NULL,
    `estado_producto` ENUM('bueno', 'dañado') NOT NULL DEFAULT 'bueno',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_dev_det_devolucion` (`devolucion_id`),
    INDEX `idx_dev_det_producto` (`producto_id`),
    INDEX `idx_dev_det_lote` (`lote_id`),
    CONSTRAINT `fk_dev_det_devolucion` FOREIGN KEY (`devolucion_id`) REFERENCES `devoluciones`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_dev_det_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos`(`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_dev_det_lote` FOREIGN KEY (`lote_id`) REFERENCES `lotes`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Insertar permisos
INSERT IGNORE INTO `permisos` (`modulo`, `accion`, `descripcion`) VALUES
('devoluciones', 'ver', 'Ver historial de devoluciones'),
('devoluciones', 'crear', 'Registrar nuevas devoluciones'),
('devoluciones', 'aprobar', 'Aprobar o rechazar devoluciones');

-- 5. Asignar permisos al rol Administrador (ID 1) y Supervisor (ID 2)
INSERT IGNORE INTO `rol_permiso` (`rol_id`, `permiso_id`)
SELECT 1, id FROM `permisos` WHERE `modulo` = 'devoluciones';

INSERT IGNORE INTO `rol_permiso` (`rol_id`, `permiso_id`)
SELECT 2, id FROM `permisos` WHERE `modulo` = 'devoluciones';

-- 6. Asignar permiso básico de crear/ver al Operador (ID 3)
INSERT IGNORE INTO `rol_permiso` (`rol_id`, `permiso_id`)
SELECT 3, id FROM `permisos` WHERE `modulo` = 'devoluciones' AND `accion` IN ('ver', 'crear');

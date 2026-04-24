-- =====================================================
-- MIGRACIÓN: Módulo de Conteo Físico (Auditoría de Inventario)
-- Ejecutar sobre la base de datos invsys_db existente
-- =====================================================

-- Sesiones de conteo
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

-- Detalle: conteo por producto
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

-- =====================================================
-- InvSys - Migración 001
-- Código de Barras + Precio de Compra + Producto-Proveedor
-- =====================================================

USE `invsys_db`;

-- 1. Agregar campo código de barras a productos
ALTER TABLE `productos`
    ADD COLUMN `codigo_barras` VARCHAR(50) DEFAULT NULL COMMENT 'Código de barras EAN-13, UPC, etc.' AFTER `sku`,
    ADD COLUMN `precio_compra` DECIMAL(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Precio de compra/costo unitario' AFTER `precio`;

-- Índice único nullable para código de barras
ALTER TABLE `productos`
    ADD UNIQUE KEY `uk_productos_barcode` (`codigo_barras`);

-- Índice para búsqueda rápida
ALTER TABLE `productos`
    ADD INDEX `idx_productos_barcode` (`codigo_barras`);

-- 2. Crear tabla pivote producto_proveedor
CREATE TABLE IF NOT EXISTS `producto_proveedor` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `producto_id` INT UNSIGNED NOT NULL,
    `proveedor_id` INT UNSIGNED NOT NULL,
    `codigo_proveedor` VARCHAR(50) DEFAULT NULL COMMENT 'Código del producto en catálogo del proveedor',
    `precio_compra` DECIMAL(12,2) DEFAULT NULL COMMENT 'Precio unitario de compra a este proveedor',
    `tiempo_entrega_dias` INT UNSIGNED DEFAULT NULL COMMENT 'Días estimados de entrega',
    `es_preferido` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Proveedor preferido para este producto',
    `notas` TEXT DEFAULT NULL,
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_prod_prov` (`producto_id`, `proveedor_id`),
    INDEX `idx_pp_producto` (`producto_id`),
    INDEX `idx_pp_proveedor` (`proveedor_id`),
    INDEX `idx_pp_preferido` (`es_preferido`),
    CONSTRAINT `fk_pp_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_pp_proveedor` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Datos de ejemplo: vincular productos existentes con proveedores
INSERT INTO `producto_proveedor` (`producto_id`, `proveedor_id`, `codigo_proveedor`, `precio_compra`, `tiempo_entrega_dias`, `es_preferido`) VALUES
(1, 1, 'HP-PB450-G8', 16500.00, 5, 1),
(2, 1, 'DELL-P2422H', 4500.00, 5, 1),
(3, 2, 'RESMA-A4-75G', 95.00, 2, 1),
(4, 3, 'DET-IND-5L', 75.00, 3, 1),
(5, 3, 'GLAT-M-100', 120.00, 3, 1);

-- 4. Actualizar precio_compra en productos de ejemplo
UPDATE `productos` SET `precio_compra` = 16500.00 WHERE `id` = 1;
UPDATE `productos` SET `precio_compra` = 4500.00 WHERE `id` = 2;
UPDATE `productos` SET `precio_compra` = 95.00 WHERE `id` = 3;
UPDATE `productos` SET `precio_compra` = 75.00 WHERE `id` = 4;
UPDATE `productos` SET `precio_compra` = 120.00 WHERE `id` = 5;

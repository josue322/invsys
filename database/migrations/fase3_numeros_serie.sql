-- ============================================
-- InvSys — Fase 3: Números de Serie
-- Migración: Agregar soporte de trazabilidad unitaria
-- ============================================

-- 1. Columna en productos para habilitar el tracking de series
ALTER TABLE productos 
    ADD COLUMN requiere_serie TINYINT(1) NOT NULL DEFAULT 0 
    AFTER es_perecedero;

-- 2. Tabla de números de serie
CREATE TABLE IF NOT EXISTS numeros_serie (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    producto_id INT UNSIGNED NOT NULL,
    numero_serie VARCHAR(100) NOT NULL,
    estado ENUM('disponible','asignado','en_reparacion','dado_de_baja') NOT NULL DEFAULT 'disponible',
    movimiento_entrada_id INT UNSIGNED DEFAULT NULL,
    movimiento_salida_id INT UNSIGNED DEFAULT NULL,
    notas TEXT DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uk_producto_serie (producto_id, numero_serie),
    INDEX idx_serie_estado (estado),
    CONSTRAINT fk_serie_producto FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
    CONSTRAINT fk_serie_mov_entrada FOREIGN KEY (movimiento_entrada_id) REFERENCES movimientos(id) ON DELETE SET NULL,
    CONSTRAINT fk_serie_mov_salida FOREIGN KEY (movimiento_salida_id) REFERENCES movimientos(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

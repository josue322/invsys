-- =====================================================
-- Fase 8: Transferencias entre Ubicaciones
-- =====================================================

-- Actualizar la tabla movimientos para aceptar el tipo 'transferencia'
ALTER TABLE `movimientos` 
MODIFY COLUMN `tipo` ENUM('entrada','salida','ajuste','transferencia') NOT NULL;

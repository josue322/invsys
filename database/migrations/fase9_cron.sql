-- =====================================================
-- Fase 9: Actualización para automatización de correos (Cron)
-- =====================================================

-- Añadir columna a la tabla alertas para evitar envío duplicado de correos
ALTER TABLE `alertas` ADD COLUMN `notificado_correo` TINYINT(1) NOT NULL DEFAULT 0 AFTER `leida`;

-- Crear un índice para búsquedas rápidas en el cron job
ALTER TABLE `alertas` ADD INDEX `idx_alertas_notificado_correo` (`notificado_correo`);

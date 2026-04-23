-- =====================================================
-- InvSys - Migración: Permisos dedicados para
-- Proveedores y Ubicaciones
-- Ejecutar en la base de datos existente
-- =====================================================

USE `invsys_db`;

-- Insertar permisos de Proveedores (si no existen)
INSERT IGNORE INTO `permisos` (`modulo`, `accion`, `descripcion`) VALUES
('proveedores', 'ver', 'Ver proveedores'),
('proveedores', 'crear', 'Crear proveedores'),
('proveedores', 'editar', 'Editar proveedores'),
('proveedores', 'eliminar', 'Eliminar proveedores');

-- Insertar permisos de Ubicaciones (si no existen)
INSERT IGNORE INTO `permisos` (`modulo`, `accion`, `descripcion`) VALUES
('ubicaciones', 'ver', 'Ver ubicaciones'),
('ubicaciones', 'crear', 'Crear ubicaciones'),
('ubicaciones', 'editar', 'Editar ubicaciones'),
('ubicaciones', 'eliminar', 'Eliminar ubicaciones');

-- Asignar TODOS los permisos nuevos al Admin (rol_id = 1)
INSERT IGNORE INTO `rol_permiso` (`rol_id`, `permiso_id`)
SELECT 1, `id` FROM `permisos`
WHERE `modulo` IN ('proveedores', 'ubicaciones');

-- Asignar permisos nuevos al Supervisor (rol_id = 2)
INSERT IGNORE INTO `rol_permiso` (`rol_id`, `permiso_id`)
SELECT 2, `id` FROM `permisos`
WHERE `modulo` IN ('proveedores', 'ubicaciones');

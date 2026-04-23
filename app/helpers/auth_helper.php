<?php
/**
 * InvSys - Auth Helper
 * 
 * Funciones auxiliares para autenticación y autorización.
 * Disponibles globalmente en toda la aplicación.
 */

/**
 * Verificar si el usuario está autenticado.
 *
 * @return bool
 */
function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Obtener datos del usuario actual desde la sesión.
 *
 * @return array|null Datos del usuario o null si no está autenticado
 */
function currentUser(): ?array
{
    if (!isLoggedIn()) {
        return null;
    }

    return [
        'id'         => $_SESSION['user_id'] ?? 0,
        'nombre'     => $_SESSION['user_nombre'] ?? '',
        'email'      => $_SESSION['user_email'] ?? '',
        'rol_id'     => $_SESSION['user_rol_id'] ?? 0,
        'rol_nombre' => $_SESSION['user_rol_nombre'] ?? '',
    ];
}

/**
 * Obtener el ID del usuario actual.
 *
 * @return int
 */
function currentUserId(): int
{
    return (int) ($_SESSION['user_id'] ?? 0);
}

/**
 * Obtener el nombre del rol del usuario actual.
 *
 * @return string
 */
function currentUserRole(): string
{
    return $_SESSION['user_rol_nombre'] ?? '';
}

/**
 * Verificar si el usuario tiene un permiso específico.
 * El permiso se verifica en formato "modulo.accion" (ej: "productos.crear").
 *
 * @param string $permission Permiso en formato "modulo.accion"
 * @return bool
 */
function hasPermission(string $permission): bool
{
    if (!isLoggedIn()) {
        return false;
    }

    // Admin tiene todos los permisos
    if (currentUserRole() === 'Admin') {
        return true;
    }

    $permisos = $_SESSION['user_permisos'] ?? [];
    return in_array($permission, $permisos);
}

/**
 * Verificar si el usuario tiene un rol específico.
 *
 * @param string $role Nombre del rol (ej: "Admin", "Supervisor")
 * @return bool
 */
function hasRole(string $role): bool
{
    return currentUserRole() === $role;
}

/**
 * Verificar si el usuario tiene alguno de los roles dados.
 *
 * @param array $roles Lista de nombres de rol
 * @return bool
 */
function hasAnyRole(array $roles): bool
{
    return in_array(currentUserRole(), $roles);
}

/**
 * Obtener las iniciales del nombre del usuario para avatar.
 *
 * @param string|null $nombre Nombre completo
 * @return string Iniciales (máximo 2 caracteres)
 */
function userInitials(?string $nombre = null): string
{
    $nombre = $nombre ?? ($_SESSION['user_nombre'] ?? 'U');
    $parts = explode(' ', trim($nombre));
    
    if (count($parts) >= 2) {
        return mb_strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[1], 0, 1));
    }
    
    return mb_strtoupper(mb_substr($parts[0], 0, 2));
}

/**
 * Obtener el color del badge según el rol.
 *
 * @param string $rol Nombre del rol
 * @return string Clase CSS del badge
 */
function roleBadgeClass(string $rol): string
{
    return match ($rol) {
        'Admin'      => 'bg-danger',
        'Supervisor' => 'bg-warning text-dark',
        'Operador'   => 'bg-info',
        default      => 'bg-secondary',
    };
}

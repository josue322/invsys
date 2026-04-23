<?php
/**
 * InvSys - Middleware
 * 
 * Sistema de middleware para protección de rutas.
 * Verifica autenticación, roles y permisos antes de
 * permitir el acceso a un controlador.
 */

class Middleware
{
    /**
     * Ejecutar un middleware específico.
     *
     * @param string|array $middleware Nombre del middleware o [middleware, parámetro]
     * @return bool true si el middleware permite continuar
     */
    public static function handle(string|array $middleware): bool
    {
        // Si es un array, extraer middleware y parámetro
        if (is_array($middleware)) {
            $name = $middleware[0];
            $param = $middleware[1] ?? null;
        } else {
            // Parsear formato "permiso:modulo.accion"
            if (str_contains($middleware, ':')) {
                [$name, $param] = explode(':', $middleware, 2);
            } else {
                $name = $middleware;
                $param = null;
            }
        }

        return match ($name) {
            'auth'    => self::authMiddleware(),
            'guest'   => self::guestMiddleware(),
            'role'    => self::roleMiddleware($param),
            'permiso' => self::permisoMiddleware($param),
            default   => true,
        };
    }

    /**
     * Middleware de autenticación.
     * Redirige al login si el usuario no está autenticado.
     *
     * @return bool
     */
    private static function authMiddleware(): bool
    {
        if (!isLoggedIn()) {
            $_SESSION['flash'] = [
                'type'    => 'warning',
                'message' => 'Debe iniciar sesión para acceder a esta página.',
            ];
            header('Location: ' . url('login'));
            exit;
        }

        // Verificar si la sesión ha expirado
        if (isset($_SESSION['last_activity'])) {
            $maxLifetime = (int) sysConfig('session_lifetime', '3600');
            if ((time() - $_SESSION['last_activity']) > $maxLifetime) {
                // Sesión expirada
                session_unset();
                session_destroy();
                session_start();
                $_SESSION['flash'] = [
                    'type'    => 'info',
                    'message' => 'Su sesión ha expirado. Por favor inicie sesión nuevamente.',
                ];
                header('Location: ' . url('login'));
                exit;
            }
        }
        $_SESSION['last_activity'] = time();

        // Regenerar ID de sesión periódicamente (cada 30 minutos)
        if (!isset($_SESSION['last_regeneration'])) {
            $_SESSION['last_regeneration'] = time();
        } elseif (time() - $_SESSION['last_regeneration'] > 1800) {
            session_regenerate_id(true);
            $_SESSION['last_regeneration'] = time();
        }

        return true;
    }

    /**
     * Middleware de invitado.
     * Redirige al dashboard si el usuario YA está autenticado.
     *
     * @return bool
     */
    private static function guestMiddleware(): bool
    {
        if (isLoggedIn()) {
            header('Location: ' . url('dashboard'));
            exit;
        }
        return true;
    }

    /**
     * Middleware de rol.
     * Verifica que el usuario tenga un rol específico.
     *
     * @param string|null $roleName Nombre del rol requerido (ej: "Admin")
     * @return bool
     */
    private static function roleMiddleware(?string $roleName): bool
    {
        if (!$roleName) {
            return true;
        }

        $user = currentUser();
        if (!$user) {
            header('Location: ' . url('login'));
            exit;
        }

        // Soportar múltiples roles separados por |
        $allowedRoles = explode('|', $roleName);
        
        if (!in_array($user['rol_nombre'], $allowedRoles)) {
            http_response_code(403);
            $_SESSION['flash'] = [
                'type'    => 'error',
                'message' => 'No tiene permisos para acceder a esta sección.',
            ];
            header('Location: ' . url('dashboard'));
            exit;
        }

        return true;
    }

    /**
     * Middleware de permiso.
     * Verifica que el usuario tenga un permiso específico (módulo.acción).
     *
     * @param string|null $permission Permiso en formato "modulo.accion"
     * @return bool
     */
    private static function permisoMiddleware(?string $permission): bool
    {
        if (!$permission) {
            return true;
        }

        if (!hasPermission($permission)) {
            http_response_code(403);
            $_SESSION['flash'] = [
                'type'    => 'error',
                'message' => 'No tiene permisos para realizar esta acción.',
            ];
            header('Location: ' . url('dashboard'));
            exit;
        }

        return true;
    }
}

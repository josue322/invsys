<?php
/**
 * InvSys - AuthService
 * 
 * Servicio de autenticación.
 * Maneja login, logout, verificación de credenciales,
 * carga de permisos y gestión de sesión.
 */

class AuthService
{
    private Usuario $usuarioModel;
    private Sesion $sesionModel;
    private Log $logModel;
    private SecurityService $securityService;

    public function __construct()
    {
        $this->usuarioModel = new Usuario();
        $this->sesionModel = new Sesion();
        $this->logModel = new Log();
        $this->securityService = SecurityService::getInstance();
    }

    /**
     * Intentar login con email y password.
     *
     * @param string $email
     * @param string $password
     * @return array ['success' => bool, 'message' => string]
     */
    public function login(string $email, string $password): array
    {
        // 0. Rate limiting por IP (previene fuerza bruta contra múltiples cuentas)
        $rateLimiter = RateLimiter::getInstance();
        $clientIP = $this->securityService->getClientIP();
        $rateCheck = $rateLimiter->check($clientIP, 'login', 20, 900);

        if ($rateCheck['limited']) {
            $retryMin = (int) ceil($rateCheck['retry_after'] / 60);
            $this->securityService->logAction(null, 'login_rate_limited', 'auth',
                "Rate limit alcanzado para IP: {$clientIP}. Reintentar en {$retryMin} minutos."
            );
            return [
                'success' => false,
                'message' => "Demasiados intentos de login. Intente en {$retryMin} minutos.",
            ];
        }

        // Registrar intento de login por IP
        $rateLimiter->hit($clientIP, 'login');

        // 1. Verificar si la cuenta está bloqueada
        $bloqueo = $this->securityService->isBlocked($email);
        if ($bloqueo['blocked']) {
            $this->securityService->logAction(null, 'login_blocked', 'auth',
                "Intento de login con cuenta bloqueada: {$email}. Quedan {$bloqueo['remaining_minutes']} minutos."
            );
            return [
                'success' => false,
                'message' => "Cuenta bloqueada temporalmente. Intente en {$bloqueo['remaining_minutes']} minutos.",
            ];
        }

        // 2. Buscar usuario por email
        $usuario = $this->usuarioModel->findByEmail($email);

        if (!$usuario) {
            $this->securityService->registerFailedAttempt($email);
            $this->securityService->logAction(null, 'login_fallido', 'auth',
                "Intento de login fallido con email no registrado: {$email}"
            );
            return [
                'success' => false,
                'message' => 'Credenciales incorrectas.',
            ];
        }

        // 3. Verificar si el usuario está activo
        if (!$usuario->activo) {
            $this->securityService->logAction($usuario->id, 'login_inactivo', 'auth',
                "Intento de login con cuenta inactiva: {$email}"
            );
            return [
                'success' => false,
                'message' => 'Su cuenta está desactivada. Contacte al administrador.',
            ];
        }

        // 4. Verificar password
        if (!password_verify($password, $usuario->password)) {
            $this->securityService->registerFailedAttempt($email);
            $remaining = $this->securityService->getRemainingAttempts($email);
            $this->securityService->logAction($usuario->id, 'login_fallido', 'auth',
                "Contraseña incorrecta para: {$email}. Intentos restantes: {$remaining}"
            );
            return [
                'success' => false,
                'message' => "Credenciales incorrectas. Intentos restantes: {$remaining}.",
            ];
        }

        // 5. Login exitoso - Iniciar sesión
        $this->startSession($usuario);

        // 6. Resetear intentos fallidos y rate limit por IP
        $this->securityService->resetAttempts($email);
        $rateLimiter->clear($clientIP, 'login');

        // 7. Actualizar último login
        $this->usuarioModel->updateLastLogin($usuario->id);

        // 8. Registrar sesión en BD
        $token = bin2hex(random_bytes(32));
        $this->sesionModel->create([
            'usuario_id'   => $usuario->id,
            'token'        => $token,
            'ip'           => $this->securityService->getClientIP(),
            'user_agent'   => $this->securityService->getUserAgent(),
        ]);

        // 9. Log de auditoría
        $this->securityService->logAction($usuario->id, 'login_exitoso', 'auth',
            "Login exitoso desde IP: " . $this->securityService->getClientIP()
        );

        return [
            'success' => true,
            'message' => 'Login exitoso.',
        ];
    }

    /**
     * Iniciar sesión PHP con datos del usuario.
     *
     * @param object $usuario Datos del usuario de la BD
     */
    private function startSession(object $usuario): void
    {
        // Regenerar ID de sesión para prevenir session fixation
        session_regenerate_id(true);

        // Almacenar datos en sesión
        $_SESSION['user_id'] = $usuario->id;
        $_SESSION['user_nombre'] = $usuario->nombre;
        $_SESSION['user_email'] = $usuario->email;
        $_SESSION['user_rol_id'] = $usuario->rol_id;
        $_SESSION['user_rol_nombre'] = $this->getUserRoleName($usuario->rol_id);
        $_SESSION['user_permisos'] = $this->loadUserPermissions($usuario->rol_id);
        $_SESSION['last_activity'] = time();
        $_SESSION['last_regeneration'] = time();

        // Cargar preferencia de tema
        $themeService = new ThemeService();
        $_SESSION['user_theme'] = $themeService->getUserTheme($usuario->id);
    }

    /**
     * Obtener el nombre del rol por ID.
     *
     * @param int $rolId
     * @return string
     */
    private function getUserRoleName(int $rolId): string
    {
        $rol = new Rol();
        $result = $rol->findById($rolId);
        return $result ? $result->nombre : 'Sin rol';
    }

    /**
     * Cargar permisos del usuario según su rol.
     * Retorna un array de strings "modulo.accion".
     *
     * @param int $rolId
     * @return array
     */
    private function loadUserPermissions(int $rolId): array
    {
        $permiso = new Permiso();
        $permisos = $permiso->getByRolId($rolId);
        
        $result = [];
        foreach ($permisos as $p) {
            $result[] = "{$p->modulo}.{$p->accion}";
        }
        return $result;
    }

    /**
     * Cerrar sesión.
     */
    public function logout(): void
    {
        $userId = $_SESSION['user_id'] ?? null;

        if ($userId) {
            // Desactivar sesión en BD
            $this->sesionModel->deactivateByUserId($userId);

            // Log de auditoría
            $this->securityService->logAction($userId, 'logout', 'auth', 'Cierre de sesión');
        }

        // Destruir sesión PHP
        session_unset();
        session_destroy();
    }
}

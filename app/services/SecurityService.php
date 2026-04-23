<?php
/**
 * InvSys - SecurityService
 * 
 * Servicio de seguridad.
 * Gestiona control de intentos fallidos de login,
 * registro de IP/user_agent, y logs de auditoría.
 */

class SecurityService
{
    private Usuario $usuarioModel;
    private Log $logModel;

    /** @var int Máximo de intentos fallidos antes del bloqueo */
    private int $maxAttempts = 5;

    /** @var int Minutos de bloqueo */
    private int $lockoutMinutes = 15;

    /** @var self|null Instancia singleton para evitar múltiples instanciaciones por request */
    private static ?self $instance = null;

    /**
     * Obtener instancia singleton del servicio.
     * Evita crear múltiples objetos Usuario, Log y queries de config por request.
     *
     * @return self
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $this->usuarioModel = new Usuario();
        $this->logModel = new Log();

        // Intentar cargar configuración de BD
        $this->loadConfig();
    }

    /**
     * Cargar configuración desde la BD.
     */
    private function loadConfig(): void
    {
        try {
            $config = new Config();
            
            $maxAttempts = $config->getValue('intentos_login_max');
            if ($maxAttempts !== null) {
                $this->maxAttempts = (int) $maxAttempts;
            }

            $lockoutMinutes = $config->getValue('tiempo_bloqueo_minutos');
            if ($lockoutMinutes !== null) {
                $this->lockoutMinutes = (int) $lockoutMinutes;
            }
        } catch (\Exception $e) {
            // Si falla, usar valores por defecto
        }
    }

    /**
     * Verificar si un email/cuenta está bloqueada.
     *
     * @param string $email
     * @return array ['blocked' => bool, 'remaining_minutes' => int]
     */
    public function isBlocked(string $email): array
    {
        $usuario = $this->usuarioModel->findByEmail($email);
        
        if (!$usuario) {
            return ['blocked' => false, 'remaining_minutes' => 0];
        }

        if ($usuario->bloqueado_hasta && strtotime($usuario->bloqueado_hasta) > time()) {
            $remaining = (int) ceil((strtotime($usuario->bloqueado_hasta) - time()) / 60);
            return ['blocked' => true, 'remaining_minutes' => $remaining];
        }

        return ['blocked' => false, 'remaining_minutes' => 0];
    }

    /**
     * Registrar un intento fallido de login.
     *
     * @param string $email
     */
    public function registerFailedAttempt(string $email): void
    {
        $usuario = $this->usuarioModel->findByEmail($email);
        
        if (!$usuario) {
            return;
        }

        $intentos = $usuario->intentos_fallidos + 1;
        $data = ['intentos_fallidos' => $intentos];

        // Si alcanzó el máximo, bloquear la cuenta
        if ($intentos >= $this->maxAttempts) {
            $bloqueoHasta = date('Y-m-d H:i:s', time() + ($this->lockoutMinutes * 60));
            $data['bloqueado_hasta'] = $bloqueoHasta;
            $data['intentos_fallidos'] = 0; // Reset para después del bloqueo

            $this->logAction($usuario->id, 'cuenta_bloqueada', 'seguridad',
                "Cuenta bloqueada hasta {$bloqueoHasta} por {$this->maxAttempts} intentos fallidos"
            );
        }

        $this->usuarioModel->update($usuario->id, $data);
    }

    /**
     * Obtener intentos restantes antes del bloqueo.
     *
     * @param string $email
     * @return int
     */
    public function getRemainingAttempts(string $email): int
    {
        $usuario = $this->usuarioModel->findByEmail($email);
        if (!$usuario) {
            return 0;
        }
        return max(0, $this->maxAttempts - $usuario->intentos_fallidos);
    }

    /**
     * Resetear intentos fallidos.
     *
     * @param string $email
     */
    public function resetAttempts(string $email): void
    {
        $usuario = $this->usuarioModel->findByEmail($email);
        if ($usuario) {
            $this->usuarioModel->update($usuario->id, [
                'intentos_fallidos' => 0,
                'bloqueado_hasta'   => null,
            ]);
        }
    }

    /**
     * Registrar una acción en el log de auditoría.
     *
     * @param int|null $userId ID del usuario (null si no está autenticado)
     * @param string $action Tipo de acción
     * @param string $module Módulo del sistema
     * @param string $details Detalles de la acción
     */
    public function logAction(?int $userId, string $action, string $module, string $details = ''): void
    {
        try {
            $this->logModel->create([
                'usuario_id' => $userId,
                'accion'     => $action,
                'modulo'     => $module,
                'detalles'   => $details,
                'ip'         => $this->getClientIP(),
                'user_agent' => $this->getUserAgent(),
            ]);
        } catch (\Exception $e) {
            // Si falla el log, no interrumpir la operación
            error_log("Error al registrar log: " . $e->getMessage());
        }
    }

    /**
     * Obtener la IP real del cliente.
     *
     * @return string
     */
    public function getClientIP(): string
    {
        $headers = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR',
        ];

        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ips = explode(',', $_SERVER[$header]);
                $ip = trim($ips[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return '0.0.0.0';
    }

    /**
     * Obtener el User-Agent del navegador.
     *
     * @return string
     */
    public function getUserAgent(): string
    {
        return substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 500);
    }
}

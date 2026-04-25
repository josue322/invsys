<?php
/**
 * InvSys - MailService
 * 
 * Servicio de envío de correos electrónicos.
 * Soporta dos modos:
 *   1. mail() nativo de PHP (por defecto)
 *   2. SMTP directo vía socket (cuando smtp_activo = 1 en configuración)
 * 
 * La configuración SMTP se lee de la tabla `configuraciones` (panel visual),
 * con fallback a las variables de .env para retrocompatibilidad.
 */

class MailService
{
    /** @var MailService|null Instancia singleton */
    private static ?MailService $instance = null;

    /** @var string Nombre del sistema para el remitente */
    private string $systemName;

    /** @var string Email del remitente (noreply) */
    private string $fromEmail;

    /** @var string Nombre del remitente */
    private string $fromName;

    /** @var bool Si SMTP está activo */
    private bool $smtpActive;

    /** @var array Configuración SMTP */
    private array $smtpConfig;

    private function __construct()
    {
        $this->systemName = Config::get('nombre_sistema', 'InvSys');
        
        // Leer de BD primero, fallback a .env
        $this->fromEmail = Config::get('mail_from_address') 
            ?: EnvLoader::get('MAIL_FROM_ADDRESS', 'noreply@invsys.com');
        $this->fromName = Config::get('mail_from_name') 
            ?: EnvLoader::get('MAIL_FROM_NAME', $this->systemName);

        // SMTP config from DB
        $this->smtpActive = Config::get('smtp_activo', '0') === '1';
        $this->smtpConfig = [
            'host'       => Config::get('smtp_host', ''),
            'port'       => (int) Config::get('smtp_port', '587'),
            'encryption' => Config::get('smtp_encryption', 'tls'),
            'auth'       => Config::get('smtp_auth', '1') === '1',
            'username'   => Config::get('smtp_username', ''),
            'password'   => Config::get('smtp_password', ''),
        ];
    }

    /**
     * Obtener la instancia singleton.
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

    /**
     * Reiniciar singleton (útil después de cambiar configuración).
     */
    public static function reset(): void
    {
        self::$instance = null;
    }

    /**
     * Enviar email de bienvenida cuando se crea una cuenta.
     *
     * @param string $toEmail Email del destinatario
     * @param string $toName  Nombre del destinatario
     * @param string $method  Método de creación ('registro' o 'admin')
     * @return bool True si se envió correctamente
     */
    public function sendWelcomeEmail(string $toEmail, string $toName, string $method = 'registro'): bool
    {
        $subject = "Bienvenido a {$this->systemName} — Cuenta creada exitosamente";

        $loginUrl = $this->getLoginUrl();
        $year     = date('Y');
        $color    = Config::get('color_principal', '#6366f1');

        // Mensaje según el método de creación
        $introMessage = match ($method) {
            'admin' => "Un administrador ha creado una cuenta para ti en <strong>{$this->systemName}</strong>.",
            default => "Tu cuenta en <strong>{$this->systemName}</strong> ha sido creada exitosamente.",
        };

        $html = $this->buildWelcomeHtml(
            $toName,
            $introMessage,
            $loginUrl,
            $color,
            $year
        );

        return $this->send($toEmail, $subject, $html);
    }

    /**
     * Enviar un correo de prueba al admin actual.
     *
     * @param string $toEmail Email del admin
     * @return array ['success' => bool, 'message' => string]
     */
    public function sendTestEmail(string $toEmail): array
    {
        $subject = "[{$this->systemName}] Correo de prueba SMTP";
        $date = formatDate(date('Y-m-d H:i:s'));
        $mode = $this->smtpActive ? 'SMTP (' . $this->smtpConfig['host'] . ':' . $this->smtpConfig['port'] . ')' : 'mail() nativo';
        
        $html = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"></head>
<body style="margin:0; padding:0; background:#f4f4f8; font-family:'Segoe UI',Tahoma,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f8; padding:40px 0;">
        <tr><td align="center">
            <table width="520" cellpadding="0" cellspacing="0" style="background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 2px 12px rgba(0,0,0,0.06);">
                <tr><td style="background:linear-gradient(135deg,#6366f1,#8b5cf6); padding:28px; text-align:center;">
                    <h2 style="color:#fff; margin:0; font-size:20px;">✅ Correo de prueba exitoso</h2>
                </td></tr>
                <tr><td style="padding:28px;">
                    <p style="color:#4a4a68; font-size:14px; line-height:1.7;">
                        Este correo confirma que la configuración de correo de <strong>{$this->systemName}</strong> funciona correctamente.
                    </p>
                    <div style="background:#f8f8fc; border-radius:8px; padding:16px; margin:16px 0; border-left:3px solid #6366f1;">
                        <p style="color:#6b6b8d; font-size:13px; margin:0 0 6px;">
                            <strong>Modo:</strong> {$mode}<br>
                            <strong>Fecha:</strong> {$date}<br>
                            <strong>Remitente:</strong> {$this->fromName} &lt;{$this->fromEmail}&gt;
                        </p>
                    </div>
                    <p style="color:#9999b0; font-size:12px; margin:16px 0 0;">
                        Este es un correo automático de prueba. No es necesario responder.
                    </p>
                </td></tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
HTML;

        try {
            $result = $this->send($toEmail, $subject, $html);
            return [
                'success' => $result,
                'message' => $result 
                    ? "Correo de prueba enviado a {$toEmail}" 
                    : 'No se pudo enviar el correo. Verifica la configuración SMTP.'
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Enviar un correo electrónico con contenido HTML.
     * Usa SMTP si está configurado, sino mail() nativo.
     *
     * @param string $to      Dirección del destinatario
     * @param string $subject Asunto del correo
     * @param string $body    Cuerpo HTML del correo
     * @return bool
     */
    public function send(string $to, string $subject, string $body): bool
    {
        if ($this->smtpActive && !empty($this->smtpConfig['host'])) {
            return $this->sendViaSmtp($to, $subject, $body);
        }

        return $this->sendViaMail($to, $subject, $body);
    }

    /**
     * Enviar usando mail() nativo de PHP.
     */
    private function sendViaMail(string $to, string $subject, string $body): bool
    {
        $headers  = "From: {$this->fromName} <{$this->fromEmail}>\r\n";
        $headers .= "Reply-To: {$this->fromEmail}\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "X-Mailer: {$this->systemName}\r\n";

        try {
            $result = @mail($to, $subject, $body, $headers);
            $this->logMailAttempt($to, $subject, $result, '', 'mail()');
            return $result;
        } catch (\Throwable $e) {
            $this->logMailAttempt($to, $subject, false, $e->getMessage(), 'mail()');
            return false;
        }
    }

    /**
     * Enviar usando conexión SMTP directa vía socket.
     * Soporta TLS/SSL con autenticación.
     */
    private function sendViaSmtp(string $to, string $subject, string $body): bool
    {
        $host = $this->smtpConfig['host'];
        $port = $this->smtpConfig['port'];
        $encryption = $this->smtpConfig['encryption'];
        $auth = $this->smtpConfig['auth'];
        $username = $this->smtpConfig['username'];
        $password = $this->smtpConfig['password'];

        $errorMsg = '';

        try {
            // Construir dirección de conexión
            $address = $host;
            if ($encryption === 'ssl') {
                $address = 'ssl://' . $host;
            }

            $context = stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ]
            ]);

            $socket = @stream_socket_client(
                "{$address}:{$port}",
                $errno,
                $errstr,
                15, // timeout
                STREAM_CLIENT_CONNECT,
                $context
            );

            if (!$socket) {
                $errorMsg = "No se pudo conectar a {$host}:{$port} — {$errstr}";
                $this->logMailAttempt($to, $subject, false, $errorMsg, 'SMTP');
                return false;
            }

            // Leer saludo del servidor
            $this->smtpRead($socket);

            // EHLO
            $this->smtpCommand($socket, "EHLO " . gethostname());

            // STARTTLS si es TLS
            if ($encryption === 'tls') {
                $this->smtpCommand($socket, "STARTTLS");
                if (!@stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT)) {
                    // Fallback a TLS 1.0/1.1
                    @stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                }
                $this->smtpCommand($socket, "EHLO " . gethostname());
            }

            // Autenticación
            if ($auth && !empty($username)) {
                $this->smtpCommand($socket, "AUTH LOGIN");
                $this->smtpCommand($socket, base64_encode($username));
                $this->smtpCommand($socket, base64_encode($password));
            }

            // Remitente y destinatario
            $this->smtpCommand($socket, "MAIL FROM:<{$this->fromEmail}>");
            $this->smtpCommand($socket, "RCPT TO:<{$to}>");

            // Datos
            $this->smtpCommand($socket, "DATA");

            // Construir headers del mensaje
            $message  = "From: {$this->fromName} <{$this->fromEmail}>\r\n";
            $message .= "To: {$to}\r\n";
            $message .= "Subject: {$subject}\r\n";
            $message .= "MIME-Version: 1.0\r\n";
            $message .= "Content-Type: text/html; charset=UTF-8\r\n";
            $message .= "X-Mailer: {$this->systemName}\r\n";
            $message .= "\r\n";
            $message .= $body;
            $message .= "\r\n.\r\n";

            fwrite($socket, $message);
            $this->smtpRead($socket);

            // QUIT
            $this->smtpCommand($socket, "QUIT");
            fclose($socket);

            $this->logMailAttempt($to, $subject, true, '', 'SMTP');
            return true;

        } catch (\Throwable $e) {
            $errorMsg = $e->getMessage();
            $this->logMailAttempt($to, $subject, false, $errorMsg, 'SMTP');
            if (isset($socket) && is_resource($socket)) {
                @fclose($socket);
            }
            return false;
        }
    }

    /**
     * Enviar un comando SMTP y leer la respuesta.
     */
    private function smtpCommand($socket, string $command): string
    {
        fwrite($socket, $command . "\r\n");
        return $this->smtpRead($socket);
    }

    /**
     * Leer respuesta del servidor SMTP.
     */
    private function smtpRead($socket): string
    {
        $response = '';
        stream_set_timeout($socket, 10);
        while ($line = fgets($socket, 515)) {
            $response .= $line;
            // Si el 4to carácter es un espacio, es la última línea
            if (isset($line[3]) && $line[3] === ' ') break;
        }
        return $response;
    }

    /**
     * Registrar intento de envío de correo en el log del sistema.
     */
    private function logMailAttempt(string $to, string $subject, bool $success, string $error = '', string $method = 'mail()'): void
    {
        $logDir  = STORAGE_PATH . '/logs';
        $logFile = $logDir . '/mail.log';

        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }

        $status = $success ? 'OK' : 'FAIL';
        $entry  = sprintf(
            "[%s] [%s] [%s] To: %s | Subject: %s%s\n",
            date('Y-m-d H:i:s'),
            $status,
            $method,
            $to,
            $subject,
            $error ? " | Error: {$error}" : ''
        );

        @file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);
    }

    /**
     * Construir la URL de login.
     */
    private function getLoginUrl(): string
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $base     = rtrim(BASE_URL, '/');

        return "{$protocol}://{$host}{$base}/login";
    }

    /**
     * Construir el HTML del email de bienvenida.
     */
    private function buildWelcomeHtml(
        string $name,
        string $introMessage,
        string $loginUrl,
        string $color,
        string $year
    ): string {
        $escapedName  = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        $escapedUrl   = htmlspecialchars($loginUrl, ENT_QUOTES, 'UTF-8');
        $systemName   = htmlspecialchars($this->systemName, ENT_QUOTES, 'UTF-8');

        return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a {$systemName}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f8; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f8; padding: 40px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, {$color}, #8b5cf6); padding: 40px 40px 30px; text-align: center;">
                            <div style="width: 64px; height: 64px; background: rgba(255,255,255,0.2); border-radius: 16px; margin: 0 auto 16px; line-height: 64px; font-size: 28px;">
                                📦
                            </div>
                            <h1 style="color: #ffffff; margin: 0; font-size: 26px; font-weight: 700; letter-spacing: -0.5px;">
                                {$systemName}
                            </h1>
                            <p style="color: rgba(255,255,255,0.85); margin: 8px 0 0; font-size: 14px;">
                                Sistema de Gestión de Inventario
                            </p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 40px;">
                            <h2 style="color: #1a1a2e; margin: 0 0 16px; font-size: 22px; font-weight: 600;">
                                ¡Hola, {$escapedName}! 👋
                            </h2>
                            <p style="color: #4a4a68; font-size: 15px; line-height: 1.7; margin: 0 0 24px;">
                                {$introMessage}
                            </p>
                            <p style="color: #4a4a68; font-size: 15px; line-height: 1.7; margin: 0 0 32px;">
                                Ya puedes acceder al sistema para gestionar inventarios, registrar movimientos, 
                                generar reportes y mucho más.
                            </p>

                            <!-- CTA Button -->
                            <div style="text-align: center; margin: 0 0 32px;">
                                <a href="{$escapedUrl}" 
                                   style="display: inline-block; background: linear-gradient(135deg, {$color}, #8b5cf6); color: #ffffff; text-decoration: none; padding: 14px 40px; border-radius: 10px; font-size: 15px; font-weight: 600; letter-spacing: 0.3px; box-shadow: 0 4px 16px rgba(99,102,241,0.35);">
                                    Iniciar Sesión →
                                </a>
                            </div>

                            <!-- Info Box -->
                            <div style="background: #f8f8fc; border-radius: 12px; padding: 20px 24px; border-left: 4px solid {$color};">
                                <p style="color: #6b6b8d; font-size: 13px; line-height: 1.6; margin: 0;">
                                    <strong style="color: #4a4a68;">💡 Consejo:</strong> Si tienes algún problema para acceder, 
                                    contacta al administrador del sistema para verificar el estado de tu cuenta.
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background: #f8f8fc; padding: 24px 40px; text-align: center; border-top: 1px solid #ebebf0;">
                            <p style="color: #9999b0; font-size: 12px; margin: 0 0 4px;">
                                Este es un correo automático de {$systemName}. No es necesario responder.
                            </p>
                            <p style="color: #b0b0c5; font-size: 11px; margin: 0;">
                                © {$year} {$systemName} — Todos los derechos reservados.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    }
}

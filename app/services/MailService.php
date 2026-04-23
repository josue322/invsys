<?php
/**
 * InvSys - MailService
 * 
 * Servicio de envío de correos electrónicos.
 * Utiliza la función mail() nativa de PHP.
 * Para producción, asegurar que el servidor tenga
 * configurado un MTA (sendmail, postfix) o SMTP relay.
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

    private function __construct()
    {
        $this->systemName = Config::get('nombre_sistema', 'InvSys');
        $this->fromEmail  = EnvLoader::get('MAIL_FROM_ADDRESS', 'noreply@invsys.com');
        $this->fromName   = EnvLoader::get('MAIL_FROM_NAME', $this->systemName);
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
     * Enviar un correo electrónico con contenido HTML.
     *
     * @param string $to      Dirección del destinatario
     * @param string $subject Asunto del correo
     * @param string $body    Cuerpo HTML del correo
     * @return bool
     */
    public function send(string $to, string $subject, string $body): bool
    {
        $headers  = "From: {$this->fromName} <{$this->fromEmail}>\r\n";
        $headers .= "Reply-To: {$this->fromEmail}\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "X-Mailer: {$this->systemName}\r\n";

        try {
            $result = @mail($to, $subject, $body, $headers);

            // Log del intento de envío
            $this->logMailAttempt($to, $subject, $result);

            return $result;
        } catch (\Throwable $e) {
            $this->logMailAttempt($to, $subject, false, $e->getMessage());
            return false;
        }
    }

    /**
     * Registrar intento de envío de correo en el log del sistema.
     *
     * @param string $to      Destinatario
     * @param string $subject Asunto
     * @param bool   $success Si se envió correctamente
     * @param string $error   Mensaje de error (si aplica)
     */
    private function logMailAttempt(string $to, string $subject, bool $success, string $error = ''): void
    {
        $logDir  = STORAGE_PATH . '/logs';
        $logFile = $logDir . '/mail.log';

        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }

        $status = $success ? 'OK' : 'FAIL';
        $entry  = sprintf(
            "[%s] [%s] To: %s | Subject: %s%s\n",
            date('Y-m-d H:i:s'),
            $status,
            $to,
            $subject,
            $error ? " | Error: {$error}" : ''
        );

        @file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);
    }

    /**
     * Construir la URL de login.
     *
     * @return string
     */
    private function getLoginUrl(): string
    {
        // Intentar construir una URL absoluta
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $base     = rtrim(BASE_URL, '/');

        return "{$protocol}://{$host}{$base}/login";
    }

    /**
     * Construir el HTML del email de bienvenida.
     *
     * @param string $name         Nombre del usuario
     * @param string $introMessage Mensaje introductorio
     * @param string $loginUrl     URL de login
     * @param string $color        Color principal del sistema
     * @param string $year         Año actual
     * @return string HTML completo del email
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

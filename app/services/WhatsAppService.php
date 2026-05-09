<?php
/**
 * InvSys - WhatsAppService
 * 
 * Servicio para enviar notificaciones mediante CallMeBot (WhatsApp API gratuita).
 * 
 * Configuración previa requerida (una sola vez):
 * 1. Agregar el número de CallMeBot a tus contactos de WhatsApp.
 * 2. Enviarle el mensaje: "I allow callmebot to send me messages"
 * 3. El bot te responderá con tu APIKEY personal.
 * 4. Guardar el APIKEY en Configuración > WhatsApp del sistema.
 * 
 * @see https://www.callmebot.com/blog/free-api-whatsapp-messages/
 */

class WhatsAppService
{
    private Config $configModel;
    private bool $enabled;
    private string $phone;
    private string $apiKey;

    public function __construct()
    {
        $this->configModel = new Config();
        
        $this->enabled = (bool) $this->configModel->getValue('whatsapp_enabled');
        $this->phone   = $this->configModel->getValue('whatsapp_phone') ?? '';
        $this->apiKey  = $this->configModel->getValue('whatsapp_apikey') ?? '';
    }

    /**
     * Enviar mensaje simple al administrador.
     *
     * @param string $message Texto del mensaje (soporta emojis y saltos de línea)
     * @return bool True si la API respondió correctamente
     */
    public function sendMessage(string $message): bool
    {
        if (!$this->enabled || empty($this->phone) || empty($this->apiKey)) {
            return false;
        }

        $params = [
            'phone'  => $this->phone,
            'text'   => $message,
            'apikey' => $this->apiKey,
        ];

        $url = 'https://api.callmebot.com/whatsapp.php?' . http_build_query($params);

        return $this->sendRequest($url);
    }

    /**
     * Enviar el resumen de alertas diarias por WhatsApp al administrador.
     *
     * @param array $alertasPendientes Array de objetos alerta
     * @return bool
     */
    public function sendDailyAlertDigest(array $alertasPendientes): bool
    {
        if (empty($alertasPendientes)) {
            return true;
        }

        $fecha = date('d/m/Y H:i');
        $total = count($alertasPendientes);

        $msg = "📦 *InvSys - Reporte de Alertas*\n";
        $msg .= "📅 {$fecha}\n";
        $msg .= "⚠️ Tienes *{$total}* alertas nuevas:\n\n";

        $count = 0;
        foreach ($alertasPendientes as $alerta) {
            if ($count >= 10) {
                $msg .= "...\n_y " . ($total - 10) . " más._\n";
                break;
            }

            $icon = match ($alerta->tipo) {
                'stock_minimo' => '📉',
                'stock_agotado' => '❌',
                default => '🔔'
            };

            $msg .= "{$icon} " . strip_tags($alerta->mensaje) . "\n\n";
            $count++;
        }

        $msg .= "Para ver los detalles completos, inicia sesión en el sistema.\n";

        return $this->sendMessage($msg);
    }

    /**
     * Realizar la petición HTTP GET a CallMeBot.
     * CallMeBot usa GET (no POST como UltraMsg).
     *
     * @param string $url URL completa con parámetros
     * @return bool
     */
    private function sendRequest(string $url): bool
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error || $httpCode !== 200) {
            error_log("WhatsAppService (CallMeBot) Error ($httpCode): $error. Response: $response");
            return false;
        }

        return true;
    }
}

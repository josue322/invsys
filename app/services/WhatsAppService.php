<?php
/**
 * InvSys - WhatsAppService
 * 
 * Servicio para enviar notificaciones mediante UltraMsg (WhatsApp API).
 */

class WhatsAppService
{
    private Config $configModel;
    private bool $enabled;
    private string $apiUrl;
    private string $instanceId;
    private string $token;

    public function __construct()
    {
        $this->configModel = new Config();
        
        $this->enabled = (bool) $this->configModel->getValue('whatsapp_enabled');
        $this->apiUrl = rtrim($this->configModel->getValue('whatsapp_api_url') ?? 'https://api.ultramsg.com/', '/');
        $this->instanceId = $this->configModel->getValue('whatsapp_instance_id') ?? '';
        $this->token = $this->configModel->getValue('whatsapp_token') ?? '';
    }

    /**
     * Enviar mensaje simple.
     */
    public function sendMessage(string $phone, string $message): bool
    {
        if (!$this->enabled || empty($this->instanceId) || empty($this->token) || empty($phone)) {
            return false;
        }

        // Limpiar teléfono (solo números y +)
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        if (empty($phone)) {
            return false;
        }

        $url = "{$this->apiUrl}/{$this->instanceId}/messages/chat";
        
        $data = [
            'token' => $this->token,
            'to' => $phone,
            'body' => $message
        ];

        return $this->sendRequest($url, $data);
    }

    /**
     * Enviar el resumen de alertas diarias por WhatsApp.
     */
    public function sendDailyAlertDigest(string $phone, array $alertasPendientes): bool
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

        $msg .= "Para ver los detalles completos, por favor inicia sesión en el sistema.\n";

        return $this->sendMessage($phone, $msg);
    }

    /**
     * Realizar la petición HTTP POST.
     */
    private function sendRequest(string $url, array $data): bool
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Para evitar problemas locales con certificados
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error || $httpCode !== 200) {
            error_log("WhatsAppService Error ($httpCode): $error. Response: $response");
            return false;
        }

        return true;
    }
}

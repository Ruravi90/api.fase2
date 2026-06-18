<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenWAService
{
    protected $baseUrl;
    protected $apiKey;
    protected $sessionId;
    protected $isEnabled;

    public function __construct()
    {
        $this->baseUrl = env('OPENWA_BASE_URL', 'http://localhost:2785/api');
        $this->apiKey = env('OPENWA_API_KEY');
        // Usaremos 'fase2-bot' como nombre de sesión por defecto (basado en tu captura)
        $this->sessionId = 'fase2-bot'; 
        // Bandera para activar/desactivar envíos
        $this->isEnabled = env('OPENWA_ENABLED', true);
    }

    private function client()
    {
        return Http::withHeaders([
            'X-API-Key' => $this->apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->baseUrl($this->baseUrl);
    }

    /**
     * Enviar un mensaje de texto
     */
    public function sendMessage($phone, $text, $sessionId = null)
    {
        if (!$this->isEnabled) {
            Log::info("OpenWA está desactivado en .env. Mensaje omitido para {$phone}");
            return ['status' => 'disabled', 'message' => 'WhatsApp envíos desactivados'];
        }

        $session = $sessionId ?? $this->sessionId;
        
        // Formatear el teléfono al formato que requiere WhatsApp Web JS (ej. 521XXXXXXXXXX@c.us)
        if (!str_contains($phone, '@c.us')) {
            $phone = "{$phone}@c.us";
        }

        $response = $this->client()->post("/sessions/{$session}/messages/send-text", [
            'chatId' => $phone,
            'text' => $text
        ]);

        if ($response->failed()) {
            Log::error('Error enviando mensaje OpenWA: ' . $response->body());
        }

        return $response->json();
    }
}

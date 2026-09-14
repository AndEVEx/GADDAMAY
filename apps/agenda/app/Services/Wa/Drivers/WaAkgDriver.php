<?php

namespace App\Services\Wa\Drivers;

use App\Services\Wa\Contracts\WaGatewayDriverInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WaAkgDriver implements WaGatewayDriverInterface
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $sessionId;

    public function __construct(array $config = [])
    {
        $this->baseUrl = rtrim($config['base_url'] ?? 'http://localhost:3001', '/');
        $this->apiKey = $config['api_key'] ?? '';
        $this->sessionId = $config['session_id'] ?? 'default';
    }

    public function sendMessage(string $targetPhone, string $message): array
    {
        $normalizedPhone = $this->normalizePhone($targetPhone);

        try {
            $client = Http::timeout(10);

            $headers = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ];

            if (!empty($this->apiKey)) {
                $headers['x-api-key'] = $this->apiKey;
                $headers['Authorization'] = 'Bearer ' . $this->apiKey;
            }

            if (!empty($this->sessionId)) {
                $headers['x-session-id'] = $this->sessionId;
            }

            $payload = [
                'phone' => $normalizedPhone,
                'message' => $message,
                'sessionId' => $this->sessionId,
            ];

            $response = $client->withHeaders($headers)->post("{$this->baseUrl}/api/messages/send", $payload);

            if ($response->successful()) {
                $body = $response->json();
                return [
                    'success' => true,
                    'message_id' => $body['data']['id'] ?? ($body['id'] ?? uniqid('akg_')),
                    'error' => null,
                    'driver' => 'wa_akg',
                ];
            }

            Log::warning('WA-AKG Gateway failed to send message', [
                'phone' => $normalizedPhone,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'message_id' => null,
                'error' => 'WA-AKG Error (' . $response->status() . '): ' . $response->body(),
                'driver' => 'wa_akg',
            ];
        } catch (\Throwable $e) {
            Log::error('WA-AKG Gateway Exception: ' . $e->getMessage(), [
                'phone' => $normalizedPhone,
            ]);

            return [
                'success' => false,
                'message_id' => null,
                'error' => 'Connection Exception: ' . $e->getMessage(),
                'driver' => 'wa_akg',
            ];
        }
    }

    public function checkStatus(): array
    {
        try {
            $client = Http::timeout(5);
            if (!empty($this->apiKey)) {
                $client = $client->withHeaders(['x-api-key' => $this->apiKey]);
            }
            $response = $client->get("{$this->baseUrl}/api/sessions/{$this->sessionId}/status");

            return [
                'connected' => $response->successful(),
                'status' => $response->json() ?? [],
                'driver' => 'wa_akg',
            ];
        } catch (\Throwable $e) {
            return [
                'connected' => false,
                'error' => $e->getMessage(),
                'driver' => 'wa_akg',
            ];
        }
    }

    protected function normalizePhone(string $phone): string
    {
        $clean = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($clean, '0')) {
            $clean = '62' . substr($clean, 1);
        }
        return $clean;
    }
}

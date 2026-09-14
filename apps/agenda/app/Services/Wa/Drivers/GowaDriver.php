<?php

namespace App\Services\Wa\Drivers;

use App\Services\Wa\Contracts\WaGatewayDriverInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GowaDriver implements WaGatewayDriverInterface
{
    protected string $baseUrl;
    protected string $user;
    protected string $pass;
    protected string $deviceId;

    public function __construct(array $config = [])
    {
        $this->baseUrl = rtrim($config['base_url'] ?? 'http://localhost:3000', '/');
        $this->user = $config['user'] ?? '';
        $this->pass = $config['pass'] ?? '';
        $this->deviceId = $config['device_id'] ?? '';
    }

    public function sendMessage(string $targetPhone, string $message): array
    {
        $normalizedPhone = $this->normalizePhone($targetPhone);

        try {
            $client = Http::timeout(10);

            if (!empty($this->user) && !empty($this->pass)) {
                $client = $client->withBasicAuth($this->user, $this->pass);
            }

            $headers = ['Content-Type' => 'application/json'];
            if (!empty($this->deviceId)) {
                $headers['X-Device-Id'] = $this->deviceId;
            }

            $response = $client->withHeaders($headers)->post("{$this->baseUrl}/send/message", [
                'phone' => $normalizedPhone,
                'message' => $message,
            ]);

            if ($response->successful()) {
                $body = $response->json();
                return [
                    'success' => true,
                    'message_id' => $body['data']['id'] ?? ($body['id'] ?? uniqid('gowa_')),
                    'error' => null,
                    'driver' => 'gowa',
                ];
            }

            Log::warning('GOWA Gateway failed to send message', [
                'phone' => $normalizedPhone,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'message_id' => null,
                'error' => 'GOWA Error (' . $response->status() . '): ' . $response->body(),
                'driver' => 'gowa',
            ];
        } catch (\Throwable $e) {
            Log::error('GOWA Gateway Exception: ' . $e->getMessage(), [
                'phone' => $normalizedPhone,
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message_id' => null,
                'error' => 'Connection Exception: ' . $e->getMessage(),
                'driver' => 'gowa',
            ];
        }
    }

    public function checkStatus(): array
    {
        try {
            $client = Http::timeout(5);
            if (!empty($this->user) && !empty($this->pass)) {
                $client = $client->withBasicAuth($this->user, $this->pass);
            }
            if (!empty($this->deviceId)) {
                $client = $client->withHeaders(['X-Device-Id' => $this->deviceId]);
            }

            $response = $client->get("{$this->baseUrl}/app/status");

            return [
                'connected' => $response->successful(),
                'status' => $response->json() ?? [],
                'driver' => 'gowa',
            ];
        } catch (\Throwable $e) {
            return [
                'connected' => false,
                'error' => $e->getMessage(),
                'driver' => 'gowa',
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

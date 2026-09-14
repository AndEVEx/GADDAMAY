<?php

namespace App\Services\Wa\Drivers;

use App\Services\Wa\Contracts\WaGatewayDriverInterface;
use Illuminate\Support\Facades\Log;

class LogWaDriver implements WaGatewayDriverInterface
{
    public function sendMessage(string $targetPhone, string $message): array
    {
        Log::info("[SIMULASI WA GATEWAY] Mengirim pesan ke {$targetPhone}: " . $message);

        return [
            'success' => true,
            'message_id' => 'sim_' . uniqid(),
            'error' => null,
            'driver' => 'log',
        ];
    }

    public function checkStatus(): array
    {
        return [
            'connected' => true,
            'status' => ['mode' => 'simulation_log'],
            'driver' => 'log',
        ];
    }
}

<?php

namespace App\Services\Wa\Contracts;

interface WaGatewayDriverInterface
{
    /**
     * Send a WhatsApp message to target phone number.
     * 
     * @param string $targetPhone Destination phone number (e.g. 08123456789 or 628123456789)
     * @param string $message Text content of the message
     * @return array ['success' => bool, 'message_id' => ?string, 'error' => ?string, 'driver' => string]
     */
    public function sendMessage(string $targetPhone, string $message): array;

    /**
     * Check if the gateway is connected / active.
     */
    public function checkStatus(): array;
}

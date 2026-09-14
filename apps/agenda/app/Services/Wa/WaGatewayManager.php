<?php

namespace App\Services\Wa;

use App\Services\Wa\Contracts\WaGatewayDriverInterface;
use App\Services\Wa\Drivers\GowaDriver;
use App\Services\Wa\Drivers\WaAkgDriver;
use App\Services\Wa\Drivers\LogWaDriver;
use InvalidArgumentException;

class WaGatewayManager
{
    protected array $drivers = [];
    protected string $defaultDriver;

    public function __construct()
    {
        $this->defaultDriver = config('services.wa_gateway.driver', 'gowa');
    }

    public function driver(?string $name = null): WaGatewayDriverInterface
    {
        $name = $name ?: $this->defaultDriver;

        if (!isset($this->drivers[$name])) {
            $this->drivers[$name] = $this->createDriver($name);
        }

        return $this->drivers[$name];
    }

    protected function createDriver(string $name): WaGatewayDriverInterface
    {
        $config = config("services.wa_gateway.{$name}", []);

        return match ($name) {
            'gowa' => new GowaDriver($config),
            'wa_akg' => new WaAkgDriver($config),
            'log' => new LogWaDriver(),
            default => throw new InvalidArgumentException("Driver WhatsApp Gateway [{$name}] tidak didukung."),
        };
    }

    public function sendMessage(string $targetPhone, string $message): array
    {
        return $this->driver()->sendMessage($targetPhone, $message);
    }

    public function checkStatus(): array
    {
        return $this->driver()->checkStatus();
    }
}

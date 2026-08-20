<?php

namespace App\Services;

use App\Models\PushSubscription;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class WebPushService
{
    private static ?WebPush $webPushInstance = null;

    /**
     * Get or initialize the WebPush client instance.
     */
    public static function getClient(): WebPush
    {
        if (self::$webPushInstance === null) {
            $auth = [
                'VAPID' => [
                    'subject' => config('webpush.vapid.subject', 'mailto:admin@smkn2indramayu.sch.id'),
                    'publicKey' => config('webpush.vapid.public_key'),
                    'privateKey' => config('webpush.vapid.private_key'),
                ],
            ];

            $options = config('webpush.options', [
                'TTL' => 86400,
                'urgency' => 'high',
            ]);

            self::$webPushInstance = new WebPush($auth, $options);
            self::$webPushInstance->setReuseVAPIDHeaders(true);
        }

        return self::$webPushInstance;
    }

    /**
     * Send Web Push notification to a specific user across all their registered devices.
     */
    public static function sendToUser(User|string $user, string $title, string $body, ?string $actionUrl = null, array $customData = []): int
    {
        $userId = $user instanceof User ? $user->id : $user;
        $subscriptions = PushSubscription::where('user_id', $userId)->get();

        if ($subscriptions->isEmpty()) {
            return 0;
        }

        $payload = json_encode([
            'title' => $title,
            'body' => $body,
            'icon' => '/pwa-icons/icon-192.png',
            'badge' => '/pwa-icons/icon-192.png',
            'action_url' => $actionUrl ?? '/guru/dashboard',
            'vibrate' => [300, 150, 300, 150, 300],
            'data' => array_merge([
                'url' => $actionUrl ?? '/guru/dashboard',
                'timestamp' => time(),
            ], $customData),
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $webPush = self::getClient();
        $queuedCount = 0;

        foreach ($subscriptions as $sub) {
            try {
                $webPushSubscription = Subscription::create([
                    'endpoint' => $sub->endpoint,
                    'publicKey' => $sub->public_key,
                    'authToken' => $sub->auth_token,
                    'contentEncoding' => $sub->content_encoding ?? 'aes128gcm',
                ]);

                $webPush->queueNotification($webPushSubscription, $payload);
                $queuedCount++;
            } catch (\Throwable $e) {
                Log::warning("Failed to queue WebPush for subscription {$sub->id}: " . $e->getMessage());
            }
        }

        if ($queuedCount === 0) {
            return 0;
        }

        $sentCount = 0;
        try {
            foreach ($webPush->flush() as $report) {
                $endpoint = $report->getRequest()->getUri()->__toString();
                if ($report->isSuccess()) {
                    $sentCount++;
                } else {
                    $statusCode = $report->getResponse()?->getStatusCode();
                    Log::warning("WebPush delivery failed to [{$endpoint}]: {$report->getReason()} (HTTP {$statusCode})");

                    // If expired or invalid (404 Not Found or 410 Gone), remove from database
                    if (in_array($statusCode, [404, 410])) {
                        PushSubscription::where('endpoint', $endpoint)->delete();
                        Log::info("Cleaned up expired WebPush endpoint: {$endpoint}");
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error("WebPush flush error: " . $e->getMessage());
        }

        return $sentCount;
    }

    /**
     * Send Web Push notification to all users matching specific roles (e.g. ['admin', 'waka', 'kepsek']).
     */
    public static function sendToRoles(array $roles, string $title, string $body, ?string $actionUrl = null, array $customData = []): int
    {
        $users = User::whereIn('role', $roles)->get();
        $totalSent = 0;

        foreach ($users as $user) {
            $totalSent += self::sendToUser($user, $title, $body, $actionUrl, $customData);
        }

        return $totalSent;
    }
}

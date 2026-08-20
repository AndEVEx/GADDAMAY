<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PushSubscription;
use App\Services\WebPushService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    /**
     * Return public VAPID key for frontend push subscription handshake.
     */
    public function getVapidPublicKey(): JsonResponse
    {
        return response()->json([
            'publicKey' => config('webpush.vapid.public_key'),
        ]);
    }

    /**
     * Store or update Web Push subscription endpoint for the authenticated user.
     */
    public function subscribe(Request $request): JsonResponse
    {
        $request->validate([
            'endpoint' => 'required|string',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
            'contentEncoding' => 'nullable|string',
        ]);

        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $endpoint = $request->input('endpoint');
        $publicKey = $request->input('keys.p256dh');
        $authToken = $request->input('keys.auth');
        $contentEncoding = $request->input('contentEncoding', 'aes128gcm');
        $userAgent = $request->header('User-Agent');

        PushSubscription::updateOrCreate(
            ['endpoint' => $endpoint],
            [
                'user_id' => $user->id,
                'public_key' => $publicKey,
                'auth_token' => $authToken,
                'content_encoding' => $contentEncoding,
                'user_agent' => $userAgent,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Perangkat Anda berhasil terhubung dengan Notifikasi Push AgenDamay!',
        ]);
    }

    /**
     * Remove Web Push subscription endpoint.
     */
    public function unsubscribe(Request $request): JsonResponse
    {
        $endpoint = $request->input('endpoint');
        if (!empty($endpoint)) {
            PushSubscription::where('endpoint', $endpoint)
                ->where('user_id', auth()->id())
                ->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Langganan notifikasi push berhasil dihapus.',
        ]);
    }

    /**
     * Send a test push notification to verify the setup on user's device.
     */
    public function sendTestPush(): JsonResponse
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $sentCount = WebPushService::sendToUser(
            $user,
            '🔔 Uji Coba Notifikasi AgenDamay',
            'Selamat! Notifikasi Push HP Anda sudah aktif dan terhubung dengan sistem AgenDamay.',
            '/guru/dashboard',
            ['type' => 'test']
        );

        return response()->json([
            'success' => true,
            'sent_count' => $sentCount,
            'message' => $sentCount > 0
                ? "Notifikasi berhasil dikirim ke {$sentCount} perangkat Anda!"
                : "Belum ada perangkat yang terdaftar untuk menerima push notification.",
        ]);
    }
}

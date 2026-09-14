<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;

class TrackUserActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $now = Carbon::now('Asia/Jakarta');
            
            // Cache individual online status (TTL 10 mins)
            Cache::put('user_online_' . $user->id, [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'last_seen_at' => $now->toDateTimeString(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ], now()->addMinutes(10));

            // Maintain active user IDs list in Cache
            $onlineUserIds = Cache::get('online_user_ids', []);
            if (!in_array($user->id, $onlineUserIds)) {
                $onlineUserIds[] = $user->id;
            }
            // Clean expired users from the ID list
            $activeIds = [];
            foreach ($onlineUserIds as $id) {
                if (Cache::has('user_online_' . $id)) {
                    $activeIds[] = $id;
                }
            }
            Cache::put('online_user_ids', $activeIds, now()->addMinutes(30));
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class TimeRestrictionMiddleware
{
    /**
     * Blokir guru mengisi agenda setelah jam 16:00.
     * Admin dan Waka dapat bypass.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Admin dan Waka bisa bypass
        if ($user->canOverride()) {
            return $next($request);
        }

        $now = Carbon::now('Asia/Jakarta');

        // Blokir jika hari ini adalah Hari Libur Sekolah / Nasional
        $holiday = \App\Models\HariLibur::isHariLibur($now);
        if ($holiday) {
            if ($request->expectsJson() || $request->header('X-Livewire')) {
                return response()->json([
                    'message' => 'Hari ini adalah Hari Libur (' . $holiday->nama_hari_libur . '). KBM ditiadakan.'
                ], 403);
            }

            session()->flash('error', 'Hari ini adalah Hari Libur (' . $holiday->nama_hari_libur . '). KBM ditiadakan.');
            return redirect()->route('guru.dashboard');
        }

        if ($now->hour >= 16) {
            if ($request->expectsJson() || $request->header('X-Livewire')) {
                return response()->json([
                    'message' => 'Waktu pengisian agenda telah berakhir (maks. 16:00). Hubungi Admin/Waka untuk koreksi.'
                ], 403);
            }

            session()->flash('error', 'Waktu pengisian agenda telah berakhir (maks. 16:00). Hubungi Admin/Waka untuk koreksi.');
            return redirect()->back();
        }

        return $next($request);
    }
}

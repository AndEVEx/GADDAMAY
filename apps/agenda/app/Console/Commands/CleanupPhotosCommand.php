<?php

namespace App\Console\Commands;

use App\Models\AgendaHarian;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupPhotosCommand extends Command
{
    protected $signature = 'agenda:cleanup-photos {--days=14 : Days to keep photos}';
    protected $description = 'Hapus foto bukti yang lebih dari N hari';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoff = Carbon::now()->subDays($days);

        $agendas = AgendaHarian::whereNotNull('foto_bukti_path')
            ->where('created_at', '<', $cutoff)
            ->get();

        $deletedCount = 0;

        foreach ($agendas as $agenda) {
            $path = $agenda->foto_bukti_path;

            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
                $deletedCount++;
            }

            $agenda->update(['foto_bukti_path' => null]);
        }

        $this->info("Cleaned up {$deletedCount} photos older than {$days} days.");
        return self::SUCCESS;
    }
}

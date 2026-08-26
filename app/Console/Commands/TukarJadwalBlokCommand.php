<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\JadwalSwapService;

class TukarJadwalBlokCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jadwal:tukar-blok {--rombelA= : ID Rombel A} {--rombelB= : ID Rombel B}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tukar jadwal pelajaran antar rombel (rolling blok teori & produktif untuk jurusan TP, APHP, NKPI)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $rombelA = $this->option('rombelA');
        $rombelB = $this->option('rombelB');

        if ($rombelA && $rombelB) {
            $this->info("Menukar jadwal antara Rombel {$rombelA} dan {$rombelB}...");
            $res = JadwalSwapService::swapRombelSchedules($rombelA, $rombelB);
            if ($res['success']) {
                $this->info($res['message']);
                return Command::SUCCESS;
            } else {
                $this->error($res['message']);
                return Command::FAILURE;
            }
        }

        $this->info("Menjalankan pertukaran jadwal otomatis untuk jurusan TP, APHP/APHPi, dan NKPI (Tingkat X, XI, XII)...");
        $res = JadwalSwapService::swapAllVocationalBlockSchedules();

        if ($res['success']) {
            $this->info($res['message']);
            foreach ($res['details'] as $detail) {
                $this->line(" - " . $detail);
            }
            return Command::SUCCESS;
        } else {
            $this->warn($res['message']);
            return Command::SUCCESS;
        }
    }
}

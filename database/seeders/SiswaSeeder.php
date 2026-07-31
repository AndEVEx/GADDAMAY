<?php

namespace Database\Seeders;

use App\Models\Rombel;
use App\Models\Siswa;
use Illuminate\Database\Seeder;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        $rombels = Rombel::all();
        $count = 0;

        foreach ($rombels as $rombel) {
            for ($i = 1; $i <= 3; $i++) {
                Siswa::firstOrCreate(
                    ['nama' => "Siswa {$i} {$rombel->nama_kelas}", 'rombel_id' => $rombel->id],
                    ['nis' => sprintf('%04d%02d', $rombel->tingkat, $count + $i)]
                );
            }
            $count += 3;
        }

        $this->command->info("Siswa seeded: {$count} students (3 per rombel)");
    }
}

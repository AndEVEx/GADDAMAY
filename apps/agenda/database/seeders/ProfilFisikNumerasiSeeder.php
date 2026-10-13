<?php

namespace Database\Seeders;

use App\Models\LmsTkaPaket;
use App\Models\LmsTesFisikSiswa;
use App\Models\LmsTkaHasilSiswa;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProfilFisikNumerasiSeeder extends Seeder
{
    public function run(): void
    {
        $guru = User::where('role', 'guru')->first() ?: User::first();
        $siswaList = Siswa::take(5)->get();

        foreach ($siswaList as $idx => $siswa) {
            // 1. Data Fisik
            LmsTesFisikSiswa::updateOrCreate(
                [
                    'siswa_id' => $siswa->id,
                    'semester' => 'Ganjil',
                    'tahun_ajaran' => '2026/2027',
                ],
                [
                    'guru_olahraga_id' => $guru ? $guru->id : null,
                    'tanggal_tes' => now()->subDays(10 + $idx),
                    'tinggi_badan_cm' => 165 + ($idx * 2),
                    'berat_badan_kg' => 56 + ($idx * 3),
                    'bmi' => round((56 + ($idx * 3)) / pow((165 + ($idx * 2)) / 100, 2), 1),
                    'kategori_bmi' => 'Normal / Ideal',
                    'lari_1200m_detik' => 310 + ($idx * 15),
                    'push_up_1min' => 30 + ($idx * 2),
                    'sit_up_1min' => 36 + ($idx * 2),
                    'shuttle_run_detik' => 11.0 + ($idx * 0.2),
                    'sit_and_reach_cm' => 17 + $idx,
                    'skor_kebugaran' => 84 + $idx,
                    'predikat' => 'Sangat Baik (A)',
                    'catatan_guru_olahraga' => 'Daya tahan kardiorespirasi sangat prima, disarankan menjaga hidrasi dan terus berlatih secara konsisten.',
                ]
            );

            // 2. Data Numerasi TKA
            $paket = LmsTkaPaket::first();
            if ($paket) {
                LmsTkaHasilSiswa::updateOrCreate(
                    [
                        'paket_id' => $paket->id,
                        'siswa_id' => $siswa->id,
                    ],
                    [
                        'waktu_mulai' => now()->subDays(5),
                        'waktu_selesai' => now()->subDays(5)->addMinutes(45),
                        'durasi_detik' => 2700,
                        'jumlah_benar' => 17,
                        'jumlah_salah' => 3,
                        'nilai_skor' => 85,
                        'lembar_jawaban' => ['1' => 'A', '2' => 'B', '3' => 'C'],
                        'status' => 'selesai',
                    ]
                );
            }
        }
    }
}

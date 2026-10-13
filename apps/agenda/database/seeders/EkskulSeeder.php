<?php

namespace Database\Seeders;

use App\Models\Ekskul;
use App\Models\EkskulAnggota;
use App\Models\EkskulPertemuan;
use App\Models\EkskulPresensi;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EkskulSeeder extends Seeder
{
    public function run(): void
    {
        $guru = User::where('role', 'guru')->first() ?: User::first();
        $admin = User::where('role', 'admin')->first() ?: $guru;

        $ekskulList = [
            [
                'nama_ekskul' => 'Paskibraka Korps Taruna SMKN 2',
                'slug' => 'paskibraka-korps-taruna',
                'kategori' => 'bela_negara_ketarunaan',
                'deskripsi' => 'Penggemblengan kedisiplinan, baris berbaris (PBB), formasi pengibaran bendera pusaka, dan kepemimpinan ketarunaan.',
                'jadwal_rutin' => 'Setiap Sabtu, 07.30 - 10.30 WIB',
                'tempat_latihan' => 'Lapangan Upacara Utama SMKN 2 Indramayu',
                'pembina_guru_id' => $guru?->id,
                'pelatih_nama' => 'Serka (Purn) Herman Suherman',
                'kontak_pelatih' => '0812-2233-4455',
                'logo_ikon' => 'bi-flag-fill',
            ],
            [
                'nama_ekskul' => 'IT Cyber Club & Robotika Vokasi',
                'slug' => 'it-cyber-robotika',
                'kategori' => 'sains_teknologi',
                'deskripsi' => 'Komunitas riset web development, PWA offline, Internet of Things (IoT), robotika Arduino, dan keamanan siber sekolah.',
                'jadwal_rutin' => 'Setiap Sabtu, 09.00 - 12.00 WIB',
                'tempat_latihan' => 'Laboratorium Komputer PPLG & IoT Center',
                'pembina_guru_id' => $admin?->id,
                'pelatih_nama' => 'Bayu Wicaksono, S.Kom.',
                'kontak_pelatih' => '0813-8899-7711',
                'logo_ikon' => 'bi-cpu-fill',
            ],
            [
                'nama_ekskul' => 'Pramuka Ambalan Suryanegara (Gudep 02)',
                'slug' => 'pramuka-ambalan-suryanegara',
                'kategori' => 'bela_negara_ketarunaan',
                'deskripsi' => 'Pendidikan kepanduan, pioneering tali-temali, penjelajahan alam, navigasi darat, dan kepemimpinan pramuka penegak.',
                'jadwal_rutin' => 'Setiap Jumat, 15.30 - 17.30 WIB',
                'tempat_latihan' => 'Bumi Perkemahan / Halaman Barat Sekolah',
                'pembina_guru_id' => $guru?->id,
                'pelatih_nama' => 'Kak Rudi Hartono, M.Pd.',
                'kontak_pelatih' => '0857-1122-3344',
                'logo_ikon' => 'bi-compass-fill',
            ],
            [
                'nama_ekskul' => 'Futsal & Sepak Bola Taruna',
                'slug' => 'futsal-taruna',
                'kategori' => 'olahraga',
                'deskripsi' => 'Pengembangan bakat olahraga bola, taktik futsal modern, turnamen liga antar-SMK, dan pembinaan stamina kardio.',
                'jadwal_rutin' => 'Selasa & Kamis, 16.00 - 18.00 WIB',
                'tempat_latihan' => 'Lapangan Olahraga Serbaguna SMKN 2',
                'pembina_guru_id' => $guru?->id,
                'pelatih_nama' => 'Coach Didin Wahyudi, S.Pd.',
                'kontak_pelatih' => '0821-4455-6677',
                'logo_ikon' => 'bi-dribbble',
            ],
            [
                'nama_ekskul' => 'Palang Merah Remaja (PMR Wira)',
                'slug' => 'pmr-wira',
                'kategori' => 'sosial_pmi',
                'deskripsi' => 'Pelatihan pertolongan pertama pada kecelakaan (P3K), kesiapsiagaan bencana, donor darah sukarela, dan posko kesehatan upacara.',
                'jadwal_rutin' => 'Setiap Sabtu, 08.00 - 10.30 WIB',
                'tempat_latihan' => 'Ruang UKS Terpadu & Lapangan Sekolah',
                'pembina_guru_id' => $guru?->id,
                'pelatih_nama' => 'Ns. Ratna Dewi, S.Kep.',
                'kontak_pelatih' => '0819-3322-1100',
                'logo_ikon' => 'bi-heart-pulse-fill',
            ],
            [
                'nama_ekskul' => 'Seni Musik & Karawitan Tradisional Dermayu',
                'slug' => 'karawitan-seni-musik',
                'kategori' => 'seni_budaya',
                'deskripsi' => 'Pelestarian seni gamelan dermayuan, kolaborasi musik etnik modern, dan paduan suara upacara wisuda sekolah.',
                'jadwal_rutin' => 'Setiap Sabtu, 13.00 - 15.30 WIB',
                'tempat_latihan' => 'Gedung Kesenian & Budaya Sekolah',
                'pembina_guru_id' => $guru?->id,
                'pelatih_nama' => 'Ki Dalang Sukardi',
                'kontak_pelatih' => '0877-6655-4433',
                'logo_ikon' => 'bi-music-note-beamed',
            ],
        ];

        $savedEkskuls = [];
        foreach ($ekskulList as $e) {
            $savedEkskuls[] = Ekskul::updateOrCreate(['slug' => $e['slug']], $e);
        }

        // 2. DAFTARKAN ANGGOTA SISWA KE EKSKUL
        $siswaAll = Siswa::take(10)->get();
        if ($siswaAll->count() > 0 && count($savedEkskuls) > 0) {
            $paskibra = $savedEkskuls[0];
            $itClub = $savedEkskuls[1];

            foreach ($siswaAll as $idx => $s) {
                // Semua siswa masuk Paskibra atau IT Club
                $targetEkskul = $idx % 2 === 0 ? $paskibra : $itClub;
                $jabatan = $idx === 0 ? 'ketua' : ($idx === 1 ? 'sekretaris' : 'anggota');

                $anggota = EkskulAnggota::updateOrCreate(
                    [
                        'ekskul_id' => $targetEkskul->id,
                        'siswa_id' => $s->id,
                        'tahun_ajaran' => '2026/2027',
                    ],
                    [
                        'jabatan_anggota' => $jabatan,
                        'nomor_anggota' => 'EKS-' . strtoupper(substr($targetEkskul->slug, 0, 3)) . '-' . str_pad($idx + 1, 3, '0', STR_PAD_LEFT),
                        'status_keanggotaan' => 'aktif',
                        'nilai_predikat' => 'A (Sangat Baik)',
                    ]
                );
            }

            // 3. BUAT 1 PERTEMUAN LATIHAN CONTOH PASKIBRA DENGAN PRESENSI
            $pertemuan = EkskulPertemuan::updateOrCreate(
                [
                    'ekskul_id' => $paskibra->id,
                    'tanggal_pertemuan' => now()->subDays(3)->format('Y-m-d'),
                ],
                [
                    'jam_mulai' => '07:30',
                    'jam_selesai' => '10:30',
                    'topik_latihan' => 'Latihan PBB Dasar & Formasi Pengibaran 17-an',
                    'deskripsi_kegiatan' => 'Pemanasan jasmani, latihan langkah tegap, penghormatan bendera, dan formasi shaf 3 peleton.',
                    'pencatat_user_id' => $guru?->id,
                    'status_pertemuan' => 'selesai',
                ]
            );

            // Isi Presensi Pertemuan
            $anggotaPaskibra = EkskulAnggota::where('ekskul_id', $paskibra->id)->get();
            foreach ($anggotaPaskibra as $aIdx => $a) {
                $status = $aIdx === 2 ? 'I' : 'H';
                $metode = $aIdx % 2 === 0 ? 'scan_qr' : 'input_manual';

                EkskulPresensi::updateOrCreate(
                    [
                        'ekskul_pertemuan_id' => $pertemuan->id,
                        'siswa_id' => $a->siswa_id,
                    ],
                    [
                        'ekskul_anggota_id' => $a->id,
                        'status' => $status,
                        'metode_presensi' => $metode,
                        'waktu_scan' => $status === 'H' ? now()->subDays(3)->setTime(7, 35 + $aIdx, 0) : null,
                        'catatan' => $status === 'I' ? 'Izin ada keperluan keluarga' : 'Hadir tepat waktu berseragam dinas',
                    ]
                );
            }
        }
    }
}

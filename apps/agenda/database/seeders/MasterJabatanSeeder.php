<?php

namespace Database\Seeders;

use App\Models\MasterJabatan;
use App\Models\PenugasanJabatan;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Seeder;

class MasterJabatanSeeder extends Seeder
{
    public function run(): void
    {
        // 1. DAFTAR MASTER JABATAN GURU & TENAGA KEPENDIDIKAN
        $jabatanGuru = [
            [
                'kode_jabatan' => 'wali_kelas',
                'nama_jabatan' => 'Wali Kelas',
                'kategori' => 'guru',
                'level_hierarki' => 2,
                'deskripsi_tugas' => 'Membina kelas, memonitor presensi gerbang & KBM, tes fisik PJOK, latihan TKA, dan buku saku PERDUPTAR siswa binaan.',
                'delegasi_modul' => 'walikelas.monitoring,parenting.guru.disiplin',
                'hak_akses' => 'verifikator',
            ],
            [
                'kode_jabatan' => 'guru_bk',
                'nama_jabatan' => 'Guru Bimbingan Konseling (BK)',
                'kategori' => 'guru',
                'level_hierarki' => 2,
                'deskripsi_tugas' => 'Bimbingan konseling siswa, penanganan pelanggaran disiplin sedang/berat PERDUPTAR, dan pemanggilan orang tua.',
                'delegasi_modul' => 'perduptar.poin,parenting.guru.disiplin',
                'hak_akses' => 'admin',
            ],
            [
                'kode_jabatan' => 'pembina_kesiswaan',
                'nama_jabatan' => 'Pembina Kesiswaan & Ketarunaan',
                'kategori' => 'guru',
                'level_hierarki' => 1,
                'deskripsi_tugas' => 'Penanggung jawab utama pembinaan kedisiplinan taruna, pengesahan poin PERDUPTAR 2026, dan sidang disiplin.',
                'delegasi_modul' => 'perduptar.poin',
                'hak_akses' => 'admin',
            ],
            [
                'kode_jabatan' => 'guru_piket',
                'nama_jabatan' => 'Guru Piket Sekolah & Gerbang',
                'kategori' => 'guru',
                'level_hierarki' => 2,
                'deskripsi_tugas' => 'Pencatatan keterlambatan gerbang, penerbitan surat izin masuk/keluar KBM, dan pemantauan ketertiban apel.',
                'delegasi_modul' => 'perizinan.index,perduptar.poin',
                'hak_akses' => 'operator',
            ],
            [
                'kode_jabatan' => 'koordinator_literasi',
                'nama_jabatan' => 'Koordinator Literasi Sekolah',
                'kategori' => 'guru',
                'level_hierarki' => 1,
                'deskripsi_tugas' => 'Pengelola sesi siaran live literasi lapangan Rabu pagi, kurasi teks bacaan pemateri, dan kuis pemahaman literasi.',
                'delegasi_modul' => 'literasi.kelola,literasi.presenter',
                'hak_akses' => 'admin',
            ],
            [
                'kode_jabatan' => 'kepala_perpustakaan',
                'nama_jabatan' => 'Kepala Perpustakaan Digital',
                'kategori' => 'guru',
                'level_hierarki' => 1,
                'deskripsi_tugas' => 'Manajemen katalog e-book kurikulum, monitoring waktu membaca siswa di PWA, dan pengelolaan rak buku digital.',
                'delegasi_modul' => 'perpus.kelola,perpus.katalog',
                'hak_akses' => 'admin',
            ],
            [
                'kode_jabatan' => 'koordinator_ujikom',
                'nama_jabatan' => 'Koordinator LSP-P1 & Ujikom',
                'kategori' => 'guru',
                'level_hierarki' => 1,
                'deskripsi_tugas' => 'Verifikasi dokumen pendaftaran APL-01 & APL-02 asesi, penjadwalan TUK uji kompetensi, dan penugasan asesor.',
                'delegasi_modul' => 'ujikom.admin.verifikasi',
                'hak_akses' => 'admin',
            ],
            [
                'kode_jabatan' => 'pembimbing_lks',
                'nama_jabatan' => 'Pembimbing LKS (Lomba Keterampilan Siswa)',
                'kategori' => 'guru',
                'level_hierarki' => 2,
                'deskripsi_tugas' => 'Pengisian materi pembinaan intensif diferensiasi LKS tingkat kabupaten, provinsi, dan nasional di LMS.',
                'delegasi_modul' => 'lms.guru.materi',
                'hak_akses' => 'operator',
            ],
            [
                'kode_jabatan' => 'manajer_tefa',
                'nama_jabatan' => 'Pengelola / Manajer Teaching Factory (TEFA)',
                'kategori' => 'guru',
                'level_hierarki' => 1,
                'deskripsi_tugas' => 'Pengelolaan order pesanan industri (SPK), pembagian job sheet produksi, kontrol mutu (QC), dan log jam kerja siswa.',
                'delegasi_modul' => 'tefa.admin.orders,tefa.admin.detail',
                'hak_akses' => 'admin',
            ],
            [
                'kode_jabatan' => 'koordinator_pkl',
                'nama_jabatan' => 'Koordinator Pokja PKL & Hubin',
                'kategori' => 'guru',
                'level_hierarki' => 1,
                'deskripsi_tugas' => 'Pemetaan penempatan DUDI mitra, monitoring presensi geolocation PKL, dan evaluasi berkala jurnal siswa.',
                'delegasi_modul' => 'pkl.admin.kelola,pkl.guru.monitoring',
                'hak_akses' => 'admin',
            ],
            [
                'kode_jabatan' => 'guru_olahraga_pjok',
                'nama_jabatan' => 'Guru Olahraga PJOK (Pengelola Tes Fisik)',
                'kategori' => 'guru',
                'level_hierarki' => 2,
                'deskripsi_tugas' => 'Pengukuran antropometri IMT (BMI), tes kebugaran lari 1200m, push-up, sit-up, shuttle run, dan kelenturan.',
                'delegasi_modul' => 'lms.fisik.kelola',
                'hak_akses' => 'operator',
            ],
            [
                'kode_jabatan' => 'proktor_tka',
                'nama_jabatan' => 'Proktor & Teknisi Asesmen (TKA/ANBK)',
                'kategori' => 'guru',
                'level_hierarki' => 2,
                'deskripsi_tugas' => 'Pengelolaan bank soal numerasi/literasi TKA, penjadwalan simulasi ujian CBT, dan analisis butir soal.',
                'delegasi_modul' => 'lms.tka.kelola',
                'hak_akses' => 'operator',
            ],
            [
                'kode_jabatan' => 'admin_ai',
                'nama_jabatan' => 'Administrator & Kurator DamAI Edukasi',
                'kategori' => 'guru',
                'level_hierarki' => 2,
                'deskripsi_tugas' => 'Pengawasan sistem chatbot DamAI sekolah, pemeliharaan model Open-WebUI lokal, dan keamanan prompt edukatif.',
                'delegasi_modul' => 'ai.chatbot',
                'hak_akses' => 'admin',
            ],
            [
                'kode_jabatan' => 'kepala_lab',
                'nama_jabatan' => 'Kepala Laboratorium Komputer / Bengkel',
                'kategori' => 'guru',
                'level_hierarki' => 1,
                'deskripsi_tugas' => 'Pemeliharaan fasilitas bengkel vokasi, inventaris perangkat praktikum, dan keselamatan kerja (K3L).',
                'delegasi_modul' => 'admin.kelas',
                'hak_akses' => 'operator',
            ],
            [
                'kode_jabatan' => 'pembina_ekskul',
                'nama_jabatan' => 'Pembina OSIS & Ekstrakurikuler',
                'kategori' => 'guru',
                'level_hierarki' => 2,
                'deskripsi_tugas' => 'Pendampingan kegiatan organisasi kesiswaan dan validasi sertifikat poin prestasi ekstrakurikuler.',
                'delegasi_modul' => 'perduptar.poin',
                'hak_akses' => 'operator',
            ],
        ];

        // 2. DAFTAR MASTER JABATAN SISWA / TARUNA
        $jabatanSiswa = [
            [
                'kode_jabatan' => 'danyon_taruna',
                'nama_jabatan' => 'Komandan Batalyon (Danyon Taruna)',
                'kategori' => 'siswa',
                'level_hierarki' => 1,
                'deskripsi_tugas' => 'Pemimpin tertinggi Korps Taruna SMKN 2 Indramayu, pengendali apel pagi, dan teladan kedisiplinan PERDUPTAR.',
                'delegasi_modul' => 'perduptar.poin,profil.index',
                'hak_akses' => 'operator',
            ],
            [
                'kode_jabatan' => 'wadanyon_taruna',
                'nama_jabatan' => 'Wakil Komandan Batalyon (Wadanyon)',
                'kategori' => 'siswa',
                'level_hierarki' => 1,
                'deskripsi_tugas' => 'Membantu Danyon dalam koordinasi internal staf batalyon dan penertiban barisan upacara.',
                'delegasi_modul' => 'perduptar.poin',
                'hak_akses' => 'operator',
            ],
            [
                'kode_jabatan' => 'poltar_disiplin',
                'nama_jabatan' => 'Polisi Taruna (Poltar / Provost)',
                'kategori' => 'siswa',
                'level_hierarki' => 2,
                'deskripsi_tugas' => 'Pengawas penegakan tata tertib gerbang, pemeriksaan kerapian seragam & potongan rambut ukuran 0.0.0.',
                'delegasi_modul' => 'perduptar.poin',
                'hak_akses' => 'operator',
            ],
            [
                'kode_jabatan' => 'danton_ketua_kelas',
                'nama_jabatan' => 'Komandan Peleton (Danton / Ketua Kelas)',
                'kategori' => 'siswa',
                'level_hierarki' => 2,
                'deskripsi_tugas' => 'Memimpin kelas, verifikasi token OTP kehadiran KBM, memimpin doa & tadarus 15 menit, dan lapor presensi jam ke-1.',
                'delegasi_modul' => 'ketua.verifikasi,ketua.jadwal,ketua.anggota',
                'hak_akses' => 'operator',
            ],
            [
                'kode_jabatan' => 'ketua_osis',
                'nama_jabatan' => 'Ketua OSIS & MPK',
                'kategori' => 'siswa',
                'level_hierarki' => 1,
                'deskripsi_tugas' => 'Penyalur aspirasi taruna, koordinasi bakti sosial, peringatan hari besar nasional, dan duta perwakilan sekolah.',
                'delegasi_modul' => 'profil.index',
                'hak_akses' => 'viewer',
            ],
            [
                'kode_jabatan' => 'duta_literasi',
                'nama_jabatan' => 'Duta Literasi Sekolah',
                'kategori' => 'siswa',
                'level_hierarki' => 3,
                'deskripsi_tugas' => 'Membantu presenter siaran Rabu pagi, menggerakkan pojok baca kelas, dan kampanye resensi buku di PWA.',
                'delegasi_modul' => 'literasi.siswa.live,perpus.katalog',
                'hak_akses' => 'viewer',
            ],
            [
                'kode_jabatan' => 'duta_perpustakaan',
                'nama_jabatan' => 'Duta Perpustakaan Digital (Pustakawan Muda)',
                'kategori' => 'siswa',
                'level_hierarki' => 3,
                'deskripsi_tugas' => 'Membantu sosialisasi rak buku offline PWA kepada rekan siswa dan pendataan minat baca kelas.',
                'delegasi_modul' => 'perpus.katalog',
                'hak_akses' => 'viewer',
            ],
            [
                'kode_jabatan' => 'pm_siswa_tefa',
                'nama_jabatan' => 'Project Manager Siswa TEFA',
                'kategori' => 'siswa',
                'level_hierarki' => 2,
                'deskripsi_tugas' => 'Memimpin tim kerja siswa dalam pengerjaan order industri, log kerja job sheet, dan koordinasi dengan instruktur.',
                'delegasi_modul' => 'tefa.siswa.log',
                'hak_akses' => 'operator',
            ],
            [
                'kode_jabatan' => 'helpdesk_siswa_it',
                'nama_jabatan' => 'Tim Duta IT & Helpdesk PWA Siswa',
                'kategori' => 'siswa',
                'level_hierarki' => 3,
                'deskripsi_tugas' => 'Membantu instalasi aplikasi PWA di ponsel siswa dan pelaporan kendala jaringan lokal gerbang.',
                'delegasi_modul' => 'profil.kartu',
                'hak_akses' => 'viewer',
            ],
            [
                'kode_jabatan' => 'peer_counselor_bk',
                'nama_jabatan' => 'Konselor Sebaya (Duta Anti-Bullying BK)',
                'kategori' => 'siswa',
                'level_hierarki' => 3,
                'deskripsi_tugas' => 'Mendengarkan aspirasi rekan siswa sebaya dan menjadi jembatan pelaporan dini potensi konflik ke guru BK.',
                'delegasi_modul' => 'perduptar.poin',
                'hak_akses' => 'viewer',
            ],
        ];

        $allMaster = array_merge($jabatanGuru, $jabatanSiswa);
        $masterMap = [];
        foreach ($allMaster as $m) {
            $record = MasterJabatan::updateOrCreate(
                ['kode_jabatan' => $m['kode_jabatan']],
                $m
            );
            $masterMap[$m['kode_jabatan']] = $record->id;
        }

        // 3. SEED PENUGASAN CONTOH (GURU & SISWA)
        $adminUser = User::where('role', 'admin')->first();
        $guruUser = User::where('role', 'guru')->first();
        $firstRombel = Rombel::first();
        $siswaList = Siswa::take(3)->get();

        // Penugasan Guru: Kepala Perpustakaan & Pembina Kesiswaan
        if ($adminUser && isset($masterMap['kepala_perpustakaan'])) {
            PenugasanJabatan::updateOrCreate(
                [
                    'master_jabatan_id' => $masterMap['kepala_perpustakaan'],
                    'user_id' => $adminUser->id,
                    'tahun_ajaran' => '2026/2027',
                ],
                [
                    'tipe_subjek' => 'guru',
                    'nomor_sk' => 'SK.800/012/SMKN2/2026',
                    'tanggal_mulai' => now()->startOfYear(),
                    'status_aktif' => true,
                    'keterangan' => 'Penanggung Jawab Sistem Perpustakaan Digital PWA',
                ]
            );
        }

        if ($guruUser && isset($masterMap['wali_kelas'])) {
            PenugasanJabatan::updateOrCreate(
                [
                    'master_jabatan_id' => $masterMap['wali_kelas'],
                    'user_id' => $guruUser->id,
                    'tahun_ajaran' => '2026/2027',
                ],
                [
                    'tipe_subjek' => 'guru',
                    'rombel_id' => $firstRombel?->id,
                    'nomor_sk' => 'SK.800/015/SMKN2/2026',
                    'tanggal_mulai' => now()->startOfYear(),
                    'status_aktif' => true,
                    'keterangan' => 'Wali Kelas ' . ($firstRombel->nama_rombel ?? 'X Vokasi'),
                ]
            );
        }

        // Penugasan Siswa: Danyon Taruna & Danton Kelas
        if (isset($siswaList[0]) && isset($masterMap['danyon_taruna'])) {
            PenugasanJabatan::updateOrCreate(
                [
                    'master_jabatan_id' => $masterMap['danyon_taruna'],
                    'siswa_id' => $siswaList[0]->id,
                    'tahun_ajaran' => '2026/2027',
                ],
                [
                    'tipe_subjek' => 'siswa',
                    'rombel_id' => $siswaList[0]->rombel_id,
                    'nomor_sk' => 'SK.800/BATALYON/2026',
                    'tanggal_mulai' => now()->startOfYear(),
                    'status_aktif' => true,
                    'keterangan' => 'Komandan Batalyon Angkatan 2026',
                ]
            );
        }

        if (isset($siswaList[1]) && isset($masterMap['danton_ketua_kelas'])) {
            PenugasanJabatan::updateOrCreate(
                [
                    'master_jabatan_id' => $masterMap['danton_ketua_kelas'],
                    'siswa_id' => $siswaList[1]->id,
                    'tahun_ajaran' => '2026/2027',
                ],
                [
                    'tipe_subjek' => 'siswa',
                    'rombel_id' => $siswaList[1]->rombel_id,
                    'nomor_sk' => 'SK.800/DANTON/2026',
                    'tanggal_mulai' => now()->startOfYear(),
                    'status_aktif' => true,
                    'keterangan' => 'Danton / KM Kelas ' . ($siswaList[1]->rombel->nama_rombel ?? '-'),
                ]
            );
        }
    }
}

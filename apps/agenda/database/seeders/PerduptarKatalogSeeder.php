<?php

namespace Database\Seeders;

use App\Models\PerduptarKatalog;
use App\Models\PerduptarPoinSiswa;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Seeder;

class PerduptarKatalogSeeder extends Seeder
{
    public function run(): void
    {
        // 1. KATALOG PRESTASI (PASAL 33 PERDUPTAR 2026)
        $prestasiItems = [
            // Prestasi Luar Biasa (+11 s/d +20)
            [
                'tipe' => 'prestasi',
                'kategori_tingkat' => 'Prestasi Luar Biasa',
                'bobot_poin' => 20,
                'pasal_rujukan' => 'Pasal 33 Ayat 3 Huruf a',
                'nama_butir' => 'Tindakan Terpuji Menyelamatkan Jiwa & Harta Benda',
                'deskripsi_aturan' => 'Melakukan suatu perbuatan yang sangat terpuji sehingga menghindarkan dari bahaya yang mengancam keselamatan jiwa dan harta benda.',
                'sanksi_default' => 'Penghargaan Resmi Sekolah & Piagam',
            ],
            [
                'tipe' => 'prestasi',
                'kategori_tingkat' => 'Prestasi Luar Biasa',
                'bobot_poin' => 20,
                'pasal_rujukan' => 'Pasal 33 Ayat 3 Huruf a',
                'nama_butir' => 'Penemuan IPTEK Baru Bermanfaat Bagi Masyarakat / Juara Internasional',
                'deskripsi_aturan' => 'Penemuan teknologi tepat guna atau menjadi juara lomba akademik/non-akademik tingkat internasional.',
                'sanksi_default' => 'Penghargaan Resmi Kepala Sekolah',
            ],
            [
                'tipe' => 'prestasi',
                'kategori_tingkat' => 'Prestasi Luar Biasa',
                'bobot_poin' => 19,
                'pasal_rujukan' => 'Pasal 33 Ayat 3 Huruf b',
                'nama_butir' => 'Juara Lomba / Pertandingan Tingkat Nasional & Penyelamatan Lingkungan',
                'deskripsi_aturan' => 'Menjadi juara perlombaan tingkat nasional atau aktif dalam aksi penyelamatan lingkungan hidup.',
                'sanksi_default' => 'Piagam Penghargaan & Beasiswa Prestasi',
            ],
            [
                'tipe' => 'prestasi',
                'kategori_tingkat' => 'Prestasi Luar Biasa',
                'bobot_poin' => 16,
                'pasal_rujukan' => 'Pasal 33 Ayat 3 Huruf c',
                'nama_butir' => 'Juara Tingkat Provinsi / Peserta Tingkat Internasional',
                'deskripsi_aturan' => 'Menjadi juara lomba tingkat provinsi atau peserta lomba internasional.',
                'sanksi_default' => 'Piagam Penghargaan Sekolah',
            ],
            [
                'tipe' => 'prestasi',
                'kategori_tingkat' => 'Prestasi Luar Biasa',
                'bobot_poin' => 15,
                'pasal_rujukan' => 'Pasal 33 Ayat 3 Huruf f',
                'nama_butir' => 'Juara Lomba Akademik / Non-Akademik Tingkat Kabupaten',
                'deskripsi_aturan' => 'Menjadi juara 1, 2, atau 3 lomba tingkat kabupaten.',
                'sanksi_default' => 'Piagam Penghargaan',
            ],
            [
                'tipe' => 'prestasi',
                'kategori_tingkat' => 'Prestasi Luar Biasa',
                'bobot_poin' => 13,
                'pasal_rujukan' => 'Pasal 33 Ayat 3 Huruf h',
                'nama_butir' => 'Juara Lomba Tingkat Kecamatan',
                'deskripsi_aturan' => 'Menjadi juara dalam perlombaan akademik maupun non-akademik tingkat kecamatan.',
                'sanksi_default' => 'Piagam Penghargaan',
            ],

            // Prestasi Biasa (+5 s/d +10)
            [
                'tipe' => 'prestasi',
                'kategori_tingkat' => 'Prestasi Biasa',
                'bobot_poin' => 10,
                'pasal_rujukan' => 'Pasal 33 Ayat 4 Huruf a',
                'nama_butir' => 'Panitia Kegiatan Sosial / Lingkungan Hidup Tingkat Provinsi',
                'deskripsi_aturan' => 'Menjadi panitia atau peserta aktif kegiatan sosial dan lingkungan tingkat provinsi/nasional.',
                'sanksi_default' => 'Sertifikat Aktivis Taruna',
            ],
            [
                'tipe' => 'prestasi',
                'kategori_tingkat' => 'Prestasi Biasa',
                'bobot_poin' => 8,
                'pasal_rujukan' => 'Pasal 33 Ayat 4 Huruf c',
                'nama_butir' => 'Ketua Staff Batalyon Taruna SMKN 2 Indramayu',
                'deskripsi_aturan' => 'Menjabat dan melaksanakan amanah sebagai Komandan/Ketua Batalyon Taruna.',
                'sanksi_default' => 'Tanda Jabatan & Sertifikat Kepemimpinan',
            ],
            [
                'tipe' => 'prestasi',
                'kategori_tingkat' => 'Prestasi Biasa',
                'bobot_poin' => 7,
                'pasal_rujukan' => 'Pasal 33 Ayat 4 Huruf d',
                'nama_butir' => 'Pengurus Inti Staff Batalyon Taruna SMKN 2 Indramayu',
                'deskripsi_aturan' => 'Duduk dalam struktur kepengurusan Staff Batalyon Taruna SMKN 2 Indramayu.',
                'sanksi_default' => 'Sertifikat Pengurus Batalyon',
            ],
            [
                'tipe' => 'prestasi',
                'kategori_tingkat' => 'Prestasi Biasa',
                'bobot_poin' => 6,
                'pasal_rujukan' => 'Pasal 33 Ayat 4 Huruf e',
                'nama_butir' => 'Ketua / Koordinator Ekstrakurikuler Sekolah',
                'deskripsi_aturan' => 'Sebagai ketua kelompok pengembangan diri ekstrakurikuler SMKN 2 Indramayu.',
                'sanksi_default' => 'Sertifikat Ketua Ekskul',
            ],
            [
                'tipe' => 'prestasi',
                'kategori_tingkat' => 'Prestasi Biasa',
                'bobot_poin' => 5,
                'pasal_rujukan' => 'Pasal 33 Ayat 4 Huruf f',
                'nama_butir' => 'Komandan Peleton (Danton) / Juara 1-3 Program Keahlian',
                'deskripsi_aturan' => 'Menjadi ketua kelas / danton atau meraih peringkat 1 sampai 3 akademik kejuruan.',
                'sanksi_default' => 'Piagam Peringkat Kelas',
            ],

            // Prestasi Cukup (+1 s/d +4)
            [
                'tipe' => 'prestasi',
                'kategori_tingkat' => 'Prestasi Cukup',
                'bobot_poin' => 4,
                'pasal_rujukan' => 'Pasal 33 Ayat 5 Huruf a',
                'nama_butir' => 'Juara Lomba Antar Taruna / Antar Jurusan Sekolah',
                'deskripsi_aturan' => 'Juara perlombaan yang diselenggarakan sekolah, jurusan, atau Batalyon.',
                'sanksi_default' => 'Piagam Sekolah',
            ],
            [
                'tipe' => 'prestasi',
                'kategori_tingkat' => 'Prestasi Cukup',
                'bobot_poin' => 3,
                'pasal_rujukan' => 'Pasal 33 Ayat 5 Huruf b',
                'nama_butir' => 'Anggota Tim Inti Ekskul / Petugas Upacara Kebesaran',
                'deskripsi_aturan' => 'Mengikuti upacara kebesaran sebagai petugas resmi atau anggota inti ekskul.',
                'sanksi_default' => 'Apresiasi Pembina',
            ],
            [
                'tipe' => 'prestasi',
                'kategori_tingkat' => 'Prestasi Cukup',
                'bobot_poin' => 2,
                'pasal_rujukan' => 'Pasal 33 Ayat 5 Huruf c',
                'nama_butir' => 'Peserta Gerak Jalan, Pawai Budaya, atau Donor Darah',
                'deskripsi_aturan' => 'Mengikuti kegiatan pawai, gerak jalan HUT RI, atau bakti donor darah PMI.',
                'sanksi_default' => 'Catatan Positif Buku Saku',
            ],
            [
                'tipe' => 'prestasi',
                'kategori_tingkat' => 'Prestasi Cukup',
                'bobot_poin' => 1,
                'pasal_rujukan' => 'Pasal 33 Ayat 5 Huruf d',
                'nama_butir' => 'Berperan Aktif Dalam Kegiatan Positif Sekolah',
                'deskripsi_aturan' => 'Aktif berkontribusi dalam program kebersihan, literasi, atau ketertiban harian.',
                'sanksi_default' => 'Poin Positif Buku Saku',
            ],
        ];

        // 2. KATALOG PELANGGARAN (PASAL 34 PERDUPTAR 2026)
        $pelanggaranItems = [
            // Pelanggaran Sangat Berat (Pasal 34 Ayat 4)
            [
                'tipe' => 'pelanggaran',
                'kategori_tingkat' => 'Pelanggaran Sangat Berat',
                'bobot_poin' => -50,
                'pasal_rujukan' => 'Pasal 34 Ayat 4',
                'nama_butir' => 'Makar / Tindakan Kriminal / Narkoba / Terbukti Anggota Geng Motor',
                'deskripsi_aturan' => 'Tindakan kriminal, narkoba, asusila, mencemarkan nama baik sekolah, melawan pimpinan, atau anggota geng motor.',
                'sanksi_default' => 'Dikeluarkan / Dicopot Status Taruna SMKN 2 Indramayu',
            ],
            [
                'tipe' => 'pelanggaran',
                'kategori_tingkat' => 'Pelanggaran Sangat Berat',
                'bobot_poin' => -50,
                'pasal_rujukan' => 'Pasal 34 Ayat 4 Huruf e',
                'nama_butir' => 'Bolos 6 Hari Berturut-turut Tanpa Alasan Sah',
                'deskripsi_aturan' => 'Tidak mengikuti kegiatan belajar atau praktek 6 hari berturut-turut tanpa izin sah.',
                'sanksi_default' => 'Pencopotan Status Taruna / Dikeluarkan',
            ],

            // Pelanggaran Berat (Pasal 34 Ayat 5)
            [
                'tipe' => 'pelanggaran',
                'kategori_tingkat' => 'Pelanggaran Berat',
                'bobot_poin' => -30,
                'pasal_rujukan' => 'Pasal 34 Ayat 5 Huruf f',
                'nama_butir' => 'Perundungan / Bullying Terhadap Sesama Taruna',
                'deskripsi_aturan' => 'Melakukan tindakan perundungan fisik, verbal, atau mental terhadap sesama taruna/orang lain.',
                'sanksi_default' => 'Skorsing 1 Tahun s/d Dikeluarkan dari Sekolah',
            ],
            [
                'tipe' => 'pelanggaran',
                'kategori_tingkat' => 'Pelanggaran Berat',
                'bobot_poin' => -25,
                'pasal_rujukan' => 'Pasal 34 Ayat 5 Huruf d',
                'nama_butir' => 'Membawa Senjata Tajam / Menimbulkan Keonaran di Sekolah',
                'deskripsi_aturan' => 'Menyimpan senjata tajam tanpa izin atau membuat keributan/keonaran di lingkungan sekolah.',
                'sanksi_default' => 'Skorsing & Pemanggilan Orang Tua',
            ],

            // Pelanggaran Sedang (-6 s/d -20) (Pasal 34 Ayat 6)
            [
                'tipe' => 'pelanggaran',
                'kategori_tingkat' => 'Pelanggaran Sedang',
                'bobot_poin' => -20,
                'pasal_rujukan' => 'Pasal 34 Ayat 6 Huruf a',
                'nama_butir' => 'Keluar Lingkungan Sekolah Tanpa Izin (Bolos Gerbang)',
                'deskripsi_aturan' => 'Meninggalkan area sekolah saat jam belajar tanpa surat izin resmi dari petugas piket.',
                'sanksi_default' => 'Lari Keliling Lapangan 8-12 Putaran & Pemanggilan Orang Tua',
            ],
            [
                'tipe' => 'pelanggaran',
                'kategori_tingkat' => 'Pelanggaran Sedang',
                'bobot_poin' => -20,
                'pasal_rujukan' => 'Pasal 34 Ayat 6 Huruf a',
                'nama_butir' => 'Tidak Bijak Menggunakan Media Sosial (Pencemaran / Ujaran Kebencian)',
                'deskripsi_aturan' => 'Mengunggah konten berseragam sekolah yang melanggar norma kesopanan dan ketertiban.',
                'sanksi_default' => 'Pembinaan BK & Surat Peringatan (SP)',
            ],
            [
                'tipe' => 'pelanggaran',
                'kategori_tingkat' => 'Pelanggaran Sedang',
                'bobot_poin' => -15,
                'pasal_rujukan' => 'Pasal 34 Ayat 6 Huruf b',
                'nama_butir' => 'Tidak Jujur Saat Ujian / Merobek Menghilangkan Buku Saku',
                'deskripsi_aturan' => 'Mencontek saat ujian atau merusak/menghilangkan buku saku disiplin taruna.',
                'sanksi_default' => 'Remedial Ujian & Mengganti Buku Saku',
            ],
            [
                'tipe' => 'pelanggaran',
                'kategori_tingkat' => 'Pelanggaran Sedang',
                'bobot_poin' => -10,
                'pasal_rujukan' => 'Pasal 34 Ayat 6 Huruf c',
                'nama_butir' => 'Mengubah Bentuk Seragam Korps / Mangkir Dari Sanksi Pelanggaran',
                'deskripsi_aturan' => 'Memodifikasi seragam tidak sesuai aturan atau tidak menjalankan sanksi yang telah ditetapkan.',
                'sanksi_default' => 'Push Up / Sit Up 20-30x & Kerja Bakti 1 Minggu',
            ],
            [
                'tipe' => 'pelanggaran',
                'kategori_tingkat' => 'Pelanggaran Sedang',
                'bobot_poin' => -6,
                'pasal_rujukan' => 'Pasal 34 Ayat 6 Huruf d',
                'nama_butir' => 'Tidak Membawa / Mencoret Buku Saku / Bolos Belajar 2 Hari',
                'deskripsi_aturan' => 'Tidak membawa buku saku saat berdinas di sekolah atau mencoret lembaran peraturan.',
                'sanksi_default' => 'Push Up 20x & Pembersihan Lingkungan',
            ],

            // Pelanggaran Ringan (-1 s/d -5) (Pasal 34 Ayat 7)
            [
                'tipe' => 'pelanggaran',
                'kategori_tingkat' => 'Pelanggaran Ringan',
                'bobot_poin' => -5,
                'pasal_rujukan' => 'Pasal 34 Ayat 7 Huruf a',
                'nama_butir' => 'Terlambat Masuk Sekolah Lebih Dari 15 Menit',
                'deskripsi_aturan' => 'Datang terlambat melebihi batas toleransi jam apel/pembelajaran tanpa alasan sah.',
                'sanksi_default' => 'Push Up / Squat / Sit Up 3-6 Set di Pos Penjagaan',
            ],
            [
                'tipe' => 'pelanggaran',
                'kategori_tingkat' => 'Pelanggaran Ringan',
                'bobot_poin' => -4,
                'pasal_rujukan' => 'Pasal 34 Ayat 7 Huruf b',
                'nama_butir' => 'Tidak Mengikuti Upacara Bendera / Membuat Keributan di Kelas',
                'deskripsi_aturan' => 'Mangkir dari upacara pengibaran bendera hari Senin atau membuat gaduh saat KBM.',
                'sanksi_default' => 'Pembersihan Ruang Belajar / Lapangan',
            ],
            [
                'tipe' => 'pelanggaran',
                'kategori_tingkat' => 'Pelanggaran Ringan',
                'bobot_poin' => -3,
                'pasal_rujukan' => 'Pasal 34 Ayat 7 Huruf c',
                'nama_butir' => 'Pakaian Seragam Tidak Lengkap (Kopel, Pet, Dasi, Bet Jurusan)',
                'deskripsi_aturan' => 'Atribut seragam ketarunaan tidak lengkap saat jam sekolah.',
                'sanksi_default' => 'Hukuman Fisik Ringan 10-20x Push Up',
            ],
            [
                'tipe' => 'pelanggaran',
                'kategori_tingkat' => 'Pelanggaran Ringan',
                'bobot_poin' => -2,
                'pasal_rujukan' => 'Pasal 34 Ayat 7 Huruf d',
                'nama_butir' => 'Tidak Memberi Hormat Pada Guru/Senior atau Membuang Sampah Sembarangan',
                'deskripsi_aturan' => 'Tidak bersikap sempurna saat berpapasan dengan atasan/guru atau membuang sampah sembarangan.',
                'sanksi_default' => 'Tindakan Disiplin Sikap Sempurna & Membersihkan Sampah',
            ],
            [
                'tipe' => 'pelanggaran',
                'kategori_tingkat' => 'Pelanggaran Ringan',
                'bobot_poin' => -1,
                'pasal_rujukan' => 'Pasal 34 Ayat 7 Huruf e',
                'nama_butir' => 'Sepatu Tidak Disemir / Rambut Tidak Sesuai Ketentuan (Ukuran 0.0.0)',
                'deskripsi_aturan' => 'Perlengkapan pakaian tidak mengkilat, sepatu kotor, atau memelihara kumis/jambang.',
                'sanksi_default' => 'Dicukur Ukuran Standar 0.0.0 & Semir Sepatu di Tempat',
            ],
        ];

        // Insert / Update Katalog
        $allItems = array_merge($prestasiItems, $pelanggaranItems);
        foreach ($allItems as $item) {
            PerduptarKatalog::updateOrCreate(
                [
                    'pasal_rujukan' => $item['pasal_rujukan'],
                    'nama_butir' => $item['nama_butir'],
                ],
                $item
            );
        }

        // 3. SEED CONTOH REKAM CATATAN POIN SISWA
        $siswaList = Siswa::take(3)->get();
        $adminGuru = User::where('role', 'admin')->orWhere('role', 'guru')->first();
        $katalogPrestasi = PerduptarKatalog::where('tipe', 'prestasi')->first();
        $katalogPelanggaran = PerduptarKatalog::where('tipe', 'pelanggaran')->where('bobot_poin', -5)->first();

        if ($siswaList->count() > 0 && $adminGuru) {
            // Siswa 1: Punya Prestasi Juara Kelas (+5) dan Aktif (+1)
            PerduptarPoinSiswa::updateOrCreate(
                [
                    'siswa_id' => $siswaList[0]->id,
                    'nama_butir' => 'Komandan Peleton (Danton) / Juara 1-3 Program Keahlian',
                ],
                [
                    'katalog_id' => $katalogPrestasi?->id,
                    'tipe' => 'prestasi',
                    'bobot_poin' => 5,
                    'pasal_rujukan' => 'Pasal 33 Ayat 4 Huruf f',
                    'kronologi_kejadian' => 'Terpilih menjadi Komandan Peleton (Danton) upacara bendera dan memimpin dengan disiplin tinggi.',
                    'lokasi_kejadian' => 'Lapangan Utama SMKN 2 Indramayu',
                    'tanggal_kejadian' => now()->subDays(7),
                    'petugas_user_id' => $adminGuru->id,
                    'peran_petugas' => 'Pembina Kesiswaan',
                    'sanksi_diberikan' => 'Apresiasi dan Poin Prestasi Buku Saku',
                    'status_tindakan' => 'selesai_pembinaan',
                ]
            );

            // Siswa 2: Pernah terlambat 1x (-5) dan telah selesai push-up edukatif
            if (isset($siswaList[1])) {
                PerduptarPoinSiswa::updateOrCreate(
                    [
                        'siswa_id' => $siswaList[1]->id,
                        'nama_butir' => 'Terlambat Masuk Sekolah Lebih Dari 15 Menit',
                    ],
                    [
                        'katalog_id' => $katalogPelanggaran?->id,
                        'tipe' => 'pelanggaran',
                        'bobot_poin' => -5,
                        'pasal_rujukan' => 'Pasal 34 Ayat 7 Huruf a',
                        'kronologi_kejadian' => 'Tiba di pos penjagaan pukul 07.25 WIB karena kendala rantai sepeda motor.',
                        'lokasi_kejadian' => 'Pintu Gerbang Utama Pos Penjagaan',
                        'tanggal_kejadian' => now()->subDays(3),
                        'petugas_user_id' => $adminGuru->id,
                        'peran_petugas' => 'Guru Piket Sekolah',
                        'sanksi_diberikan' => 'Push Up 15x dan lapor ke Pusbinter Jurusan',
                        'status_tindakan' => 'selesai_pembinaan',
                    ]
                );
            }
        }
    }
}

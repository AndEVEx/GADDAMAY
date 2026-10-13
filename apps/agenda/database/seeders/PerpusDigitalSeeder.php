<?php

namespace Database\Seeders;

use App\Models\PerpusBuku;
use App\Models\PerpusKategori;
use App\Models\PerpusRiwayatBaca;
use App\Models\TugasTambahanGuru;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PerpusDigitalSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat direktori storage untuk buku & cover jika belum ada
        $coverDir = public_path('storage/perpus/covers');
        $pdfDir = public_path('storage/perpus/pdfs');
        if (!File::isDirectory($coverDir)) {
            File::makeDirectory($coverDir, 0755, true);
        }
        if (!File::isDirectory($pdfDir)) {
            File::makeDirectory($pdfDir, 0755, true);
        }

        // 2. Kategori Buku
        $kategoriData = [
            [
                'nama_kategori' => 'Teknologi & Vokasi PPLG',
                'slug' => 'teknologi-vokasi-pplg',
                'keterangan' => 'Modul pemrograman, rekayasa perangkat lunak, web development, dan basis data.',
                'ikon' => 'bi-laptop',
            ],
            [
                'nama_kategori' => 'Teknik & Otomotif Vokasi',
                'slug' => 'teknik-otomotif-vokasi',
                'keterangan' => 'Buku panduan mesin otomotif, chasis, kelistrikan kendaraan, dan fabrikasi logam.',
                'ikon' => 'bi-wrench-adjustable',
            ],
            [
                'nama_kategori' => 'Literasi & Sastra Sekolah',
                'slug' => 'literasi-sastra-sekolah',
                'keterangan' => 'Koleksi novel edukasi, antologi cerpen inspiratif, dan karya sastra nasional.',
                'ikon' => 'bi-book',
            ],
            [
                'nama_kategori' => 'Budaya Kerja Industri (5R)',
                'slug' => 'budaya-kerja-industri-5r',
                'keterangan' => 'Etos kerja, K3L industri, komunikasi bisnis, dan standar ISO dunia kerja.',
                'ikon' => 'bi-shield-check',
            ],
            [
                'nama_kategori' => 'Karakter & Budi Pekerti',
                'slug' => 'karakter-budi-pekerti',
                'keterangan' => 'Penguatan profil pelajar pancasila, motivasi kepemimpinan, dan keagamaan.',
                'ikon' => 'bi-heart-fill',
            ],
        ];

        $kategoriMap = [];
        foreach ($kategoriData as $kd) {
            $kat = PerpusKategori::updateOrCreate(
                ['slug' => $kd['slug']],
                $kd
            );
            $kategoriMap[$kd['slug']] = $kat->id;
        }

        // 3. Siapkan Sample PDF sederhana yang valid
        $samplePdfPath = public_path('storage/perpus/pdfs/sample-panduan-vokasi.pdf');
        if (!File::exists($samplePdfPath)) {
            // PDF minimalis valid 3 halaman
            $minimalPdf = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Kids[3 0 R 4 0 R 5 0 R]/Count 3>>endobj\n3 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 612 792]/Contents 6 0 R/Resources<</Font<</F1 9 0 R>>>>>>endobj\n4 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 612 792]/Contents 7 0 R/Resources<</Font<</F1 9 0 R>>>>>>endobj\n5 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 612 792]/Contents 8 0 R/Resources<</Font<</F1 9 0 R>>>>>>endobj\n6 0 obj<</Length 95>>stream\nBT /F1 24 Tf 50 700 Td (Modul Pembelajaran Digital SMKN 2 Indramayu) Tj ET\nBT /F1 14 Tf 50 650 Td (Bab 1: Pengenalan Arsitektur Perangkat Lunak Modern) Tj ET\nendstream\nendobj\n7 0 obj<</Length 102>>stream\nBT /F1 20 Tf 50 700 Td (Bab 2: Budaya Kerja & Disiplin Industri) Tj ET\nBT /F1 14 Tf 50 650 Td (Penerapan standar 5S / 5R dalam laboratorium dan bengkel praktik.) Tj ET\nendstream\nendobj\n8 0 obj<</Length 92>>stream\nBT /F1 20 Tf 50 700 Td (Bab 3: Evaluasi & Uji Mandiri Kompetensi) Tj ET\nBT /F1 14 Tf 50 650 Td (Latihan studi kasus integrasi sistem informasi sekolah.) Tj ET\nendstream\nendobj\n9 0 obj<</Type/Font/Subtype/Type1/BaseFont/Helvetica>>endobj\nxref\n0 10\n0000000000 65535 f \n0000000009 00000 n \n0000000056 00000 n \n0000000133 00000 n \n0000000248 00000 n \n0000000363 00000 n \n0000000478 00000 n \n0000000645 00000 n \n0000000819 00000 n \n0000000983 00000 n \ntrailer<</Size 10/Root 1 0 R>>\nstartxref\n1051\n%%EOF";
            File::put($samplePdfPath, $minimalPdf);
        }

        // 4. Koleksi Buku Digital
        $bukuData = [
            [
                'kategori_id' => $kategoriMap['teknologi-vokasi-pplg'] ?? 1,
                'judul' => 'Dasar Pemrograman Web Fullstack & PWA SMKN 2',
                'slug' => Str::slug('Dasar Pemrograman Web Fullstack & PWA SMKN 2'),
                'penulis' => 'Tim Pengembang PPLG SMKN 2 Indramayu',
                'penerbit' => 'Damay Press',
                'tahun_terbit' => '2026',
                'isbn' => '978-602-001-26-1',
                'sinopsis' => 'Panduan komprehensif implementasi Progressive Web Apps (PWA), Service Worker, IndexedDB offline, dan arsitektur backend modern untuk siswa kejuruan.',
                'file_pdf_url' => '/storage/perpus/pdfs/sample-panduan-vokasi.pdf',
                'cover_url' => null,
                'jumlah_halaman' => 128,
                'file_size_mb' => 2.45,
                'total_dibaca' => 48,
                'allow_offline_cache' => true,
                'is_published' => true,
            ],
            [
                'kategori_id' => $kategoriMap['budaya-kerja-industri-5r'] ?? 4,
                'judul' => 'Budaya Industri 5R & K3L Bengkel Modern',
                'slug' => Str::slug('Budaya Industri 5R & K3L Bengkel Modern'),
                'penulis' => 'Pokja Hubungan Industri & BKK SMKN 2',
                'penerbit' => 'Vokasi Edukasi',
                'tahun_terbit' => '2025',
                'isbn' => '978-602-002-25-8',
                'sinopsis' => 'Buku saku praktis penerapan Ringkas, Rapi, Resik, Rawat, dan Rajin dalam lingkungan bengkel mesin otomotif dan laboratorium komputer.',
                'file_pdf_url' => '/storage/perpus/pdfs/sample-panduan-vokasi.pdf',
                'cover_url' => null,
                'jumlah_halaman' => 84,
                'file_size_mb' => 1.80,
                'total_dibaca' => 32,
                'allow_offline_cache' => true,
                'is_published' => true,
            ],
            [
                'kategori_id' => $kategoriMap['literasi-sastra-sekolah'] ?? 3,
                'judul' => 'Lentera Pantura: Kumpulan Kisah Inspiratif Siswa Pesisir',
                'slug' => Str::slug('Lentera Pantura: Kumpulan Kisah Inspiratif Siswa Pesisir'),
                'penulis' => 'Klub Jurnalistik & Literasi SMKN 2',
                'penerbit' => 'Pustaka Indramayu',
                'tahun_terbit' => '2026',
                'isbn' => '978-602-003-26-5',
                'sinopsis' => 'Kisah perjuangan pemuda Pantura meraih cita-cita di dunia teknologi dan industri maritim, disajikan dengan bahasa yang menggugah motivasi belajar.',
                'file_pdf_url' => '/storage/perpus/pdfs/sample-panduan-vokasi.pdf',
                'cover_url' => null,
                'jumlah_halaman' => 140,
                'file_size_mb' => 3.10,
                'total_dibaca' => 65,
                'allow_offline_cache' => true,
                'is_published' => true,
            ],
            [
                'kategori_id' => $kategoriMap['karakter-budi-pekerti'] ?? 5,
                'judul' => 'Membangun Karakter Pelajar Pancasila yang Tangguh',
                'slug' => Str::slug('Membangun Karakter Pelajar Pancasila yang Tangguh'),
                'penulis' => 'Dra. Hj. Siti Rohmah, M.Pd.',
                'penerbit' => 'Damay Publishing',
                'tahun_terbit' => '2025',
                'isbn' => '978-602-004-25-4',
                'sinopsis' => 'Panduan penguatan etika, kejujuran akademik, toleransi, dan gotong royong dalam ekosistem sekolah menengah kejuruan.',
                'file_pdf_url' => '/storage/perpus/pdfs/sample-panduan-vokasi.pdf',
                'cover_url' => null,
                'jumlah_halaman' => 96,
                'file_size_mb' => 1.95,
                'total_dibaca' => 27,
                'allow_offline_cache' => true,
                'is_published' => true,
            ],
        ];

        $firstBuku = null;
        foreach ($bukuData as $b) {
            $bukuObj = PerpusBuku::updateOrCreate(
                ['isbn' => $b['isbn']],
                $b
            );
            if (!$firstBuku) $firstBuku = $bukuObj;
        }

        // 5. Pastikan ada Tugas Tambahan Kepala Perpustakaan untuk demo
        $adminUser = User::where('role', 'admin')->first();
        if ($adminUser) {
            TugasTambahanGuru::updateOrCreate(
                [
                    'guru_id' => $adminUser->id,
                    'jenis_tugas' => 'kepala_perpustakaan',
                ],
                [
                    'is_active' => true,
                    'keterangan' => 'Kepala Perpustakaan Digital SMKN 2 Indramayu',
                ]
            );
        }

        // 6. Buat 1 Contoh Riwayat Baca untuk Demo
        if ($adminUser && $firstBuku) {
            PerpusRiwayatBaca::updateOrCreate(
                [
                    'user_id' => $adminUser->id,
                    'buku_id' => $firstBuku->id,
                ],
                [
                    'halaman_terakhir' => 15,
                    'persentase_baca' => 12,
                    'durasi_detik' => 1450, // ~24 menit
                    'waktu_mulai_baca' => now()->subDays(2),
                    'waktu_terakhir_baca' => now()->subHours(3),
                    'status_selesai' => false,
                ]
            );
        }
    }
}

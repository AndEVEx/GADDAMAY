<?php

namespace Database\Seeders;

use App\Models\LiterasiSesi;
use App\Models\LiterasiSlide;
use App\Models\LiterasiSoal;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LiterasiDummySeeder extends Seeder
{
    public function run(): void
    {
        $guru = User::whereIn('role', ['guru', 'admin'])->first();

        $sesi = LiterasiSesi::create([
            'judul' => 'Membangun Etos Kerja Vokasi & Budaya Industri 5R',
            'tema' => 'Budaya Kerja & Karakter',
            'tanggal' => now()->toDateString(),
            'pemateri_id' => $guru?->id,
            'nama_pemateri' => $guru?->name ?? 'Drs. H. Ahmad Fauzi, M.Pd.',
            'jabatan_pemateri' => 'Koordinator Budaya Industri SMKN 2 Indramayu',
            'koordinator_id' => $guru?->id,
            'status' => 'live',
            'current_slide_index' => 1,
            'allow_quiz' => true,
            'total_slides' => 3,
            'deskripsi' => 'Penguatan karakter disiplin dan etos kerja profesional bagi seluruh siswa SMKN 2 Indramayu saat berada di bengkel, lab, maupun lingkungan industri.',
            'kode_sesi' => 'LIT-RABU01',
        ]);

        // Slide 1
        LiterasiSlide::create([
            'sesi_id' => $sesi->id,
            'urutan' => 1,
            'judul_slide' => 'Pentingnya Etos Kerja Profesional di Era Digital',
            'konten' => "Selamat pagi seluruh siswa-siswi SMKN 2 Indramayu.\n\nDalam dunia kerja modern, kecerdasan teknis (hard skills) hanyalah salah satu syarat masuk. Kunci utama keberlanjutan karir seseorang ditentukan oleh etos kerja, integritas, dan kemampuan beradaptasi (soft skills).\n\nEtos kerja bukan sekadar bekerja keras hingga lelah, melainkan kesadaran moral untuk selalu memberikan hasil terbaik, menghargai waktu rekan kerja, dan mematuhi standar operasional prosedur (SOP) dengan rasa tanggung jawab penuh tanpa perlu diawasi terus-menerus.",
            'poin_kunci' => "• Hard skills membuka pintu kesempatan, soft skills menentukan keberhasilan jangka panjang.\n• Disiplin waktu mencerminkan penghormatan terhadap diri sendiri dan mitra kerja.\n• Integritas: melakukan hal yang benar bahkan saat tidak ada yang melihat.",
            'catatan_pemateri' => 'Beri penekanan pada contoh keterlambatan 5 menit yang dapat merugikan rantai produksi di pabrik atau software house.',
        ]);

        // Slide 2
        LiterasiSlide::create([
            'sesi_id' => $sesi->id,
            'urutan' => 2,
            'judul_slide' => 'Penerapan Praktis Budaya 5R di Lingkungan Sekolah & Bengkel',
            'konten' => "Salah satu pilar utama efisiensi industri manufaktur dan teknologi adalah metodologi 5R (Ringkas, Rapi, Resik, Rawat, Rajin) yang diadaptasi dari prinsip 5S Jepang.\n\n1. Ringkas (Seiri): Memisahkan alat/barang yang masih diperlukan dengan yang tidak perlu.\n2. Rapi (Seiton): Menata dan memberi label pada alat kerja sehingga mudah ditemukan dalam hitungan detik.\n3. Resik (Seiso): Membersihkan area kerja setiap hari sebelum dan sesudah kegiatan produksi.\n4. Rawat (Seiketsu): Memelihara standar kebersihan dan kerapian secara konsisten.\n5. Rajin (Shitsuke): Membiasakan diri mematuhi SOP hingga menjadi budaya alami tanpa paksaan.",
            'poin_kunci' => "• 5R bukan sekadar piket bersih-bersih, tapi manajemen keselamatan dan produktivitas.\n• Bengkel atau lab yang rapi menurunkan risiko kecelakaan kerja hingga 70%.\n• Budayakan 'Datang Bersih, Pulang Rapi'.",
            'catatan_pemateri' => 'Ajak siswa melihat kondisi meja/bengkel mereka saat praktek kejuruan.',
        ]);

        // Slide 3
        LiterasiSlide::create([
            'sesi_id' => $sesi->id,
            'urutan' => 3,
            'judul_slide' => 'Komunikasi Asertif dan Kolaborasi Tim Vokasi',
            'konten' => "Dunia kerja saat ini tidak lagi didominasi oleh individu pekerja tunggal (lone wolf), melainkan tim lintas disiplin yang saling mendukung.\n\nKomunikasi asertif adalah keterampilan menyampaikan ide, kendala teknis, atau kritik secara jujur, lugas, dan santun tanpa merendahkan orang lain.\n\nJika dalam pengerjaan project Anda mengalami kendala atau bug sistem, jangan sembunyikan! Segera komunikasikan kepada pembimbing atau rekan tim agar solusi dapat ditemukan bersama.",
            'poin_kunci' => "• Berani bertanya saat menemui kendala teknis adalah tanda profesionalisme.\n• Dengarkan masukan rekan kerja dengan pikiran terbuka.\n• Sukses proyek adalah kemenangan bersama, bukan ego perseorangan.",
            'catatan_pemateri' => 'Tutup slide dengan instruksi kepada seluruh siswa untuk membuka tab Kuis & Refleksi di ponsel masing-masing.',
        ]);

        // Soal 1 (Pilihan Ganda)
        LiterasiSoal::create([
            'sesi_id' => $sesi->id,
            'urutan' => 1,
            'jenis_soal' => 'pilihan_ganda',
            'pertanyaan' => 'Prinsip 5R yang bermakna memisahkan alat atau barang yang diperlukan dengan yang sudah tidak terpakai adalah...',
            'opsi_a' => 'Ringkas (Seiri)',
            'opsi_b' => 'Rapi (Seiton)',
            'opsi_c' => 'Resik (Seiso)',
            'opsi_d' => 'Rawat (Seiketsu)',
            'opsi_e' => 'Rajin (Shitsuke)',
            'kunci_jawaban' => 'A',
            'bobot_skor' => 50,
            'pembahasan' => 'Ringkas (Seiri) adalah tindakan menyingkirkan barang-barang yang tidak diperlukan dari tempat kerja.',
        ]);

        // Soal 2 (Pilihan Ganda)
        LiterasiSoal::create([
            'sesi_id' => $sesi->id,
            'urutan' => 2,
            'jenis_soal' => 'pilihan_ganda',
            'pertanyaan' => 'Apa yang menjadi penentu utama keberlanjutan karir seseorang di industri menurut materi literasi?',
            'opsi_a' => 'Hanya nilai ijazah sekolah',
            'opsi_b' => 'Etos kerja, integritas, dan kemampuan beradaptasi (soft skills)',
            'opsi_c' => 'Bekerja sendiri tanpa mau berkomunikasi dengan tim',
            'opsi_d' => 'Menyembunyikan kesalahan teknis saat bekerja',
            'opsi_e' => 'Datang terlambat asalkan pekerjaan selesai',
            'kunci_jawaban' => 'B',
            'bobot_skor' => 50,
            'pembahasan' => 'Soft skills dan integritas adalah kunci keberlanjutan karir jangka panjang di industri.',
        ]);

        // Soal 3 (Esai Pendek)
        LiterasiSoal::create([
            'sesi_id' => $sesi->id,
            'urutan' => 3,
            'jenis_soal' => 'esai_pendek',
            'pertanyaan' => 'Tuliskan satu komitmen nyata yang akan Anda terapkan mulai hari ini di kelas atau bengkel kejuruan Anda terkait budaya kerja 5R!',
            'bobot_skor' => 20,
            'pembahasan' => 'Kriteria: Menyebutkan tindakan konkret (misal merapikan kabel lab, mengembalikan obeng ke toolbox, dll).',
        ]);
    }
}

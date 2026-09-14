<?php

namespace Database\Seeders;

use App\Models\MotivasiPantun;
use Illuminate\Database\Seeder;

class MotivasiPantunSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // PANTUN - SEBELUM MENGAJAR
            ['isi' => "Pergi ke pasar beli terasi,\nJangan lupa beli ikan pari.\nMari mengajar dengan prestasi,\nAgar murid jadi berseri.", 'tipe' => 'pantun', 'kategori' => 'sebelum_mengajar'],
            ['isi' => "Buah mangga masak di pohon,\nJatuh ke tanah dimakan burung.\nAyo mengajar jangan segan,\nIlmu berguna sepanjang lorong.", 'tipe' => 'pantun', 'kategori' => 'sebelum_mengajar'],
            ['isi' => "Air mengalir ke muara,\nIkan berenang dengan riang.\nGuru mengajar penuh suara,\nMurid belajar tanpa bimbang.", 'tipe' => 'pantun', 'kategori' => 'sebelum_mengajar'],
            ['isi' => "Kelapa muda di tepi pantai,\nAirnya segar diminum siang.\nSemangat baru sudah terpancar,\nAyo mengajar penuh sayang.", 'tipe' => 'pantun', 'kategori' => 'sebelum_mengajar'],
            ['isi' => "Pagi cerah mentari bersinar,\nBurung berkicau di ranting jati.\nSiapkan hati untuk mengajar,\nBerbagi ilmu penuh inspirasi.", 'tipe' => 'pantun', 'kategori' => 'sebelum_mengajar'],
            ['isi' => "Ke Cirebon membeli batik,\nMotifnya indah warna-warni.\nMengajar itu sungguh asyik,\nMembentuk masa depan negeri.", 'tipe' => 'pantun', 'kategori' => 'sebelum_mengajar'],
            ['isi' => "Buah salak dari Pondoh,\nRasanya manis tiada tara.\nAyo semangat jangan mudah bosan,\nMengajar dengan penuh cinta.", 'tipe' => 'pantun', 'kategori' => 'sebelum_mengajar'],

            // KATA MUTIARA - SEBELUM MENGAJAR
            ['isi' => 'Guru yang hebat bukan hanya mengajar, tetapi menginspirasi.', 'tipe' => 'kata_mutiara', 'kategori' => 'sebelum_mengajar'],
            ['isi' => 'Setiap anak adalah bintang, tugas kita membantu mereka bersinar.', 'tipe' => 'kata_mutiara', 'kategori' => 'sebelum_mengajar'],
            ['isi' => 'Mengajar adalah seni menanamkan keingintahuan.', 'tipe' => 'kata_mutiara', 'kategori' => 'sebelum_mengajar'],
            ['isi' => 'Satu buku, satu pena, satu guru, bisa mengubah dunia.', 'tipe' => 'kata_mutiara', 'kategori' => 'sebelum_mengajar'],
            ['isi' => 'Pendidikan bukanlah pengisian ember, melainkan penyalaan api.', 'tipe' => 'kata_mutiara', 'kategori' => 'sebelum_mengajar'],
            ['isi' => 'Guru: pahlawan tanpa tanda jasa, tetapi dengan tanda kasih.', 'tipe' => 'kata_mutiara', 'kategori' => 'sebelum_mengajar'],
            ['isi' => 'Yang paling indah dari mengajar adalah melihat murid berhasil melampaui gurunya.', 'tipe' => 'kata_mutiara', 'kategori' => 'sebelum_mengajar'],
            ['isi' => 'Setiap pertemuan di kelas adalah kesempatan emas menciptakan perubahan.', 'tipe' => 'kata_mutiara', 'kategori' => 'sebelum_mengajar'],

            // PANTUN - SIAP MENGAJAR
            ['isi' => "Ikan bandeng dari Indramayu,\nDiolah enak jadi pindang.\nHandshake berhasil sudah siap,\nAyo mulai belajar jangan bimbang!", 'tipe' => 'pantun', 'kategori' => 'siap_mengajar'],
            ['isi' => "Terbang tinggi burung elang,\nHinggap di dahan pohon cemara.\nSemangat mengajar jangan hilang,\nIlmu berguna untuk semua.", 'tipe' => 'pantun', 'kategori' => 'siap_mengajar'],
            ['isi' => "Pisang goreng hangat di pagi hari,\nDitemani teh manis di gelas.\nKelas sudah siap dimulai,\nBelajar bersama penuh ikhlas.", 'tipe' => 'pantun', 'kategori' => 'siap_mengajar'],
            ['isi' => "Ombak bergulung di laut lepas,\nNelayan berlayar penuh harap.\nKelas dimulai dengan tepat,\nSemoga ilmu bisa diserap.", 'tipe' => 'pantun', 'kategori' => 'siap_mengajar'],
            ['isi' => "Bunga melati harum semerbak,\nTumbuh di taman indah permai.\nSiap mengajar dengan semangat,\nMembuat murid lebih pandai.", 'tipe' => 'pantun', 'kategori' => 'siap_mengajar'],

            // KATA MUTIARA - SIAP MENGAJAR
            ['isi' => 'Kelas telah siap, mari kita wujudkan pembelajaran yang bermakna! 🎯', 'tipe' => 'kata_mutiara', 'kategori' => 'siap_mengajar'],
            ['isi' => 'Selamat mengajar! Ingat, Anda sedang membentuk masa depan bangsa. 🇮🇩', 'tipe' => 'kata_mutiara', 'kategori' => 'siap_mengajar'],
            ['isi' => 'Verifikasi sukses! Saatnya berbagi ilmu dengan penuh cinta. ❤️', 'tipe' => 'kata_mutiara', 'kategori' => 'siap_mengajar'],
            ['isi' => 'Pembelajaran dimulai! Jadikan hari ini lebih baik dari kemarin. ✨', 'tipe' => 'kata_mutiara', 'kategori' => 'siap_mengajar'],
            ['isi' => 'Guru terbaik bukan yang tahu segalanya, tapi yang tidak berhenti belajar. 📚', 'tipe' => 'kata_mutiara', 'kategori' => 'siap_mengajar'],
            ['isi' => 'Setiap detik di kelas adalah investasi masa depan. Mulai sekarang! ⏱️', 'tipe' => 'kata_mutiara', 'kategori' => 'siap_mengajar'],
            ['isi' => 'Handshake berhasil! Siap mencetak generasi unggul SMKN 2 Indramayu! 💪', 'tipe' => 'kata_mutiara', 'kategori' => 'siap_mengajar'],
            ['isi' => 'Bismillah, semoga ilmu yang diajarkan hari ini bermanfaat dunia akhirat. 🤲', 'tipe' => 'kata_mutiara', 'kategori' => 'siap_mengajar'],
        ];

        foreach ($data as $item) {
            MotivasiPantun::firstOrCreate($item);
        }

        $this->command->info('Motivasi & Pantun seeded: ' . count($data) . ' entries');
    }
}

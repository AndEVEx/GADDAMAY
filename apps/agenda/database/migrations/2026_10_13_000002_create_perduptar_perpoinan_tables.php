<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Katalog Master Butir Peraturan PERDUPTAR 2026
        Schema::create('perduptar_katalog', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('tipe', ['prestasi', 'pelanggaran']);
            $table->string('kategori_tingkat'); // Prestasi: Luar Biasa, Biasa, Cukup. Pelanggaran: Sangat Berat, Berat, Sedang, Ringan
            $table->integer('bobot_poin'); // +1 s/d +20 untuk prestasi, -1 s/d -20 untuk pelanggaran, 0 untuk sangat berat / berat langsung sanksi
            $table->string('pasal_rujukan'); // misal "Pasal 33 Ayat 3", "Pasal 34 Ayat 6"
            $table->string('nama_butir');
            $table->text('deskripsi_aturan')->nullable();
            $table->string('sanksi_default')->nullable(); // misal "Push Up 20-30x", "Dicukur 0.0.0", "Dikeluarkan dari SMKN 2"
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });

        // 2. Transaksi Pencatatan Poin Buku Saku Taruna/Siswa
        Schema::create('perduptar_poin_siswa', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->char('siswa_id', 36);
            $table->uuid('katalog_id')->nullable();
            $table->enum('tipe', ['prestasi', 'pelanggaran']);
            $table->integer('bobot_poin'); // Poin riil yang diberikan
            $table->string('pasal_rujukan')->nullable();
            $table->string('nama_butir');
            $table->text('kronologi_kejadian')->nullable();
            $table->string('lokasi_kejadian')->nullable();
            $table->date('tanggal_kejadian');
            $table->char('petugas_user_id', 36)->nullable();
            $table->string('peran_petugas')->nullable(); // Pembina Kesiswaan, Guru BK, Guru Piket, Wali Kelas, Staff Batalyon
            $table->string('sanksi_diberikan')->nullable();
            $table->enum('status_tindakan', ['pending', 'sedang_dibina', 'selesai_pembinaan'])->default('pending');
            $table->enum('surat_peringatan', ['tidak_ada', 'sp1', 'sp2', 'sp3', 'panggilan_ortu', 'skorsing', 'drop_out'])->default('tidak_ada');
            $table->string('foto_bukti')->nullable();
            $table->boolean('notif_ortu_terkirim')->default(false);
            $table->timestamps();

            $table->foreign('siswa_id')->references('id')->on('siswa')->onDelete('cascade');
            $table->foreign('katalog_id')->references('id')->on('perduptar_katalog')->onDelete('set null');
            $table->foreign('petugas_user_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['siswa_id', 'tipe', 'tanggal_kejadian']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perduptar_poin_siswa');
        Schema::dropIfExists('perduptar_katalog');
    }
};

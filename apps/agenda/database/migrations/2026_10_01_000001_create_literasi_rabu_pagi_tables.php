<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Sesi Literasi Lapangan (Rabu Pagi)
        Schema::create('literasi_sesi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('judul');
            $table->string('tema', 100)->default('Literasi Umum');
            $table->date('tanggal');
            $table->char('pemateri_id', 36)->nullable(); // Guru pemateri
            $table->string('nama_pemateri');
            $table->string('jabatan_pemateri', 150)->nullable();
            $table->char('koordinator_id', 36)->nullable(); // Koordinator literasi
            $table->string('status', 20)->default('draft'); // draft, live, selesai
            $table->integer('current_slide_index')->default(0); // slide aktif yang disiarkan real-time
            $table->boolean('allow_quiz')->default(false); // apakah kuis / evaluasi dibuka
            $table->integer('total_slides')->default(0);
            $table->text('deskripsi')->nullable();
            $table->string('kode_sesi', 20)->nullable()->unique();
            $table->timestamps();

            $table->foreign('pemateri_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('koordinator_id')->references('id')->on('users')->onDelete('set null');
        });

        // 2. Slide / Halaman Materi Literasi (Ditayangkan & Dikontrol Pemateri)
        Schema::create('literasi_slides', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('sesi_id');
            $table->integer('urutan')->default(1);
            $table->string('judul_slide');
            $table->longText('konten'); // Tulisan / naskah bacaan materi
            $table->text('poin_kunci')->nullable(); // Bullet point sorotan penting
            $table->string('gambar_url')->nullable();
            $table->text('catatan_pemateri')->nullable(); // Catatan presenter (speaker notes)
            $table->timestamps();

            $table->foreign('sesi_id')->references('id')->on('literasi_sesi')->onDelete('cascade');
        });

        // 3. Soal Evaluasi / Kuis Ketercapaian Literasi (Pilihan Ganda & Esai Pendek)
        Schema::create('literasi_soal', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('sesi_id');
            $table->integer('urutan')->default(1);
            $table->string('jenis_soal', 30)->default('pilihan_ganda'); // pilihan_ganda, esai_pendek
            $table->text('pertanyaan');
            $table->text('opsi_a')->nullable();
            $table->text('opsi_b')->nullable();
            $table->text('opsi_c')->nullable();
            $table->text('opsi_d')->nullable();
            $table->text('opsi_e')->nullable();
            $table->char('kunci_jawaban', 1)->nullable(); // A, B, C, D, E
            $table->integer('bobot_skor')->default(10);
            $table->text('pembahasan')->nullable();
            $table->timestamps();

            $table->foreign('sesi_id')->references('id')->on('literasi_sesi')->onDelete('cascade');
        });

        // 4. Presensi & Ketercapaian Siswa per Sesi
        Schema::create('literasi_presensi_siswa', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('sesi_id');
            $table->char('siswa_id', 36);
            $table->char('rombel_id', 36)->nullable();
            $table->timestamp('waktu_gabung')->nullable();
            $table->timestamp('waktu_submit')->nullable();
            $table->decimal('skor_pg', 5, 2)->default(0);
            $table->string('status_evaluasi', 30)->default('mengikuti'); // mengikuti, sudah_mengerjakan, dinilai
            $table->text('rangkuman_siswa')->nullable();
            $table->timestamps();

            $table->unique(['sesi_id', 'siswa_id']);
            $table->foreign('sesi_id')->references('id')->on('literasi_sesi')->onDelete('cascade');
            $table->foreign('siswa_id')->references('id')->on('siswa')->onDelete('cascade');
            $table->foreign('rombel_id')->references('id')->on('rombel')->onDelete('set null');
        });

        // 5. Jawaban Detail Siswa (Butir Soal & Esai Pendek)
        Schema::create('literasi_jawaban_siswa', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('sesi_id');
            $table->char('siswa_id', 36);
            $table->uuid('soal_id');
            $table->char('jawaban_pg', 1)->nullable();
            $table->text('jawaban_esai')->nullable();
            $table->boolean('is_benar')->nullable();
            $table->decimal('skor', 5, 2)->default(0);
            $table->text('catatan_koreksi')->nullable();
            $table->timestamps();

            $table->foreign('sesi_id')->references('id')->on('literasi_sesi')->onDelete('cascade');
            $table->foreign('siswa_id')->references('id')->on('siswa')->onDelete('cascade');
            $table->foreign('soal_id')->references('id')->on('literasi_soal')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('literasi_jawaban_siswa');
        Schema::dropIfExists('literasi_presensi_siswa');
        Schema::dropIfExists('literasi_soal');
        Schema::dropIfExists('literasi_slides');
        Schema::dropIfExists('literasi_sesi');
    }
};

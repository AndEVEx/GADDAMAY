<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tabel Master Materi Pembelajaran LMS (KBM Reguler, Ujikom Intensif, LKS Khusus)
        Schema::create('lms_materi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('judul', 150);
            $table->enum('kategori', ['kbm_reguler', 'ujikom_intensif', 'lks_khusus'])->default('kbm_reguler');
            $table->string('bidang_keahlian', 50)->default('PPLG'); // PPLG, Otomotif, TKJ, dll.
            $table->char('mapel_id', 36)->nullable(); // Terhubung ke mata pelajaran KBM
            $table->char('tp_id', 36)->nullable(); // Terhubung ke Tujuan Pembelajaran (TP)
            $table->char('guru_id', 36); // FK ke users (Guru Pengampu / Pembimbing Ujikom & LKS)
            $table->text('deskripsi')->nullable();
            $table->longText('konten_materi'); // Isi teks, panduan praktikum, job sheet
            $table->string('file_lampiran', 255)->nullable(); // PDF modul / Jobsheet
            $table->string('link_video', 255)->nullable(); // URL YouTube / Video tutorial
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->foreign('guru_id')->references('id')->on('users')->onDelete('cascade');
        });

        // 2. Tabel Penugasan Jalur Belajar Personal per Siswa (Diferensiasi Pembelajaran)
        Schema::create('lms_penugasan_siswa', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('materi_id');
            $table->char('siswa_id', 36);
            $table->enum('tipe_jalur', ['reguler', 'pengayaan', 'remedial', 'peserta_ujikom', 'peserta_lks'])->default('reguler');
            $table->date('target_selesai')->nullable();
            $table->enum('status_progres', ['belum_mulai', 'sedang_belajar', 'selesai'])->default('belum_mulai');
            $table->decimal('skor_evaluasi', 5, 2)->nullable();
            $table->text('catatan_guru')->nullable();
            $table->dateTime('selesai_at')->nullable();
            $table->timestamps();

            $table->foreign('materi_id')->references('id')->on('lms_materi')->onDelete('cascade');
            $table->foreign('siswa_id')->references('id')->on('siswa')->onDelete('cascade');
            $table->unique(['materi_id', 'siswa_id']);
        });

        // 3. Tabel Pengumpulan Proyek Latihan & Simulasi Ujikom / LKS Siswa
        Schema::create('lms_proyek_latihan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('penugasan_id');
            $table->char('siswa_id', 36);
            $table->uuid('materi_id');
            $table->string('link_repository_git', 255)->nullable(); // GitHub / GitLab URL
            $table->string('file_proyek', 255)->nullable(); // File zip / dokumen
            $table->text('deskripsi_pekerjaan');
            $table->enum('status_review', ['menunggu', 'disetujui', 'perlu_revisi'])->default('menunggu');
            $table->text('catatan_pembimbing')->nullable();
            $table->dateTime('dinilai_at')->nullable();
            $table->timestamps();

            $table->foreign('penugasan_id')->references('id')->on('lms_penugasan_siswa')->onDelete('cascade');
            $table->foreign('siswa_id')->references('id')->on('siswa')->onDelete('cascade');
            $table->foreign('materi_id')->references('id')->on('lms_materi')->onDelete('cascade');
        });

        // 4. Tabel Skema Sertifikasi Ujikom (LSP-P1 / UKK)
        Schema::create('ujikom_skema', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode_skema', 50)->unique(); // Misal: SKM-PPLG-001
            $table->string('nama_skema', 150); // Misal: Pemrograman Web & Perangkat Bergerak
            $table->string('jurusan', 50)->default('PPLG');
            $table->text('deskripsi')->nullable();
            $table->integer('jumlah_unit_kompetensi')->default(5);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 5. Tabel Pendaftaran Asesi Ujikom
        Schema::create('ujikom_pendaftaran', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nomor_pendaftaran', 40)->unique(); // Misal: UKK-2026-001
            $table->uuid('skema_id');
            $table->char('siswa_id', 36);
            $table->string('tahun_ajaran', 20)->default('2025/2026');
            $table->enum('status_verifikasi', ['draft', 'menunggu_verifikasi', 'lolos_administrasi', 'revisi_berkas'])->default('menunggu_verifikasi');
            $table->char('asesor_id', 36)->nullable(); // FK ke users
            $table->date('jadwal_asesmen')->nullable();
            $table->string('tempat_uji_kompetensi_tuk', 100)->default('Lab Komputer 1 SMKN 2 Indramayu');
            $table->enum('hasil_asesmen', ['belum_dinilai', 'kompeten', 'belum_kompeten'])->default('belum_dinilai');
            $table->text('catatan_asesor')->nullable();
            $table->timestamps();

            $table->foreign('skema_id')->references('id')->on('ujikom_skema')->onDelete('cascade');
            $table->foreign('siswa_id')->references('id')->on('siswa')->onDelete('cascade');
            $table->foreign('asesor_id')->references('id')->on('users')->onDelete('set null');
        });

        // 6. Tabel Berkas Pra-Asesmen & Portofolio Ujikom
        Schema::create('ujikom_berkas_asesmen', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('pendaftaran_id');
            $table->enum('jenis_berkas', ['apl_01_permohonan', 'apl_02_mandiri', 'portofolio_proyek', 'sertifikat_pkl', 'rapor'])->default('apl_01_permohonan');
            $table->string('nama_file', 255);
            $table->string('file_path', 255);
            $table->boolean('is_valid')->default(true);
            $table->timestamps();

            $table->foreign('pendaftaran_id')->references('id')->on('ujikom_pendaftaran')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ujikom_berkas_asesmen');
        Schema::dropIfExists('ujikom_pendaftaran');
        Schema::dropIfExists('ujikom_skema');
        Schema::dropIfExists('lms_proyek_latihan');
        Schema::dropIfExists('lms_penugasan_siswa');
        Schema::dropIfExists('lms_materi');
    }
};
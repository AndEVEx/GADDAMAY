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
        // 1. Tabel Pesanan / Order Proyek TEFA Konsumen
        Schema::create('tefa_order', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode_order', 30)->unique(); // Misal: TEFA-202609-001
            $table->string('nama_pemesan', 100);
            $table->string('nomor_wa_pemesan', 25);
            $table->string('instansi_pemesan', 150)->nullable();
            $table->string('judul_proyek', 150);
            $table->string('kategori_kejuruan', 50)->default('PPLG'); // PPLG, Otomotif, TKJ, dll.
            $table->decimal('biaya_proyek', 12, 2)->default(0);
            $table->date('tanggal_masuk');
            $table->date('target_selesai');
            $table->date('tanggal_selesai_aktual')->nullable();
            $table->enum('status', ['menunggu', 'dalam_produksi', 'qc_inspeksi', 'selesai_diserahkan', 'dibatalkan'])->default('menunggu');
            $table->char('instruktur_id', 36); // FK ke users (Guru / Instruktur TEFA)
            $table->text('deskripsi_proyek')->nullable();
            $table->text('catatan_proyek')->nullable();
            $table->timestamps();

            $table->foreign('instruktur_id')->references('id')->on('users')->onDelete('cascade');
        });

        // 2. Tabel Tim Kerja & Job Sheet Siswa Pelaksana
        Schema::create('tefa_tim_kerja', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tefa_order_id');
            $table->char('siswa_id', 36);
            $table->enum('peran_dalam_tim', ['project_manager', 'teknisi_utama', 'quality_tester', 'operator', 'asisten'])->default('teknisi_utama');
            $table->text('job_desc')->nullable(); // Tugas spesifik yang harus diselesaikan
            $table->enum('status_tim', ['aktif', 'selesai'])->default('aktif');
            $table->timestamps();

            $table->foreign('tefa_order_id')->references('id')->on('tefa_order')->onDelete('cascade');
            $table->foreign('siswa_id')->references('id')->on('siswa')->onDelete('cascade');
        });

        // 3. Tabel Logsheet Jam Kerja Produksi Siswa
        Schema::create('tefa_log_produksi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tefa_order_id');
            $table->char('siswa_id', 36);
            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->unsignedInteger('durasi_menit')->default(0);
            $table->text('ringkasan_pekerjaan');
            $table->text('alat_dan_bahan')->nullable();
            $table->string('foto_progres', 255)->nullable();
            $table->enum('status_qc', ['menunggu_qc', 'lolos_qc', 'perlu_revisi'])->default('menunggu_qc');
            $table->text('catatan_instruktur')->nullable();
            $table->char('diperiksa_oleh_id', 36)->nullable();
            $table->dateTime('diperiksa_at')->nullable();
            $table->timestamps();

            $table->foreign('tefa_order_id')->references('id')->on('tefa_order')->onDelete('cascade');
            $table->foreign('siswa_id')->references('id')->on('siswa')->onDelete('cascade');
            $table->foreign('diperiksa_oleh_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tefa_log_produksi');
        Schema::dropIfExists('tefa_tim_kerja');
        Schema::dropIfExists('tefa_order');
    }
};
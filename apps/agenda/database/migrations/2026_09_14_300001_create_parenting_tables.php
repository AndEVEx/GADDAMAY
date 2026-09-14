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
        // 1. Tabel Sesi OTP & Magic Link Akses Orang Tua (Bebas Password)
        Schema::create('parenting_sesi_otp', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->char('siswa_id', 36);
            $table->string('nomor_wa_ortu', 25);
            $table->string('otp_code', 6)->nullable();
            $table->string('magic_token', 64)->unique();
            $table->dateTime('expires_at');
            $table->boolean('is_used')->default(false);
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamps();

            $table->foreign('siswa_id')->references('id')->on('siswa')->onDelete('cascade');
        });

        // 2. Tabel Buku Kedisiplinan & Prestasi Siswa (Bimbingan Konseling & Wali Kelas)
        Schema::create('parenting_catatan_disiplin', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->char('siswa_id', 36);
            $table->enum('kategori', ['pelanggaran', 'pembinaan_bk', 'prestasi', 'apresiasi'])->default('pelanggaran');
            $table->string('jenis_tindakan', 150); // Contoh: Keterlambatan, Seragam, Juara Lomba, Sikap Positif
            $table->integer('poin')->default(0); // Positif untuk prestasi (+10), Negatif untuk pelanggaran (-5)
            $table->text('deskripsi');
            $table->char('petugas_id', 36); // FK ke users (Guru BK / Wali Kelas / Guru Piket)
            $table->string('peran_petugas', 50)->default('guru_bk'); // guru_bk / wali_kelas / guru_piket
            $table->string('foto_bukti', 255)->nullable();
            $table->date('tanggal_kejadian');
            $table->enum('notif_wa_ortu_status', ['pending', 'sent_auto', 'sent_manual', 'skipped'])->default('pending');
            $table->dateTime('wa_sent_at')->nullable();
            $table->timestamps();

            $table->foreign('siswa_id')->references('id')->on('siswa')->onDelete('cascade');
            $table->foreign('petugas_id')->references('id')->on('users')->onDelete('cascade');
        });

        // 3. Tabel Konsultasi Online Orang Tua ke Sekolah
        Schema::create('parenting_konsultasi_ortu', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->char('siswa_id', 36);
            $table->string('nama_ortu', 100);
            $table->string('nomor_wa_ortu', 25);
            $table->string('topik_konsultasi', 150);
            $table->text('pesan');
            $table->enum('tujuan', ['wali_kelas', 'guru_bk', 'guru_piket'])->default('wali_kelas');
            $table->enum('status', ['diajukan', 'direspon', 'selesai'])->default('diajukan');
            $table->text('tanggapan_sekolah')->nullable();
            $table->char('ditanggapi_oleh_id', 36)->nullable();
            $table->dateTime('ditanggapi_at')->nullable();
            $table->timestamps();

            $table->foreign('siswa_id')->references('id')->on('siswa')->onDelete('cascade');
            $table->foreign('ditanggapi_oleh_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parenting_konsultasi_ortu');
        Schema::dropIfExists('parenting_catatan_disiplin');
        Schema::dropIfExists('parenting_sesi_otp');
    }
};
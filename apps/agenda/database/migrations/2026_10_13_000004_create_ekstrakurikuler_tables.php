<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Master Ekstrakurikuler
        Schema::create('ekskul', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_ekskul');
            $table->string('slug')->unique();
            $table->string('kategori'); // olahraga, bela_negara_ketarunaan, seni_budaya, keagamaan, sains_teknologi, kebahasaan, sosial_pmi
            $table->text('deskripsi')->nullable();
            $table->string('jadwal_rutin')->nullable(); // misal: "Sabtu, 08.00 - 11.00 WIB"
            $table->string('tempat_latihan')->nullable(); // misal: "Lapangan Utama / Lab Komputer"
            $table->char('pembina_guru_id', 36)->nullable(); // Guru pembina
            $table->string('pelatih_nama')->nullable(); // Nama pelatih / instruktur
            $table->char('pelatih_user_id', 36)->nullable(); // User login pelatih jika ada
            $table->string('kontak_pelatih', 30)->nullable();
            $table->string('logo_ikon', 50)->default('bi-trophy');
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();

            $table->foreign('pembina_guru_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('pelatih_user_id')->references('id')->on('users')->onDelete('set null');
        });

        // 2. Anggota Ekskul (Siswa Terdaftar)
        Schema::create('ekskul_anggota', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ekskul_id');
            $table->char('siswa_id', 36);
            $table->enum('jabatan_anggota', ['anggota', 'ketua', 'wakil_ketua', 'sekretaris', 'bendahara'])->default('anggota');
            $table->string('tahun_ajaran', 20)->default('2026/2027');
            $table->string('nomor_anggota')->nullable();
            $table->enum('status_keanggotaan', ['aktif', 'non_aktif', 'alumni'])->default('aktif');
            $table->string('nilai_predikat', 20)->default('A (Sangat Baik)');
            $table->timestamps();

            $table->foreign('ekskul_id')->references('id')->on('ekskul')->onDelete('cascade');
            $table->foreign('siswa_id')->references('id')->on('siswa')->onDelete('cascade');
            $table->unique(['ekskul_id', 'siswa_id', 'tahun_ajaran']);
        });

        // 3. Sesi Pertemuan / Latihan Ekskul
        Schema::create('ekskul_pertemuan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ekskul_id');
            $table->date('tanggal_pertemuan');
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->string('topik_latihan');
            $table->text('deskripsi_kegiatan')->nullable();
            $table->char('pencatat_user_id', 36)->nullable(); // Pembina atau Pelatih yang memimpin sesi
            $table->enum('status_pertemuan', ['dibuka', 'selesai'])->default('selesai');
            $table->string('foto_kegiatan')->nullable();
            $table->timestamps();

            $table->foreign('ekskul_id')->references('id')->on('ekskul')->onDelete('cascade');
            $table->foreign('pencatat_user_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['ekskul_id', 'tanggal_pertemuan']);
        });

        // 4. Presensi / Absensi Anggota Ekskul
        Schema::create('ekskul_presensi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ekskul_pertemuan_id');
            $table->uuid('ekskul_anggota_id');
            $table->char('siswa_id', 36);
            $table->enum('status', ['H', 'S', 'I', 'A'])->default('H'); // Hadir, Sakit, Izin, Alpa
            $table->enum('metode_presensi', ['scan_qr', 'input_manual'])->default('input_manual');
            $table->timestamp('waktu_scan')->nullable();
            $table->string('catatan')->nullable();
            $table->timestamps();

            $table->foreign('ekskul_pertemuan_id')->references('id')->on('ekskul_pertemuan')->onDelete('cascade');
            $table->foreign('ekskul_anggota_id')->references('id')->on('ekskul_anggota')->onDelete('cascade');
            $table->foreign('siswa_id')->references('id')->on('siswa')->onDelete('cascade');
            $table->unique(['ekskul_pertemuan_id', 'siswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ekskul_presensi');
        Schema::dropIfExists('ekskul_pertemuan');
        Schema::dropIfExists('ekskul_anggota');
        Schema::dropIfExists('ekskul');
    }
};

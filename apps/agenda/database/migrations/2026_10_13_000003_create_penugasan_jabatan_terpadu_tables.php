<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Kamus Master Jabatan & Peran Modul (Guru & Siswa)
        Schema::create('master_jabatan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode_jabatan', 60)->unique();
            $table->string('nama_jabatan');
            $table->enum('kategori', ['guru', 'siswa', 'gabungan'])->default('guru');
            $table->tinyInteger('level_hierarki')->default(2); // 1 = Koordinator/Pimpinan, 2 = Pengurus/Staf, 3 = Pelaksana/Delegasi
            $table->text('deskripsi_tugas')->nullable();
            $table->string('delegasi_modul')->nullable(); // Nama rute atau grup modul: misal "perduptar,literasi,perpus"
            $table->string('hak_akses')->default('operator'); // admin, operator, verifikator, viewer
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });

        // 2. Transaksi Penugasan Jabatan (Guru & Siswa)
        Schema::create('penugasan_jabatan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('master_jabatan_id');
            $table->enum('tipe_subjek', ['guru', 'siswa']);
            $table->char('user_id', 36)->nullable(); // Jika guru/pegawai
            $table->char('siswa_id', 36)->nullable(); // Jika siswa
            $table->char('rombel_id', 36)->nullable(); // Khusus Wali Kelas, Danton, Koordinator Jurusan
            $table->string('tahun_ajaran', 20)->default('2026/2027');
            $table->string('nomor_sk')->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->boolean('status_aktif')->default(true);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('master_jabatan_id')->references('id')->on('master_jabatan')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('siswa_id')->references('id')->on('siswa')->onDelete('cascade');
            $table->foreign('rombel_id')->references('id')->on('rombel')->onDelete('set null');
            $table->index(['tipe_subjek', 'status_aktif', 'tahun_ajaran']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penugasan_jabatan');
        Schema::dropIfExists('master_jabatan');
    }
};

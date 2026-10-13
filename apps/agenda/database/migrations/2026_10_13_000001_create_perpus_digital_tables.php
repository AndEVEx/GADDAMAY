<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Kategori Buku Perpustakaan
        Schema::create('perpus_kategori', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_kategori');
            $table->string('slug')->unique();
            $table->string('ikon', 50)->default('bi-book');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // 2. Katalog Koleksi Buku PDF Digital
        Schema::create('perpus_buku', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('kategori_id');
            $table->string('judul');
            $table->string('slug')->unique();
            $table->string('penulis');
            $table->string('penerbit')->nullable();
            $table->string('tahun_terbit', 10)->nullable();
            $table->string('isbn', 50)->nullable();
            $table->integer('jumlah_halaman')->default(1);
            $table->text('sinopsis')->nullable();
            $table->string('cover_url')->nullable();
            $table->string('file_pdf_url');
            $table->decimal('file_size_mb', 6, 2)->default(0);
            $table->boolean('allow_offline_cache')->default(true);
            $table->integer('total_dibaca')->default(0);
            $table->boolean('is_published')->default(true);
            $table->char('created_by', 36)->nullable();
            $table->timestamps();

            $table->foreign('kategori_id')->references('id')->on('perpus_kategori')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        // 3. Rekam Riwayat Membaca & Waktu Baca Pengguna
        Schema::create('perpus_riwayat_baca', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('buku_id');
            $table->char('user_id', 36);
            $table->char('siswa_id', 36)->nullable();
            $table->char('rombel_id', 36)->nullable();
            $table->integer('halaman_terakhir')->default(1);
            $table->decimal('persentase_baca', 5, 2)->default(0);
            $table->integer('durasi_detik')->default(0);
            $table->timestamp('waktu_mulai_baca')->nullable();
            $table->timestamp('waktu_terakhir_baca')->nullable();
            $table->boolean('status_selesai')->default(false);
            $table->boolean('is_offline_synced')->default(false);
            $table->text('catatan_baca')->nullable();
            $table->timestamps();

            $table->foreign('buku_id')->references('id')->on('perpus_buku')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('siswa_id')->references('id')->on('siswa')->onDelete('set null');
            $table->foreign('rombel_id')->references('id')->on('rombel')->onDelete('set null');
            $table->index(['user_id', 'buku_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perpus_riwayat_baca');
        Schema::dropIfExists('perpus_buku');
        Schema::dropIfExists('perpus_kategori');
    }
};

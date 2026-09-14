<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perizinan_siswa', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->enum('kategori', ['sakit', 'izin_keperluan', 'dispensasi_sekolah', 'izin_keluar_kampus'])->default('sakit');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->text('alasan');
            $table->string('file_lampiran')->nullable();
            $table->string('nama_pemohon')->nullable();
            $table->string('nomor_wa_pemohon', 30)->nullable();
            $table->string('hubungan_pemohon', 50)->default('orang_tua');
            $table->enum('status', ['menunggu', 'disetujui_walas', 'disetujui_piket', 'disetujui_bk', 'ditolak'])->default('menunggu');
            $table->foreignUuid('diverifikasi_oleh_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('peran_verifikator', 50)->nullable();
            $table->text('catatan_verifikator')->nullable();
            $table->dateTime('waktu_verifikasi')->nullable();
            $table->boolean('auto_locked_agenda')->default(false);
            $table->boolean('sync_gate_status')->default(false);
            $table->string('wa_notif_status', 30)->default('pending'); // pending, sent_auto, sent_manual, failed, skipped
            $table->text('wa_notif_text')->nullable();
            $table->dateTime('wa_sent_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perizinan_siswa');
    }
};
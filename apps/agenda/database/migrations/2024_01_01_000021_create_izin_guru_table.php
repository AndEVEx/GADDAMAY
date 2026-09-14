<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('izin_guru', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('guru_id')->constrained('users')->cascadeOnDelete();
            $table->enum('jenis_izin', ['izin', 'sakit', 'cuti', 'dinas', 'tugas_luar'])->default('izin');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->text('alasan');
            $table->string('file_lampiran')->nullable();
            $table->foreignUuid('guru_pengganti_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->foreignUuid('diverifikasi_oleh_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('catatan_waka')->nullable();
            $table->dateTime('waktu_verifikasi')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['guru_id', 'status']);
            $table->index(['tanggal_mulai', 'tanggal_selesai']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('izin_guru');
    }
};

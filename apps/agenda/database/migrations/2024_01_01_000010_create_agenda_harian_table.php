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
        Schema::create('agenda_harian', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('jadwal_pelajaran_id')->constrained('jadwal_pelajaran')->cascadeOnDelete();
            $table->foreignUuid('guru_id')->constrained('users')->cascadeOnDelete();
            $table->date('tanggal');
            $table->text('materi_diajarkan')->nullable();
            $table->string('token_handshake', 6)->nullable();
            $table->dateTime('waktu_mulai')->nullable();
            $table->dateTime('waktu_selesai')->nullable();
            $table->enum('status', ['menunggu_token', 'berjalan', 'selesai', 'dibatalkan'])->default('menunggu_token');
            $table->string('foto_bukti_path')->nullable();
            $table->text('prompter_custom')->nullable();
            $table->enum('status_kehadiran_guru', ['hadir', 'izin', 'cuti', 'sakit'])->default('hadir');
            $table->foreignUuid('guru_pengganti_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('koreksi_oleh_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['tanggal', 'guru_id']);
            $table->index(['tanggal', 'jadwal_pelajaran_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agenda_harian');
    }
};

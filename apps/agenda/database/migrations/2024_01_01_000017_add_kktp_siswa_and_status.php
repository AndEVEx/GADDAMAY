<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add 'token_terverifikasi' to agenda_harian status enum
        DB::statement("ALTER TABLE `agenda_harian` MODIFY COLUMN `status` ENUM('menunggu_token', 'token_terverifikasi', 'berjalan', 'selesai', 'dibatalkan') NOT NULL DEFAULT 'menunggu_token'");

        // 2. Create kktp_siswa table
        Schema::create('kktp_siswa', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('agenda_harian_id')->constrained('agenda_harian')->cascadeOnDelete();
            $table->foreignUuid('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->foreignUuid('tp_id')->constrained('tujuan_pembelajaran')->cascadeOnDelete();
            $table->enum('status', ['tercapai', 'belum_tercapai'])->default('belum_tercapai');
            $table->timestamps();
            $table->unique(['agenda_harian_id', 'siswa_id', 'tp_id'], 'kktp_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kktp_siswa');
        DB::statement("ALTER TABLE `agenda_harian` MODIFY COLUMN `status` ENUM('menunggu_token', 'berjalan', 'selesai', 'dibatalkan') NOT NULL DEFAULT 'menunggu_token'");
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nilai_kktp', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->foreignUuid('tp_id')->constrained('tujuan_pembelajaran')->cascadeOnDelete();
            $table->foreignUuid('rombel_id')->constrained('rombel')->cascadeOnDelete();
            $table->foreignUuid('guru_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['tercapai', 'belum_tercapai'])->default('tercapai');
            $table->timestamps();

            $table->unique(['siswa_id', 'tp_id', 'rombel_id'], 'nilai_kktp_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai_kktp');
    }
};

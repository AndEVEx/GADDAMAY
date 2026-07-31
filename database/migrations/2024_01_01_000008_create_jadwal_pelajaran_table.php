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
        Schema::create('jadwal_pelajaran', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('hari'); // 1-5 (Senin-Jumat)
            $table->integer('jam_ke_mulai');
            $table->integer('jam_ke_selesai');
            $table->foreignUuid('rombel_id')->nullable()->constrained('rombel')->cascadeOnDelete();
            $table->foreignUuid('mapel_id')->nullable()->constrained('mata_pelajaran')->cascadeOnDelete();
            $table->text('keterangan')->nullable(); // for non-guru entries like LPK, TIM
            $table->text('kegiatan_khusus')->nullable(); // for non-mapel like UPACARA, ISTIRAHAT, PKL, Ujikom
            $table->string('asc_lesson_id')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_pelajaran');
    }
};

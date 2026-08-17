<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hari_libur', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_hari_libur');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->enum('tipe_libur', ['nasional', 'sekolah', 'cuti_bersama', 'khusus'])->default('nasional');
            $table->text('keterangan')->nullable();
            $table->foreignUuid('created_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tanggal_mulai', 'tanggal_selesai']);
            $table->index('tipe_libur');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hari_libur');
    }
};

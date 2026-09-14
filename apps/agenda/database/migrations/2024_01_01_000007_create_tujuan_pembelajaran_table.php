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
        Schema::create('tujuan_pembelajaran', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('mapel_id')->constrained('mata_pelajaran')->cascadeOnDelete();
            $table->string('kode_tp'); // TP-01
            $table->text('deskripsi_tp');
            $table->integer('order_sequence')->default(0);
            $table->foreignUuid('ketua_mgmp_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tujuan_pembelajaran');
    }
};

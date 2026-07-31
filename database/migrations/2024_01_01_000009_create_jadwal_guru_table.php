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
        Schema::create('jadwal_guru', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('jadwal_pelajaran_id')->constrained('jadwal_pelajaran')->cascadeOnDelete();
            $table->foreignUuid('guru_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['jadwal_pelajaran_id', 'guru_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_guru');
    }
};

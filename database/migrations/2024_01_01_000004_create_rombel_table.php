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
        Schema::create('rombel', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_kelas'); // X TKJ 1
            $table->integer('tingkat'); // 10, 11, 12
            $table->string('asc_id')->nullable()->index(); // mapping from XML
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rombel');
    }
};

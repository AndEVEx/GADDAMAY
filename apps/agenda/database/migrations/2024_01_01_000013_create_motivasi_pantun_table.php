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
        Schema::create('motivasi_pantun', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('isi');
            $table->enum('tipe', ['pantun', 'kata_mutiara']);
            $table->enum('kategori', ['sebelum_mengajar', 'siap_mengajar']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('motivasi_pantun');
    }
};

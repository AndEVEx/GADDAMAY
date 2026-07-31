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
        Schema::create('agenda_tp', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('agenda_harian_id')->constrained('agenda_harian')->cascadeOnDelete();
            $table->foreignUuid('tp_id')->constrained('tujuan_pembelajaran')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agenda_tp');
    }
};

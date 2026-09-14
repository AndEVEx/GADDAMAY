<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_swap_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('batch_id')->index();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('swap_type')->default('custom_pair'); // 'all_vocational', 'custom_pair'
            $table->foreignUuid('rombel_a_id')->nullable()->constrained('rombel')->nullOnDelete();
            $table->foreignUuid('rombel_b_id')->nullable()->constrained('rombel')->nullOnDelete();
            $table->string('rombel_a_nama');
            $table->string('rombel_b_nama');
            $table->integer('schedules_count_a')->default(0);
            $table->integer('schedules_count_b')->default(0);
            $table->json('details')->nullable();
            $table->string('status')->default('active'); // 'active', 'undone'
            $table->timestamp('undone_at')->nullable();
            $table->foreignUuid('undone_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_swap_logs');
    }
};

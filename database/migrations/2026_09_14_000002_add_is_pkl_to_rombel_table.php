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
        Schema::table('rombel', function (Blueprint $table) {
            $table->boolean('is_pkl')->default(false)->after('asc_id');
            $table->string('pkl_keterangan')->nullable()->after('is_pkl');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rombel', function (Blueprint $table) {
            $table->dropColumn(['is_pkl', 'pkl_keterangan']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tujuan_pembelajaran', function (Blueprint $table) {
            $table->integer('tingkat')->nullable()->after('mapel_id'); // 10, 11, 12
            $table->index(['mapel_id', 'tingkat', 'ketua_mgmp_id'], 'tp_mapel_tingkat_guru_idx');
        });
    }

    public function down(): void
    {
        Schema::table('tujuan_pembelajaran', function (Blueprint $table) {
            $table->dropIndex('tp_mapel_tingkat_guru_idx');
            $table->dropColumn('tingkat');
        });
    }
};

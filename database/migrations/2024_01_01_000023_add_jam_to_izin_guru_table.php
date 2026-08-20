<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('izin_guru', function (Blueprint $table) {
            $table->boolean('is_seharian')->default(true)->after('jenis_izin');
            $table->string('waktu_keterangan')->nullable()->after('is_seharian');
            $table->text('jam_terpilih')->nullable()->after('waktu_keterangan');
            $table->text('jadwal_ids')->nullable()->after('jam_terpilih');
        });
    }

    public function down(): void
    {
        Schema::table('izin_guru', function (Blueprint $table) {
            $table->dropColumn(['is_seharian', 'waktu_keterangan', 'jam_terpilih', 'jadwal_ids']);
        });
    }
};

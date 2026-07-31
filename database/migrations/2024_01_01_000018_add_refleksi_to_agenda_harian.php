<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agenda_harian', function (Blueprint $table) {
            $table->text('refleksi')->nullable()->after('prompter_custom');
        });
    }
    public function down(): void
    {
        Schema::table('agenda_harian', function (Blueprint $table) {
            $table->dropColumn('refleksi');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Alter users role column to VARCHAR(50) to support guru_piket and future roles cleanly
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 50)->default('guru')->change();
        });
    }

    public function down(): void
    {
        // Revert back if needed
    }
};
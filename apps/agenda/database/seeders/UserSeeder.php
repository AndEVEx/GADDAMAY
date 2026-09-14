<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\TahunPelajaran;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin users
        $admins = [
            ['name' => 'Administrator', 'email' => 'admin@smkn2indramayu.sch.id', 'role' => 'admin'],
            ['name' => 'Kepala Sekolah', 'email' => 'kepsek@smkn2indramayu.sch.id', 'role' => 'kepsek'],
            ['name' => 'Waka Kurikulum', 'email' => 'waka@smkn2indramayu.sch.id', 'role' => 'waka'],
            ['name' => 'Ketua MGMP Matematika', 'email' => 'mgmp.mtk@smkn2indramayu.sch.id', 'role' => 'ketua_mgmp'],
            ['name' => 'Ketua MGMP B. Indonesia', 'email' => 'mgmp.bind@smkn2indramayu.sch.id', 'role' => 'ketua_mgmp'],
        ];

        foreach ($admins as $admin) {
            User::firstOrCreate(
                ['email' => $admin['email']],
                array_merge($admin, ['password' => 'password123'])
            );
        }

        // Create active academic year
        TahunPelajaran::firstOrCreate(
            ['nama' => '2024/2025', 'semester' => 'genap'],
            ['is_active' => true]
        );
    }
}

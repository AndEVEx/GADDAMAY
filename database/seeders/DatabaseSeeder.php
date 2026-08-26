<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            XmlJadwalSeeder::class,
            SiswaSeeder::class,
            MotivasiPantunSeeder::class,
            LpkUserSeeder::class,
        ]);
    }
}

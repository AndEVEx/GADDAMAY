<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LpkUserSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password123');

        $users = [
            [
                'name' => 'LPK Bahasa Jepang 1',
                'email' => 'lpkjepang1@smkn2indramayu.sch.id',
                'role' => 'guru',
            ],
            [
                'name' => 'LPK Bahasa Jepang 2',
                'email' => 'lpkjepang2@smkn2indramayu.sch.id',
                'role' => 'guru',
            ],
            [
                'name' => 'LPK Bahasa Jepang 3',
                'email' => 'lpkjepang3@smkn2indramayu.sch.id',
                'role' => 'guru',
            ],
            [
                'name' => 'LPK Bahasa Korea 1',
                'email' => 'lpkkorea1@smkn2indramayu.sch.id',
                'role' => 'guru',
            ],
            [
                'name' => 'LPK Bahasa Korea 2',
                'email' => 'lpkkorea2@smkn2indramayu.sch.id',
                'role' => 'guru',
            ],
            [
                'name' => 'LPK Bahasa Korea 3',
                'email' => 'lpkkorea3@smkn2indramayu.sch.id',
                'role' => 'guru',
            ],
            // Also alias exact emails mentioned by user in case of specific typing
            [
                'name' => 'LPK Korea 1 (Jepang)',
                'email' => 'lpkkorea1lpkjepang1@smkn2indramayu.sch.id',
                'role' => 'guru',
            ],
            [
                'name' => 'LPK Korea 2 (Jepang)',
                'email' => 'lpkkorea2lpkjepang1@smkn2indramayu.sch.id',
                'role' => 'guru',
            ],
            [
                'name' => 'LPK Korea 3 (Jepang)',
                'email' => 'lpkkorea3lpkjepang1@smkn2indramayu.sch.id',
                'role' => 'guru',
            ],
        ];

        foreach ($users as $u) {
            User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'password' => $password,
                    'role' => $u['role'],
                ]
            );
        }
    }
}

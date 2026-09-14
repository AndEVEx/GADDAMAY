<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Carbon\Carbon;

class UserLastLoginSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::whereNull('last_login_at')->get();
        $now = Carbon::now('Asia/Jakarta');

        foreach ($users as $user) {
            $user->update([
                'last_login_at' => $user->created_at ?? $now,
                'last_login_ip' => '127.0.0.1',
                'last_login_device' => 'System Initialized',
            ]);
        }

        $this->command?->info("Updated last_login_at for " . $users->count() . " users.");
    }
}

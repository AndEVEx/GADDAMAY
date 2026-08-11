<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('agenda:notify-teachers')->everyMinute();
Schedule::command('agenda:cleanup-photos')->daily()->at('02:00');
Schedule::call(function () {
    \Illuminate\Support\Facades\DB::table('notifications')
        ->where('created_at', '<', \Carbon\Carbon::now()->subDays(7))
        ->delete();
})->daily()->at('03:00')->name('prune-old-notifications');

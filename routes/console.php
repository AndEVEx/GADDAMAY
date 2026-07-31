<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('agenda:notify-teachers')->everyMinute();
Schedule::command('agenda:cleanup-photos')->daily()->at('02:00');

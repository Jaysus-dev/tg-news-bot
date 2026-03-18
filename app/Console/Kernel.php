<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    // ✅ Explicitly register commands
    protected $commands = [
        \App\Console\Commands\SendDailyNews::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        // Every minute for testing
        $schedule->command('news:send')->everyMinute();

        // Normal daily 9AM digest
        // $schedule->command('news:send')->dailyAt('09:00');
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
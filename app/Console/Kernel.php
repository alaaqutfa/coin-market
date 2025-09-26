<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // كل يوم الساعة 10 مساءً بتوقيت بيروت
        $schedule->command('attendance:calculate-daily-hours')
            ->timezone('Asia/Beirut')
            ->dailyAt('22:00')
            ->withoutOverlapping();

        // تنظيف الإشعارات المنتهية كل دقيقة
        // $schedule->command('notifications:cleanup')->everyMinute()->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        // كمان لازم يستدعي routes/console.php
        require base_path('routes/console.php');
    }
}

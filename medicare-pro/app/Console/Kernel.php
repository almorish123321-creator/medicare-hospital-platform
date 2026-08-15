<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * All scheduled tasks are registered here. The Laravel scheduler
     * evaluates them once per minute when `php artisan schedule:run`
     * is invoked (typically via cron).
     */
    protected function schedule(Schedule $schedule): void
    {
        // Reset daily queues at midnight
        $schedule->command('queues:reset-daily')
            ->dailyAt('00:00')
            ->timezone(config('app.timezone', 'UTC'))
            ->onOneServer();

        // Send appointment reminders at 8 AM for next-day appointments
        $schedule->command('appointments:send-reminders')
            ->dailyAt('08:00')
            ->timezone(config('app.timezone', 'UTC'))
            ->onOneServer();

        // Check expired medications daily at 1 AM
        $schedule->command('medications:update-expired')
            ->dailyAt('01:00')
            ->timezone(config('app.timezone', 'UTC'))
            ->onOneServer();

        // Clean old notifications weekly on Sunday at 3 AM (retain 30 days)
        $schedule->command('notifications:clean --days=30')
            ->weeklyOn(0, '03:00')
            ->timezone(config('app.timezone', 'UTC'))
            ->onOneServer();

        // Deep clean old activity logs monthly on the 1st at 3 AM (retain 90 days)
        $schedule->command('notifications:clean --days=90')
            ->monthlyOn(1, '03:00')
            ->timezone(config('app.timezone', 'UTC'))
            ->onOneServer();
    }

    /**
     * Register the commands for the application.
     *
     * This method loads all commands from the app/Console/Commands
     * directory and also includes the routes/console.php file which
     * may define additional closure-based commands.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

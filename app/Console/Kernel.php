<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\RecalculateInvoicePenalties::class,
        \App\Console\Commands\SendNoteReminders::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // بازمحاسبه شبانه جریمه‌ها — هر شب ساعت ۰۰:۳۰
        $schedule->command('invoices:recalculate-penalties')->dailyAt('00:30');

        // ارسال یادآوری یادداشت‌ها — هر روز ساعت ۰۸:۰۰
        $schedule->command('notes:send-reminders')->dailyAt('08:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

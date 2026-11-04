<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

// 👇 import your command class
use App\Console\Commands\GenerateMonthlyInvoices;

class Kernel extends ConsoleKernel
{
    /**
     * Register the commands for the application.
     */
    protected $commands = [
        GenerateMonthlyInvoices::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Adjust day/time as needed — e.g. 31st at 23:00
      $schedule->command('invoices:generate-monthly')
        ->dailyAt('00:00')
        ->when(function () {
            return now()->isSameDay(now()->copy()->endOfMonth()); // ✅ Only on last day
        });
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}

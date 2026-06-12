<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Generate tagihan otomatis H-7 (jam 00.00 dini hari)
        $schedule->command('reoda:generate-invoices')->dailyAt('00:00');

        // Kirim email/notifikasi pengingat tagihan jatuh tempo (jam 08.00 pagi)
        $schedule->command('reoda:send-payment-reminders')->dailyAt('08:00');

        // Hentikan kontrak kos yang melebihi batas toleransi (jam 00.01 dini hari)
        $schedule->command('reoda:terminate-expired-kos')->dailyAt('00:01');

        // Backup dari perintah lama (backward compat)
        // $schedule->command('contracts:process-expired')->daily();
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

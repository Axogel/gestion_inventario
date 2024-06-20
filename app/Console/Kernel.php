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
        // $schedule->command('app:scraping')->twiceDaily(0, 12);
        // $schedule->command('app:update-ves')->twiceDaily(0, 12);
        $schedule->command('app:happy-b')->twiceDaily(0, 12);
        $schedule->command('verificar:productos_vencidos')->twiceDaily(0, 12);

        // $schedule->command('inspire')->hourly();
    }
    

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

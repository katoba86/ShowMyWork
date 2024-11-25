<?php

namespace App\Console;

use App\Actions\Console\ReadinUrl;
use App\Actions\Main\AddToPage;
use App\Actions\Main\AssignImage;
use App\Actions\Main\SetImage;
use App\Modules\Scan\ScanCommand;
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


        ReadinUrl::class,
        AddToPage::class,
        ScanCommand::class,
        AssignImage::class,
        SetImage::class,





    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {

    }
}

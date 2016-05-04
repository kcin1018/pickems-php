<?php

namespace Pickems\Console;

use Pickems\Console\Commands\Init;
use Pickems\Console\Commands\Populate;
use Pickems\Console\Commands\DumpPlayers;
use Illuminate\Console\Scheduling\Schedule;
use Pickems\Console\Commands\UpdatePlayerGis;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        DumpPlayers::class,
        Init::class,
        Populate::class,
        UpdatePlayerGis::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     */
    protected function schedule(Schedule $schedule)
    {
    }
}

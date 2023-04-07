<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Monitor;
use App\Http\Controllers\MonitorController;

class PingCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ping:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ping all monitors';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Foreach monitor in the database launch ping function
        foreach (Monitor::all() as $monitor) {
            MonitorController::ping($monitor);
        }
    }
}

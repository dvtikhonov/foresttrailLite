<?php

namespace App\Jobs;

use App\Interfaces\GenerateDeviceReportInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class GenerateDeviceReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @param GenerateDeviceReportInterface $generateDeviceReport
     * @return void
     */
    public function handle(GenerateDeviceReportInterface $generateDeviceReport)
    {
        $generateDeviceReport->start();
    }
}

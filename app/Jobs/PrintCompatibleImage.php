<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Browsershot\Browsershot;
use Log;

class PrintCompatibleImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $url;
    public $fullFilePath;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($url, $fullFilePath)
    {
        $this->url = $url;
        $this->fullFilePath = $fullFilePath;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Browsershot::url($this->url)
                ->userAgent('Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Mobile Safari/537.36')
                ->touch()
                ->fullPage()
                ->noSandbox()
                ->setDelay(100)
                ->save($this->fullFilePath);
        } catch (Exception $e) {
            Log::error($e);
        }
    }
}

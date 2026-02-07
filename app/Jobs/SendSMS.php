<?php

namespace App\Jobs;


use Ipe\Sdk\Facades\SmsIr;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendSMS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $mobile;
    protected $templateId;
    protected $parameters;
    public function __construct($mobile, $templateId, $parameters)
    {
        $this->mobile = $mobile;
        $this->templateId = $templateId;
        $this->parameters = $parameters;
    }

    /**
     * Execute the job.
     */
    public function handle(): void

    {
        Log::info('start job send SMS');
        SmsIr::verifySend($this->mobile, $this->templateId, $this->parameters);
    }
}

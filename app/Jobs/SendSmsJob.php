<?php

namespace App\Jobs;

use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $to;
    public $message;

    public function __construct($to, $message)
    {
        $this->to = $to;
        $this->message = $message;
    }

    public function handle(SmsService $sms)
    {
        // Send SMS using service
        $sms->send($this->to, $this->message);
    }
}

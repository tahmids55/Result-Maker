<?php

namespace App\Jobs;

use App\Models\Student;
use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSMSJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 30;

    public function __construct(
        public readonly string   $phone,
        public readonly string   $message,
        public readonly string   $channel = 'sms',
        public readonly ?Student $student = null,
    ) {}

    public function handle(SmsService $service): void
    {
        if ($this->channel === 'whatsapp') {
            $service->sendWhatsApp($this->phone, $this->message, $this->student);
        } else {
            $service->sendSms($this->phone, $this->message, $this->student);
        }
    }
}

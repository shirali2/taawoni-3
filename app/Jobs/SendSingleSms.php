<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\Sms\SmsProviderFactory;
use App\SmsLog;

class SendSingleSms implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $mobile;
    public $message;
    public $userId;

    public function __construct(string $mobile, string $message, $userId = null)
    {
        $this->mobile  = $mobile;
        $this->message = $message;
        $this->userId  = $userId;
    }

    public function handle()
    {
        $log = SmsLog::create([
            'user_id' => $this->userId,
            'mobile'  => $this->mobile,
            'message' => $this->message,
            'status'  => 'pending',
        ]);

        try {
            $provider = SmsProviderFactory::make();
            $success  = $provider->send($this->mobile, $this->message);
            $log->update([
                'status'   => $success ? 'sent' : 'failed',
                'provider' => $provider->getName(),
                'sent_at'  => $success ? now() : null,
            ]);
        } catch (\Exception $e) {
            $log->update(['status' => 'failed']);
            \Log::error('SendSingleSms failed', ['error' => $e->getMessage()]);
        }
    }
}

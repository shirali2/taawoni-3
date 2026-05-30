<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\Sms\SmsProviderFactory;
use App\SmsLog;
use App\setting;

class SendSmsBatch implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $tries   = 1;
    public $timeout = 3600;

    protected $recipients;
    protected $campaignId;

    public function __construct(array $recipients, $campaignId = null)
    {
        $this->recipients = $recipients;
        $this->campaignId = $campaignId;
    }

    public function handle()
    {
        $startHour   = (int)(optional(setting::where('name', 'sms_start_hour')->first())->value ?? 8);
        $endHour     = (int)(optional(setting::where('name', 'sms_end_hour')->first())->value ?? 23);
        $interval    = (int)(optional(setting::where('name', 'sms_interval_seconds')->first())->value ?? 3);
        $currentHour = (int)date('H');

        if ($currentHour < $startHour || $currentHour >= $endHour) {
            $now     = new \DateTime();
            $allowed = new \DateTime('today ' . str_pad($startHour, 2, '0', STR_PAD_LEFT) . ':01');
            if ($now >= $allowed) {
                $allowed->modify('+1 day');
            }
            $secondsToWait = $allowed->getTimestamp() - $now->getTimestamp();
            \Log::info('SMS batch released until allowed hours', ['seconds' => $secondsToWait]);
            $this->release($secondsToWait);
            return;
        }

        try {
            $provider = SmsProviderFactory::make();
        } catch (\Exception $e) {
            \Log::error('SMS Batch: provider unavailable', ['error' => $e->getMessage()]);
            return;
        }

        foreach ($this->recipients as $recipient) {
            $log = SmsLog::create([
                'sms_campaign_id' => $this->campaignId,
                'user_id'         => $recipient['user_id'] ?? null,
                'mobile'          => $recipient['mobile'],
                'message'         => $recipient['message'],
                'status'          => 'pending',
                'provider'        => $provider->getName(),
            ]);

            $success = $provider->send($recipient['mobile'], $recipient['message']);

            $log->update([
                'status'  => $success ? 'sent' : 'failed',
                'sent_at' => $success ? now() : null,
            ]);

            sleep($interval);
        }

        if ($this->campaignId) {
            \App\SmsCampaign::where('id', $this->campaignId)->update(['status' => 'sent', 'sent_at' => now()]);
        }
    }
}

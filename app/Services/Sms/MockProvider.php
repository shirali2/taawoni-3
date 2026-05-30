<?php
namespace App\Services\Sms;

class MockProvider implements SmsProviderInterface
{
    public function send(string $to, string $message): bool
    {
        $managerPhone = config('sms.manager_phone') ?: $to;
        \Log::info('[TEST MODE] SMS would be sent', [
            'real_to'  => $to,
            'test_to'  => $managerPhone,
            'message'  => $message,
        ]);
        return true;
    }

    public function getName(): string { return 'mock'; }
}

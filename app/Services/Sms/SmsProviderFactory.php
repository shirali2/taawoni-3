<?php
namespace App\Services\Sms;

class SmsProviderFactory
{
    public static function make(): SmsProviderInterface
    {
        $status = config('sms.status', 'inactive');

        if ($status === 'test') {
            return new MockProvider();
        }

        if ($status !== 'active') {
            throw new \Exception('سیستم پیامک غیر فعال است.');
        }

        $driver = config('sms.driver', 'kavenegar');

        switch ($driver) {
            case 'kavenegar': return new KavenegarProvider();
            default:          return new KavenegarProvider();
        }
    }
}

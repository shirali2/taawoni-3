<?php
namespace App\Services\Sms;

interface SmsProviderInterface
{
    public function send(string $to, string $message): bool;
    public function getName(): string;
}

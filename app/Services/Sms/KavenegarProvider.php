<?php
namespace App\Services\Sms;

class KavenegarProvider implements SmsProviderInterface
{
    protected $apiKey;
    protected $lineNumber;

    public function __construct()
    {
        $this->apiKey     = config('sms.kavenegar.api_key');
        $this->lineNumber = config('sms.kavenegar.line_number');
    }

    public function send(string $to, string $message): bool
    {
        if (empty($this->apiKey)) {
            \Log::warning('SMS not sent: SMS_KAVENEGAR_API_KEY is not configured.');
            return false;
        }

        try {
            $url  = "https://api.kavenegar.com/v1/{$this->apiKey}/sms/send.json";
            $data = http_build_query([
                'receptor' => $to,
                'message'  => $message,
                'sender'   => $this->lineNumber,
            ]);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $result   = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            return $httpCode === 200;
        } catch (\Exception $e) {
            \Log::error('SMS send failed', ['error' => $e->getMessage(), 'to' => $to]);
            return false;
        }
    }

    public function getName(): string { return 'kavenegar'; }
}

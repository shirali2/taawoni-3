<?php
namespace App\Services;

class SmsVariableParser
{
    public function parse(string $template, $user, $invoice = null): string
    {
        $formData = [];
        if (method_exists($user, 'latestFormData')) {
            $formData = $user->latestFormData() ?? [];
        }

        $replacements = [
            '{نام}'            => $user->first_name ?? '',
            '{نام_خانوادگی}'  => $user->last_name ?? '',
            '{نام_کامل}'       => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')),
            '{کد_ملی}'         => $user->national_id ?? '',
            '{شناسه}'          => $user->id ?? '',
            '{شماره_اختصاصی}' => $user->code ?? '',
            '{تاریخ_امروز}'    => $this->todayJalali(),
            '{شماره_واحد}'     => $formData['unit_number'] ?? '',
            '{نام_وکیل}'       => $formData['lawyer_name'] ?? '',
        ];

        if ($invoice) {
            $replacements['{بدهی_کل}']   = number_format($invoice->remaining_balance ?? 0);
            $replacements['{واریزی_کل}'] = number_format($invoice->total_paid ?? 0);
            $replacements['{جریمه_کل}']  = number_format($invoice->total_penalty ?? 0);
        }

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }

    private function todayJalali(): string
    {
        if (function_exists('jdate')) {
            return jdate('Y/m/d');
        }
        return date('Y/m/d');
    }
}

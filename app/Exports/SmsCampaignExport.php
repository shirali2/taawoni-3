<?php

namespace App\Exports;

use App\SmsLog;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SmsCampaignExport implements FromQuery, WithHeadings, WithMapping
{
    private $campaignId;
    private $rowIndex = 0;

    public function __construct($campaignId)
    {
        $this->campaignId = $campaignId;
    }

    public function query()
    {
        return SmsLog::where('sms_campaign_id', $this->campaignId)
            ->with(['user.group']);
    }

    public function headings(): array
    {
        return [
            'ردیف',
            'شناسه پیامک',
            'نام و نام‌خانوادگی',
            'نام وکیل',
            'شماره موبایل',
            'شناسه کاربر',
            'شماره اختصاصی',
            'گروه کاربری',
            'متن پیام',
            'زمان ارسال',
            'وضعیت',
        ];
    }

    public function map($log): array
    {
        $this->rowIndex++;

        $statusLabels = ['sent' => 'ارسال شده', 'failed' => 'ناموفق', 'pending' => 'در انتظار'];

        return [
            $this->rowIndex,
            $log->id,
            trim(optional($log->user)->name . ' ' . optional($log->user)->name2),
            optional($log->user)->lawyer_name,
            $log->mobile,
            $log->user_id,
            optional($log->user)->kod,
            optional(optional($log->user)->group)->name,
            $log->message,
            $log->sent_at ? $log->sent_at->format('Y-m-d H:i') : '',
            $statusLabels[$log->status] ?? $log->status,
        ];
    }
}

<?php

namespace App\Console\Commands;

use App\invoice;
use App\invoice_pay;
use App\Services\InvoicePenaltyCalculator;
use Illuminate\Console\Command;

/**
 * بازمحاسبه کامل جریمه‌های تمام صورت‌حساب‌ها
 *
 * کاربردها:
 *   - اجرای اولیه پس از نصب
 *   - بازیابی از بکاپ
 *   - اجرای شبانه از طریق Scheduler
 *
 * اجرا: php artisan invoices:recalculate-penalties
 */
class RecalculateInvoicePenalties extends Command
{
    protected $signature   = 'invoices:recalculate-penalties';
    protected $description = 'بازمحاسبه کامل جریمه‌های همه صورت‌حساب‌ها برای همه کاربران';

    public function handle()
    {
        $calculator = new InvoicePenaltyCalculator();

        $total     = invoice::whereNotNull('am_end')->where('am_end', '!=', '')->count();
        $processed = 0;

        $this->info("تعداد صورت‌حساب‌های دارای تاریخ تعهد: {$total}");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        invoice::whereNotNull('am_end')
            ->where('am_end', '!=', '')
            ->chunk(50, function ($invoices) use ($calculator, &$processed, $bar) {
                foreach ($invoices as $inv) {
                    $calculator->recalculateAllUsersForInvoice((int) $inv['id']);
                    $processed++;
                    $bar->advance();
                }
            });

        $bar->finish();
        $this->line("");
        $this->info("عملیات با موفقیت پایان یافت. {$processed} صورت‌حساب پردازش شد.");

        return 0;
    }
}

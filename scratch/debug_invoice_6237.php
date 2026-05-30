<?php
/**
 * دیباگ محاسبه جریمه صورتحساب ۶۲۳۷ کاربر ۷۷۰۱
 * اجرا: docker exec maskan-app php /var/www/laravel/scratch/debug_invoice_6237.php
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$invoiceId = 6237;
$userId    = 7701;

$invoice = DB::table('invoices')->find($invoiceId);

if (!$invoice) {
    echo "صورتحساب $invoiceId پیدا نشد\n";
    exit(1);
}

echo "=== اطلاعات صورتحساب ===\n";
echo "ID            : $invoice->id\n";
echo "title         : $invoice->title\n";
echo "price         : " . number_format($invoice->price) . "\n";
echo "am_end        : $invoice->am_end\n";
echo "daily_fine_amt: $invoice->daily_fine_amount\n";
echo "fixed_penalty : $invoice->fixed_penalty_amount\n";

echo "\n=== پرداخت‌های کاربر ===\n";
$pays = DB::table('invoice_pays')
    ->where('id_invoice', $invoiceId)
    ->where('id_user', $userId)
    ->where('active_pay', 1)
    ->orderBy('am')
    ->get(['price', 'am', 'text']);

foreach ($pays as $p) {
    echo "  {$p->am}  " . number_format($p->price) . "  {$p->text}\n";
}

echo "\n=== رکوردهای جریمه ===\n";
$fines = DB::table('invoice_fines')
    ->where('id_invoice', $invoiceId)
    ->where('id_user', $userId)
    ->get();

foreach ($fines as $f) {
    echo "  price={$f->price}  total_paid={$f->total_paid}  total_discount={$f->total_discount}  total_penalty={$f->total_penalty}  remaining_principal={$f->remaining_principal}\n";
}

echo "\n=== تست parseJalali ===\n";
try {
    $carbon = Morilog\Jalali\Jalalian::fromFormat('Y/m/d', $invoice->am_end)->toCarbon()->startOfDay();
    echo "am_end parsed OK: " . $carbon->toDateString() . "\n";
    echo "today > am_end: " . (Carbon\Carbon::today()->gt($carbon) ? 'بله (باید جریمه داشته باشد)' : 'خیر (مهلت نرسیده)') . "\n";
} catch (\Throwable $e) {
    echo "PARSE ERROR برای am_end '{$invoice->am_end}': " . $e->getMessage() . "\n";
}

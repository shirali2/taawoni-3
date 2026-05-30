<?php

namespace App\Services;

use App\invoice;
use App\invoice_fine;
use App\invoice_pay;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;

/**
 * موتور محاسبه جریمه صورت‌حساب
 *
 * فرمول:
 *   الف) جریمه هر فیش = مبلغ_فیش ÷ 1000 × ضریب × روزهای تاخیر تا تاریخ پرداخت
 *   ب)  مانده اصل تعهد = مبلغ_تعهد_اولیه − جمع_فیش‌ها
 *   ج)  جریمه مانده = مانده_اصل ÷ 1000 × ضریب × روزهای تاخیر تا امروز
 *   د)  جمع جریمه = Σ جریمه فیش‌ها + جریمه مانده
 */
class InvoicePenaltyCalculator
{
    /**
     * محاسبه و ذخیره جریمه برای یک کاربر روی یک صورت‌حساب
     */
    public function recalculate(int $idInvoice, int $idUser): array
    {
        $totals = $this->compute($idInvoice, $idUser);
        $this->persist($idInvoice, $idUser, $totals);
        return $totals;
    }

    /**
     * محاسبه جریمه بدون ذخیره (برای نمایش لحظه‌ای)
     */
    public function compute(int $idInvoice, int $idUser): array
    {
        $invoice = invoice::find($idInvoice);
        if (!$invoice) {
            return $this->emptyResult();
        }

        $coefficient  = (int) $invoice['daily_fine_amount'];
        $principal    = (int) $invoice['price'];
        $amEnd        = $this->parseJalali($invoice['am_end']);

        // مرتب‌سازی فیش‌ها بر اساس تاریخ پرداخت (نه تاریخ ثبت)
        $receipts = invoice_pay::where('id_user', $idUser)
            ->where('id_invoice', $idInvoice)
            ->whereIn('active_pay', [0, 1])
            ->orderBy('am', 'asc')
            ->orderBy('id', 'asc')
            ->get(['price', 'am', 'text', 'payment_date', 'registration_date']);

        $totalPaid          = 0;
        $totalDiscount      = 0;
        $receiptPenalty     = 0;
        $cumulativeCashPaid = 0; // مجموع پرداخت‌های نقدی قبل از فیش جاری (بدون تخفیف)

        foreach ($receipts as $receipt) {
            $amount     = (int) $receipt['price'];
            $totalPaid += $amount;

            // تخفیف‌ها جریمه ندارند — فقط ردیابی می‌شوند
            if (mb_substr($receipt['text'] ?? '', 0, 8) === 'تخفیف > ') {
                $totalDiscount += $amount;
                continue;
            }

            if ($amEnd && $coefficient > 0) {
                $paidDate = $this->resolvePenaltyDate($receipt);
                if ($paidDate && $paidDate->gt($amEnd)) {
                    $daysLate = $amEnd->diffInDays($paidDate);

                    // باقیمانده تعهد اصلی قبل از این فیش
                    $remainingBeforeThisReceipt = max(0, $principal - $cumulativeCashPaid);
                    // فقط تا سقف باقیمانده تعهد مشمول جریمه است — نه مازاد آن
                    $penaltyBase = min($amount, $remainingBeforeThisReceipt);

                    $receiptPenalty += ($penaltyBase / 1000) * $coefficient * $daysLate;
                }
            }

            $cumulativeCashPaid += $amount;
        }

        // مانده اصل تعهد برای نمایش (تخفیف مانده را کم می‌کند)
        $remainingPrincipal = max(0, $principal - $totalPaid);

        // مانده واقعی نقدی برای محاسبه جریمه (تخفیف نباید پایه جریمه را کم کند)
        $cashRemainingPrincipal = max(0, $principal - ($totalPaid - $totalDiscount));

        $remainingPenalty = 0;
        if ($amEnd && $cashRemainingPrincipal > 0 && $coefficient > 0) {
            $today = Carbon::today()->startOfDay();
            if ($today->gt($amEnd)) {
                $daysLate         = $amEnd->diffInDays($today);
                $remainingPenalty = ($cashRemainingPrincipal / 1000) * $coefficient * $daysLate;
            }
        }

        $totalPenalty = (int) round($receiptPenalty + $remainingPenalty);

        // Net owed = principal + earned_penalty − all_payments
        // Allows negative values so overpayment can be detected before clamping.
        $netOwed          = ($principal - $totalPaid) + $totalPenalty;
        $remainingBalance = max(0, (int) round($netOwed));
        $surplusAmount    = max(0, -(int) round($netOwed));

        return [
            'total_paid'          => (int) $totalPaid,
            'total_discount'      => (int) $totalDiscount,
            'total_penalty'       => $totalPenalty,
            'remaining_principal' => (int) $remainingPrincipal,
            'remaining_balance'   => $remainingBalance,
            'surplus_amount'      => $surplusAmount,
            'status'              => $surplusAmount > 0 ? 'overpaid' : 'normal',
        ];
    }

    /**
     * recalculate تمام کاربرانی که روی این صورت‌حساب فیش دارند
     * (مناسب برای اجرای شبانه یا بعد از ویرایش صورت‌حساب)
     */
    public function recalculateAllUsersForInvoice(int $idInvoice): void
    {
        $invoice = invoice::find($idInvoice);
        if (!$invoice) return;

        // ۱. کاربرانی که در لیست خود فاکتور هستند
        $assignedUserIds = [];
        if (!empty($invoice->id_users)) {
            $decoded = json_decode($invoice->id_users, true);
            if (is_array($decoded)) {
                if (isset($decoded['Grop Users'])) {
                    // $decoded['Grop Users'] holds group IDs (id_usergrop), NOT user IDs.
                    // We must expand them to actual user IDs via the user_grops pivot table.
                    $assignedUserIds = \App\user_grop::whereIn('id_usergrop', $decoded['Grop Users'])
                        ->pluck('user_id')
                        ->toArray();
                } else {
                    $assignedUserIds = $decoded;
                }
            } else {
                $assignedUserIds = explode(',', trim($invoice->id_users, '[] '));
            }
        }

        // ۲. کاربرانی که فیش پرداخت دارند
        $paymentUserIds = invoice_pay::where('id_invoice', $idInvoice)
            ->distinct()
            ->pluck('id_user')
            ->toArray();

        // ۳. کاربرانی که قبلاً رکورد جریمه داشتند
        $fineUserIds = invoice_fine::where('id_invoice', $idInvoice)
            ->distinct()
            ->pluck('id_user')
            ->toArray();

        // ترکیب همه لیست‌ها و حذف تکراری‌ها
        $allUserIds = array_unique(array_merge($assignedUserIds, $paymentUserIds, $fineUserIds));

        foreach ($allUserIds as $idUser) {
            if (is_numeric($idUser) && $idUser > 0) {
                $this->recalculate($idInvoice, (int) $idUser);
            }
        }
    }

    /**
     * ذخیره نتایج محاسبه — همیشه یک رکورد canonical برای هر (user, invoice)
     */
    protected function persist(int $idInvoice, int $idUser, array $totals): void
    {
        DB::transaction(function () use ($idInvoice, $idUser, $totals) {
            // حذف همه رکوردهای قبلی (از جمله رکوردهای روزانه کرون قدیمی)
            invoice_fine::where('id_user', $idUser)
                ->where('id_invoice', $idInvoice)
                ->delete();

            $fine                      = new invoice_fine();
            $fine->id_user             = $idUser;
            $fine->id_invoice          = $idInvoice;
            $fine->price               = $totals['total_penalty']; // backward compat
            $fine->total_paid          = $totals['total_paid'];
            $fine->total_discount      = $totals['total_discount'];
            $fine->total_penalty       = $totals['total_penalty'];
            $fine->remaining_principal = $totals['remaining_principal'];
            $fine->remaining_balance   = $totals['remaining_balance'];
            $fine->status              = $totals['status'] ?? 'normal';
            $fine->surplus_amount      = $totals['surplus_amount'] ?? 0;
            $fine->am                  = Verta::parse(verta()->formatDate());
            $fine->save();
        });
    }

    /**
     * Pick the correct penalty cutoff date for a receipt based on billing config.
     * Falls back to the legacy `am` field when the new columns are not populated.
     */
    protected function resolvePenaltyDate(object $receipt): ?Carbon
    {
        $basis = config('billing.penalty_date_basis', 'payment_date');

        if ($basis === 'registration_date') {
            $date = $receipt['registration_date'] ?? $receipt['am'];
        } else {
            $date = $receipt['payment_date'] ?? $receipt['am'];
        }

        return $this->parseJalali($date);
    }

    protected function parseJalali(?string $value): ?Carbon
    {
        if (empty($value)) {
            return null;
        }
        try {
            return Carbon::instance(Verta::parse($value)->datetime())->startOfDay();
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function emptyResult(): array
    {
        return [
            'total_paid'          => 0,
            'total_discount'      => 0,
            'total_penalty'       => 0,
            'remaining_principal' => 0,
            'remaining_balance'   => 0,
            'surplus_amount'      => 0,
            'status'              => 'normal',
        ];
    }
}

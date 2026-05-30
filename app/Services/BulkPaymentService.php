<?php

namespace App\Services;

use App\BulkPaymentCredit;
use App\invoice_fine;
use App\invoice_pay;
use Hekmatinasser\Verta\Verta;
use Illuminate\Support\Facades\DB;

class BulkPaymentService
{
    private $calculator;
    private $penaltyService;

    public function __construct(
        InvoicePenaltyCalculator $calculator,
        PenaltyRecalculationService $penaltyService
    ) {
        $this->calculator = $calculator;
        $this->penaltyService = $penaltyService;
    }

    /**
     * Distribute a bulk payment across a user's outstanding debts (oldest due date first).
     *
     * For each fully-covered debt: registers payment with note «پرداخت از محل واریزی شماره [ID]»
     * For a partially-covered debt: registers payment with note «پرداخت بخشی از واریزی شماره [ID] — مبلغ [X] ریال»
     * Any surplus after all debts are cleared is persisted in bulk_payment_credits.
     *
     * Receipt IDs for sub-payments use the format: {receiptId}-I{invoiceId}
     *
     * @return array{affected: list<array{invoice_id:int,invoice_title:string,paid:int,was_full:bool,sub_receipt:string}>, credit_remaining: int, total_paid: int, warnings: list<string>}
     */
    public function distribute(
        int    $userId,
        int    $amount,
        string $receiptId,
        string $date
    ): array {
        $debts            = $this->getUserDebts($userId);
        $affected         = [];
        $remaining        = $amount;
        $overpaidWarnings = [];

        // Determine today's Jalali date for the audit trail
        $registrationDate = verta()->formatDate();
        $isBackdated      = $this->isBackdated($date, $registrationDate);

        DB::transaction(function () use (
            $debts, &$remaining, &$affected, &$overpaidWarnings,
            $receiptId, $date, $registrationDate, $isBackdated,
            $userId, $amount
        ) {
            foreach ($debts as $debt) {
                if ($remaining <= 0) break;

                $invoiceId        = (int) $debt['invoice_id'];
                $remainingBalance = (int) $debt['remaining_balance'];

                if ($remainingBalance <= 0) continue;

                $subReceiptId = $receiptId . '-I' . $invoiceId;

                // Idempotency guard — skip if this sub-receipt already exists
                if (invoice_pay::where('id', $subReceiptId)->exists()) {
                    continue;
                }

                if ($remaining >= $remainingBalance) {
                    $payAmount = $remainingBalance;
                    $note      = 'پرداخت از محل واریزی شماره ' . $receiptId;
                } else {
                    $payAmount = $remaining;
                    $note      = 'پرداخت بخشی از واریزی شماره ' . $receiptId . ' — مبلغ ' . number_format($payAmount) . ' ریال';
                }

                $pay                    = new invoice_pay();
                $pay->id                = $subReceiptId;
                $pay->id_user           = $userId;
                $pay->id_invoice        = $invoiceId;
                $pay->price             = $payAmount;
                $pay->text              = $note;
                $pay->am                = $date;
                $pay->payment_date      = $date;
                $pay->registration_date = $registrationDate;
                $pay->is_backdated      = $isBackdated;
                $pay->active_pay        = 1;
                $pay->save();

                $remaining -= $payAmount;

                $result = $this->penaltyService->recalculate($invoiceId, $userId);

                if (!empty($result['warning'])) {
                    $overpaidWarnings[] = $result['warning'];
                }

                $affected[] = [
                    'invoice_id'    => $invoiceId,
                    'invoice_title' => $debt['title'],
                    'paid'          => $payAmount,
                    'was_full'      => ($payAmount >= $remainingBalance),
                    'sub_receipt'   => $subReceiptId,
                    'status'        => $result['status'] ?? 'normal',
                    'surplus'       => $result['surplus_amount'] ?? 0,
                ];
            }

            if ($remaining > 0) {
                BulkPaymentCredit::create([
                    'id_user'         => $userId,
                    'receipt_id'      => $receiptId,
                    'original_amount' => $amount,
                    'credit_amount'   => $remaining,
                    'am'              => $date,
                ]);
            }
        });

        return [
            'affected'         => $affected,
            'credit_remaining' => $remaining,
            'total_paid'       => $amount - $remaining,
            'warnings'         => $overpaidWarnings,
        ];
    }

    /**
     * A receipt is backdated when its payment_date is more than the configured
     * threshold days before the registration_date.
     */
    private function isBackdated(string $paymentDateJalali, string $registrationDateJalali): bool
    {
        try {
            $threshold    = (int) config('billing.backdated_threshold_days', 1);
            $paymentCarbon = \Morilog\Jalali\Jalalian::fromFormat('Y/m/d', $paymentDateJalali)->toCarbon();
            $regCarbon     = \Morilog\Jalali\Jalalian::fromFormat('Y/m/d', $registrationDateJalali)->toCarbon();
            return $regCarbon->diffInDays($paymentCarbon, false) < -$threshold;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Return all invoices with remaining balance for the given user, ordered by due date (oldest first).
     * Ensures invoice_fine records exist for all invoices where the user has active payments.
     */
    public function getUserDebts(int $userId): array
    {
        // Ensure fine records exist for any invoice where user has payments but no fine record yet
        $invoiceIdsWithPayments = invoice_pay::where('id_user', $userId)
            ->where('active_pay', 1)
            ->distinct()
            ->pluck('id_invoice')
            ->toArray();

        foreach ($invoiceIdsWithPayments as $invoiceId) {
            $exists = invoice_fine::where('id_user', $userId)
                ->where('id_invoice', $invoiceId)
                ->exists();
            if (!$exists) {
                $this->calculator->recalculate((int) $invoiceId, $userId);
            }
        }

        return DB::table('invoice_fines')
            ->join('invoices', 'invoice_fines.id_invoice', '=', 'invoices.id')
            ->where('invoice_fines.id_user', $userId)
            ->where('invoice_fines.remaining_balance', '>', 0)
            ->orderBy('invoices.am_end', 'asc')
            ->select([
                'invoice_fines.id_invoice as invoice_id',
                'invoices.title',
                'invoices.am_end',
                'invoice_fines.remaining_balance',
                'invoice_fines.remaining_principal',
                'invoice_fines.total_penalty',
            ])
            ->get()
            ->map(function ($r) {
                return (array) $r;
            })
            ->toArray();
    }
}

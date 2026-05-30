<?php

namespace App\Services;

use App\invoice_fine;

/**
 * Conflict-resolution wrapper around InvoicePenaltyCalculator.
 *
 * Responsibilities:
 *  1. Skip rows that are already cleanly settled (remaining_balance = 0, no surplus).
 *  2. Delegate computation to InvoicePenaltyCalculator (which respects billing.penalty_date_basis).
 *  3. Surface overpaid status and produce a human-readable warning when the user overpaid.
 */
class PenaltyRecalculationService
{
    private $calculator;

    public function __construct(InvoicePenaltyCalculator $calculator)
    {
        $this->calculator = $calculator;
    }

    /**
     * Recalculate penalty for a (invoice, user) pair and persist the result.
     *
     * Returns an array with all computed totals plus:
     *   - 'status'        : 'normal' | 'overpaid'
     *   - 'surplus_amount': int — Rials overpaid beyond principal + earned penalty
     *   - 'warning'       : string|null — Persian warning message if overpaid, null otherwise
     *
     * Rows that are already cleanly settled (remaining_balance = 0 AND surplus_amount = 0)
     * are returned as-is without re-persisting.
     */
    public function recalculate(int $invoiceId, int $userId): array
    {
        if ($this->isAlreadySettled($invoiceId, $userId)) {
            return $this->settledResult($invoiceId, $userId);
        }

        $totals = $this->calculator->recalculate($invoiceId, $userId);

        $warning = null;
        if (($totals['status'] ?? 'normal') === 'overpaid') {
            $warning = sprintf(
                'این واریزی منجر به مازاد پرداخت در ردیف صورت‌حساب شماره %d شده است. مبلغ مازاد: %s ریال',
                $invoiceId,
                number_format($totals['surplus_amount'])
            );
        }

        return array_merge($totals, ['warning' => $warning]);
    }

    /**
     * Recalculate for every (invoice, user) pair tied to an invoice.
     * Returns an array of warnings keyed by userId.
     *
     * @return array<int, string>  userId → warning message
     */
    public function recalculateAllUsers(int $invoiceId): array
    {
        $this->calculator->recalculateAllUsersForInvoice($invoiceId);

        return invoice_fine::where('id_invoice', $invoiceId)
            ->where('status', 'overpaid')
            ->get(['id_user', 'surplus_amount', 'id_invoice'])
            ->mapWithKeys(function ($fine) {
                return [
                    $fine->id_user => sprintf(
                    'این واریزی منجر به مازاد پرداخت در ردیف صورت‌حساب شماره %d شده است. مبلغ مازاد: %s ریال',
                    $fine->id_invoice,
                    number_format($fine->surplus_amount)
                    ),
                ];
            })
            ->toArray();
    }

    // -------------------------------------------------------------------------

    private function isAlreadySettled(int $invoiceId, int $userId): bool
    {
        $fine = invoice_fine::where('id_invoice', $invoiceId)
            ->where('id_user', $userId)
            ->first(['remaining_balance', 'surplus_amount', 'status']);

        if (!$fine) {
            return false;
        }

        return $fine->remaining_balance == 0
            && $fine->surplus_amount == 0
            && $fine->status === 'normal';
    }

    private function settledResult(int $invoiceId, int $userId): array
    {
        $fine = invoice_fine::where('id_invoice', $invoiceId)
            ->where('id_user', $userId)
            ->first();

        if (!$fine) {
            return array_merge($this->calculator->compute($invoiceId, $userId), ['warning' => null]);
        }

        return [
            'total_paid'          => (int) $fine->total_paid,
            'total_discount'      => (int) $fine->total_discount,
            'total_penalty'       => (int) $fine->total_penalty,
            'remaining_principal' => (int) $fine->remaining_principal,
            'remaining_balance'   => (int) $fine->remaining_balance,
            'surplus_amount'      => (int) $fine->surplus_amount,
            'status'              => $fine->status ?? 'normal',
            'warning'             => null,
        ];
    }
}

<?php

namespace App\Observers;

use App\invoice_pay;
use App\Services\InvoicePenaltyCalculator;

class PaymentObserver
{
    protected $calculator;

    public function __construct(InvoicePenaltyCalculator $calculator)
    {
        $this->calculator = $calculator;
    }

    public function saved(invoice_pay $payment)
    {
        if ($payment->id_invoice && $payment->id_user) {
            $this->calculator->recalculate((int) $payment->id_invoice, (int) $payment->id_user);
        }
    }

    public function deleted(invoice_pay $payment)
    {
        if ($payment->id_invoice && $payment->id_user) {
            $this->calculator->recalculate((int) $payment->id_invoice, (int) $payment->id_user);
        }
    }
}

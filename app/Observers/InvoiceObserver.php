<?php

namespace App\Observers;

use App\invoice;
use App\Services\InvoicePenaltyCalculator;

class InvoiceObserver
{
    protected $calculator;

    public function __construct(InvoicePenaltyCalculator $calculator)
    {
        $this->calculator = $calculator;
    }

    public function saved(invoice $invoice)
    {
        $this->calculator->recalculateAllUsersForInvoice((int) $invoice->id);
    }
}

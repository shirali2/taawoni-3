<?php

return [
    /*
     | When a backdated receipt is registered, which date is used as the
     | penalty cutoff for that receipt?
     |
     | 'payment_date'      — use the date the payment was actually made (invoice_pays.payment_date)
     | 'registration_date' — use the date the receipt was entered into the system (invoice_pays.registration_date)
     |
     | 'payment_date' is more accurate (lower penalty when paid early).
     | 'registration_date' is conservative (the admin captures the full accrued penalty on entry).
     */
    'penalty_date_basis' => env('PENALTY_DATE_BASIS', 'payment_date'),

    /*
     | Minimum number of calendar days difference between payment_date and
     | registration_date before a receipt is flagged as is_backdated.
     */
    'backdated_threshold_days' => (int) env('BACKDATED_THRESHOLD_DAYS', 1),
];

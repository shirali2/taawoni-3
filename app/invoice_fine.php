<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class invoice_fine extends Authenticatable
{
    protected $fillable = [
        'id_user', 'id_invoice', 'price',
        'total_paid', 'total_discount', 'total_penalty',
        'remaining_principal', 'remaining_balance',
        'status', 'surplus_amount',
        'am',
    ];

    protected $casts = [
        'total_paid'          => 'integer',
        'total_discount'      => 'integer',
        'total_penalty'       => 'integer',
        'remaining_principal' => 'integer',
        'remaining_balance'   => 'integer',
        'surplus_amount'      => 'integer',
    ];

    public function invoice()
    {
        return $this->belongsTo(invoice::class, 'id_invoice');
    }

    public function user()
    {
        return $this->belongsTo(user::class, 'id_user');
    }

    public function isOverpaid(): bool
    {
        return $this->status === 'overpaid';
    }

    public function isSettled(): bool
    {
        return $this->remaining_balance == 0 && $this->surplus_amount == 0 && $this->status === 'normal';
    }
}

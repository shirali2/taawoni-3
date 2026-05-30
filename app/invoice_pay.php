<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class invoice_pay extends Authenticatable
{
    protected $fillable = [
        'id', 'id_user', 'id_invoice', 'price', 'text', 'am',
        'payment_date', 'registration_date', 'is_backdated',
        'active_pay', 'transactionId', 'uuid',
    ];

    protected $casts = [
        'is_backdated' => 'boolean',
        'price'        => 'integer',
        'active_pay'   => 'integer',
    ];

    public function invoice()
    {
        return $this->belongsTo(invoice::class, 'id_invoice');
    }

    public function user()
    {
        return $this->belongsTo(user::class, 'id_user');
    }
}

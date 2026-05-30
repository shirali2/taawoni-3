<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BulkPaymentCredit extends Model
{
    protected $fillable = ['id_user', 'receipt_id', 'original_amount', 'credit_amount', 'am'];

    public function user()
    {
        return $this->belongsTo(user::class, 'id_user');
    }
}

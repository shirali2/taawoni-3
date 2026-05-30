<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    protected $fillable = ['wallet_id', 'type', 'amount', 'description', 'created_by_admin_id'];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class, 'wallet_id');
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = ['manager_id', 'balance', 'min_threshold'];

    public function manager()
    {
        return $this->belongsTo(manager::class, 'manager_id');
    }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class, 'wallet_id');
    }
}

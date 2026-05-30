<?php

namespace App\Services;

use App\Wallet;
use App\WalletTransaction;
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function getOrCreate($managerId)
    {
        return Wallet::firstOrCreate(
            ['manager_id' => $managerId],
            ['balance' => 0, 'min_threshold' => 0]
        );
    }

    public function getBalance($managerId)
    {
        return $this->getOrCreate($managerId)->balance;
    }

    public function canAfford($managerId, $cost)
    {
        $wallet = $this->getOrCreate($managerId);
        return ($wallet->balance - $cost) >= $wallet->min_threshold;
    }

    public function credit($managerId, $amount, $description = null, $adminId = null)
    {
        DB::transaction(function () use ($managerId, $amount, $description, $adminId) {
            $wallet = $this->getOrCreate($managerId);
            $wallet->increment('balance', $amount);
            WalletTransaction::create([
                'wallet_id'           => $wallet->id,
                'type'                => 'credit',
                'amount'              => $amount,
                'description'         => $description,
                'created_by_admin_id' => $adminId,
            ]);
        });
    }

    public function debit($managerId, $amount, $description = null)
    {
        DB::transaction(function () use ($managerId, $amount, $description) {
            $wallet = $this->getOrCreate($managerId);
            if (!$this->canAfford($managerId, $amount)) {
                throw new \Exception('موجودی کافی نیست. کسری: ' . number_format($amount - $wallet->balance + $wallet->min_threshold) . ' ریال');
            }
            $wallet->decrement('balance', $amount);
            WalletTransaction::create([
                'wallet_id'   => $wallet->id,
                'type'        => 'debit',
                'amount'      => $amount,
                'description' => $description,
            ]);
        });
    }

    public function setThreshold($managerId, $threshold)
    {
        $this->getOrCreate($managerId)->update(['min_threshold' => $threshold]);
    }
}

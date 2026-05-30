<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Traits\HasCollectionScope;

class user extends Authenticatable
{
    use HasCollectionScope;

    protected $casts = [
        'is_hidden' => 'boolean',
        'inactive'  => 'boolean',
    ];

    /**
     * رابطه با گروه‌های کاربر (Pivot)
     */
    public function user_grops()
    {
        return $this->hasMany(user_grop::class, 'user_id');
    }

    /**
     * رابطه با صورت‌حساب‌های فروش
     */
    public function sales_invoices()
    {
        return $this->hasMany(sales_invoice::class, 'customer');
    }

    /**
     * رابطه با گروه اصلی (برای نمایش در لیست)
     */
    public function group()
    {
        return $this->belongsTo(grop::class, 'grop');
    }
}

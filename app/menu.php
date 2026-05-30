<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Traits\HasCollectionScope;

class menu extends Authenticatable
{
    use HasCollectionScope;

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($menu) {
            under::where('id_menu', $menu->id)->delete();
        });
    }
}

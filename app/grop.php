<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class grop extends Authenticatable
{
    function user_grops():HasMany
    {
        return $this->hasMany(user_grop::class,'grop_id','id');
    }
}

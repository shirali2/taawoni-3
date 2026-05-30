<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Traits\HasCollectionScope;

class form extends Authenticatable
{
    use HasCollectionScope;

    public function user_forms():HasMany
    {
        return $this->hasMany(user_form::class,'id_form');
    }
}

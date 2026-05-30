<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Traits\HasCollectionScope;

class user_grop extends Authenticatable
{
    use HasCollectionScope;
    public function grop():BelongsTo
    {
        return $this->belongsTo(grop::class,'grop_id');
    }

    public function usergrop()
    {
        return $this->belongsTo(usergrop::class, 'id_usergrop');
    }
}

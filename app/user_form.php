<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Traits\HasCollectionScope;

class user_form extends Authenticatable
{
    use HasCollectionScope;

    public function getCollectionColumn()
    {
        return null;
    }

    protected $fillable = ['id_user', 'id_form', 'form', 'filled_by', 'external_token_id'];
}

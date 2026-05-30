<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExternalFormToken extends Model
{
    protected $fillable = ['form_id', 'token', 'password_hash', 'is_active', 'created_by_admin_id', 'expires_at'];

    protected $dates = ['expires_at'];

    public function form()
    {
        return $this->belongsTo(\App\form::class);
    }

    public function isValid(): bool
    {
        if (!$this->is_active) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        return true;
    }
}

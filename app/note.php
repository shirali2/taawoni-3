<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class note extends Model
{
    protected $table = 'notes';

    protected $fillable = [
        'user_id', 'admin_id', 'content', 'is_mandatory',
        'seen_at', 'seen_by_user_at', 'status', 'reminder_date', 'is_archived', 'visibility',
    ];

    protected $casts = [
        'is_mandatory'    => 'boolean',
        'is_archived'     => 'boolean',
        'seen_at'         => 'datetime',
        'seen_by_user_at' => 'datetime',
        'reminder_date'   => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(\App\user::class, 'user_id')->withoutGlobalScopes();
    }

    public function admin()
    {
        return $this->belongsTo(\App\admin::class, 'admin_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeNotArchived($query)
    {
        return $query->where('is_archived', false);
    }

    public function scopeVisibleToUser($query)
    {
        return $query->where('visibility', 'admin_and_user');
    }

    public function scopeUnseenByUser($query)
    {
        return $query->whereNull('seen_by_user_at');
    }
}

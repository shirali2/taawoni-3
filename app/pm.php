<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

use App\grop;
use App\Traits\HasCollectionScope;

class pm extends Authenticatable
{
    use HasCollectionScope;

    protected $fillable = [
        'id_grop', 'id_users', 'title', 'img', 'text',
        'date_end_show', 'created_by',
        // Task 4: pending-note flow fields
        'save_as_note', 'note_registered_at',
    ];

    protected $casts = [
        'save_as_note'       => 'boolean',
        'note_registered_at' => 'datetime',
    ];

    public function groups()
    {
        return $this->belongsToMany(grop::class, 'pm_groups', 'pm_id', 'grop_id');
    }
}

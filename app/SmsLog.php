<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    protected $fillable = [
        'sms_campaign_id',
        'user_id',
        'mobile',
        'message',
        'status',
        'provider',
        'sent_at',
    ];

    protected $dates = ['sent_at'];

    public function user()
    {
        return $this->belongsTo(\App\user::class, 'user_id')->withoutGlobalScopes();
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmsCampaign extends Model
{
    protected $table = 'sms_campaigns';

    protected $fillable = [
        'manager_id', 'campaign_number', 'title_type', 'message_text', 'sender_name',
        'target_user_ids', 'target_group_ids', 'target_collection_ids',
        'include_active', 'include_inactive', 'total_users', 'total_sms_per_user',
        'total_sms_count', 'cost_per_sms', 'total_cost', 'interval_seconds',
        'status', 'sent_at',
    ];

    protected $casts = [
        'target_user_ids'       => 'array',
        'target_group_ids'      => 'array',
        'target_collection_ids' => 'array',
        'include_active'        => 'boolean',
        'include_inactive'      => 'boolean',
    ];

    protected $dates = ['sent_at'];

    public function logs()
    {
        return $this->hasMany(SmsLog::class, 'sms_campaign_id');
    }
}

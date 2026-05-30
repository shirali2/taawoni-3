<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
/**
 *  @property int $id
 * @property int $user_id
 * @property string $user_code
 * @property int $grop_id
 * @property string $mobile
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
use App\Traits\HasCollectionScope;

class user_auth_log extends Model
{
    use HasCollectionScope;
    protected $table = 'user_auth_logs';

    public function getCollectionColumn()
    {
        return 'grop_id';
    }
    
    protected $fillable = [
        'user_id', 
        'user_code', 
        'grop_id', 
        'mobile', 
        'status',
        'user_type',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function grop(){
        return $this->belongsTo(grop::class, 'grop_id');
    }
}

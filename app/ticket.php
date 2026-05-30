<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCollectionScope;

class ticket extends Model
{
    use HasCollectionScope;

    protected $table = 'tickets';
}

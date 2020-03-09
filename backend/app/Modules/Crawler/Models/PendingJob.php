<?php

namespace App\Modules\Crawler\Models;

use App\Traits\TableName;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class PendingJob extends Eloquent
{
    use TableName;

    protected $collection = 'pending_jobs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'url',
        'data',
    ];
}

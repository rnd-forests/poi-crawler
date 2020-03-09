<?php

namespace App\Modules\Crawler\Models;

use App\Traits\TableName;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class FailedJob extends Eloquent
{
    use TableName;

    // protected $connection = 'mongodb_prod';
    protected $collection = 'failed_jobs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
}

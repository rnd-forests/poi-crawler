<?php

namespace App\Modules\Location\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class AdditionLocationInfo extends Eloquent
{
    use SoftDeletes;

    protected $collection = 'add_loc_info';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'google_map',
    ];
}

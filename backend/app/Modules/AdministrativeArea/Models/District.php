<?php

namespace App\Modules\AdministrativeArea\Models;

use App\Traits\ScopeFullText;
use App\Traits\ScopeNameLike;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class District extends Eloquent
{
    use ScopeFullText, ScopeNameLike;

    protected $collection = 'districts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'province_id',
        'province_name',
    ];

    public static function findNameLike($name, $fields = ['_id', 'name', 'slug'])
    {
        return static::where('name', 'like', "%{$name}%")->first($fields);
    }
}

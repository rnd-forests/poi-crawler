<?php

namespace App\Modules\AdministrativeArea\Models;

use App\Traits\ScopeFullText;
use App\Traits\ScopeNameLike;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Province extends Eloquent
{
    use ScopeFullText, ScopeNameLike;

    protected $collection = 'provinces';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'country_id',
        'country_name',
    ];

    public static function findNameLike($name, $fields = ['_id', 'name', 'slug'])
    {
        return static::where('name', 'like', "%{$name}%")->first($fields);
    }
}

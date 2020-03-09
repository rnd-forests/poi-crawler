<?php

namespace App\Modules\AdministrativeArea\Models;

use App\Traits\ScopeFullText;
use App\Traits\ScopeNameLike;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Ward extends Eloquent
{
    use ScopeFullText, ScopeNameLike;

    protected $collection = 'wards';

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

    /**
     * Scope a query to only include popular users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDistrictId($query, $id)
    {
        return $query->where('district_id', '=', $id);
    }
}

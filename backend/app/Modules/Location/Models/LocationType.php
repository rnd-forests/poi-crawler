<?php

namespace App\Modules\Location\Models;

use App\Traits\ScopeFullText;
use App\Traits\ScopeNameLike;
use App\Traits\TableName;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class LocationType extends Eloquent
{
    use SoftDeletes, ScopeFullText, ScopeNameLike, TableName;

    protected $collection = 'location_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'locales', // [{language: 'en', name: 'foo', slug: '', description: ''}, ...]
    ];

    public function scopeLocale($query, $language = 'en')
    {
//        return $query->where('locales', 'elemMatch', ['language' => $language]);
        return $query->where('locales.language', $language);
    }
}

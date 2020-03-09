<?php

namespace App\Modules\Location\Models;

use App\Modules\Location\Helper\LocationHelper;
use App\Traits\ScopeFullText;
use App\Traits\ScopeNameLike;
use App\Traits\TableName;
use App\Utils\Common;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class Location extends Eloquent
{
    use SoftDeletes, ScopeFullText, ScopeNameLike, TableName;

    protected $collection = 'locations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'avatar',
        'categories',
        'images',
        'keywords',
        'weight',
        'source',
        'source_url',
        'review',
        'formatted_address',
        'locales',
//        'longitude',
//        'latitude',
        'area',
        'geometry',
        'type',
        'price_range',
        'map_info',
    ];

    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // track who modifying
            $model->edited_by = static::getModifyUser($model->edited_by);

            // format geometry
            if (! isset($model->geometry['type'])) {
                $model->geometry = static::getGeometryFormat($model->geometry['coordinates']);
            }
        });
    }

    /**
     * @param array $coordinates [longitude, latitude]
     * @param string $type
     * @return array
     */
    public static function getGeometryFormat($coordinates, $type = 'Point')
    {
        return [
            'type' => $type,
            'coordinates' => [(float) $coordinates[0], (float) $coordinates[1]],
        ];
    }

    /**
     * @param array $oldEditedUsers
     * @return array new array of modify user
     */
    public static function getModifyUser($oldEditedUsers)
    {
        $oldEditedUsers = $oldEditedUsers ?? [];
        array_push($oldEditedUsers, Common::getEditedUser(auth()->user()));

        return $oldEditedUsers;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder|mixed $query
     * @param array $coordinates in order longitude, latitude
     * @param int $max max distance in meters
     * @param int $min min distance in meters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function scopeNear($query, array $coordinates, $max = 1000, $min = 0)
    {
        return LocationHelper::queryNear($query, $coordinates, $max, $min);
    }

    public function scopeId($query, $id)
    {
        return $query->where('_id', '=', $id);
    }
}

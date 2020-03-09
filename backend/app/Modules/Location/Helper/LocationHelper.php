<?php
/**
 * Created by Trang Ha Viet.
 * Contact: <viettrangha@gmail.com>
 * 20/11/2018 12:01
 */

namespace App\Modules\Location\Helper;


class LocationHelper
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder|mixed $query
     * @param array $coordinates in order longitude, latitude
     * @param int $max max distance in meters
     * @param int $min min distance in meters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function queryNear($query, array $coordinates, $max = 1000, $min = 0)
    {
        return $query->whereRaw(
            [
                'geometry' => [
                    '$nearSphere' => [
                        '$geometry' => ['type' => "Point", 'coordinates' => $coordinates],
                        '$maxDistance' => $max,
                        '$minDistance' => $min,
                    ]
                ]
            ]
        );
    }
}

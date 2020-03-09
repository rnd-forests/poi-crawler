<?php
/**
 * Created by Trang Ha Viet.
 * Contact: <viettrangha@gmail.com>
 * 18/10/2018 08:58
 */

namespace App\Traits;

trait ScopeNameLike
{
    public static function scopeNameLike($query, $name)
    {
        return $query->where('name', 'like', "%{$name}%");
    }
}

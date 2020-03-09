<?php
/**
 * Created by Trang Ha Viet.
 * Contact: <viettrangha@gmail.com>
 * 18/10/2018 08:58
 */

namespace App\Traits;

trait ScopeFullText
{
    /**
     * Scope a query to only include popular users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFullText($query, $search, $order = true)
    {
        $query->whereRaw(['$text' => ['$search' => $search]]);

        if ($order) {
            return $query->project(['score'=>['$meta'=>'textScore']])
                ->orderBy('score', ['$meta' => "textScore"]);
        };

        return $query;
    }
}

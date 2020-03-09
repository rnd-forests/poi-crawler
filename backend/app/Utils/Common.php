<?php
/**
 * Created by Trang Ha Viet.
 * Contact: <viettrangha@gmail.com>
 * 12/10/2018 18:04
 */

namespace App\Utils;

use MongoDB\BSON\UTCDateTime;
use Illuminate\Support\Arr;

class Common
{
    public static function getEditedUser($user)
    {
        $user = is_array($user) ? $user : $user->toArray();

        return array_merge(
            Arr::only($user, ['_id', 'email', 'name']),
            ['edited_at' => new UTCDateTime()]
        );
    }
}

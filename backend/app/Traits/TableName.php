<?php
/**
 * Created by Trang Ha Viet.
 * Contact: <viettrangha@gmail.com>
 * 18/10/2018 08:58
 */

namespace App\Traits;

trait TableName
{
    public static function getTableName()
    {
        return (new static)->getTable();
    }
}

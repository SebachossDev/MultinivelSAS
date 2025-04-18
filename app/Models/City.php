<?php

namespace App\Models;

use Altwaireb\World\Models\City as Model;

class City extends Model
{
    public static function getCitiesByDepartment($departmentId)
    {
        return $departmentId
            ? self::where('state_id', $departmentId)->pluck('name', 'id')->toArray()
            : [];
    }
}

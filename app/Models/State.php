<?php

namespace App\Models;

use Altwaireb\World\Models\State as Model;

class State extends Model
{
    public static function getDepartments()
    {
        $colombia = \Altwaireb\World\Models\Country::where('name', 'Colombia')->first();
        return $colombia
            ? self::where('country_id', $colombia->id)->pluck('name', 'id')->toArray()
            : [];
    }
}

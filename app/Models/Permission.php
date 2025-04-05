<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as ModelPermission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permission extends ModelPermission
{
    use HasFactory;
}

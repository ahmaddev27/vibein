<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppUserAddress extends Model
{
    protected $table= 'app_user_addresses';

    protected $fillable = [
        'user_id',
        'address',
        'city',
        'country',
        'country',
        'area',
        'name',
        'building_number',
        'is_default'
    ];

}

<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Company extends Authenticatable
{
    protected $table = 'company';
    protected $fillable = [
        'name', 'image', 'email', 'address', 'latitude', 'longitude', 'mobile', 'description'
    ];


    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';


    protected $guard = 'company';
}

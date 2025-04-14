<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Company extends Authenticatable
{
    protected $table='companies ';
    protected $fillable = [
        'name', 'email', 'number', 'address', 'contract_start_date', 'contract_end_date', 'password'
    ];

    protected $guard = 'company';
}

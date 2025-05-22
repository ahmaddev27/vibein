<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cycle extends Model
{
    protected $table='cycles';

    protected $fillable = [
        'week_days',
        'delivers_times',
    ];

    protected $casts = [
        'week_days' => 'json',
        'delivers_times' => 'json',
    ];

}

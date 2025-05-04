<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StationImages extends Model
{

    protected $table='station_images';
    protected $fillable = [
        'station_id',
        'image',
    ];

    public function station()
    {
        return $this->belongsTo(Station::class);
    }
}

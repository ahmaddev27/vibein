<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Machine extends Model
{
    protected $table='machines';
    protected $fillable = [
        'name',
        'description',
        'status',
        'size',
        'meta_title',
        'category_id',

    ];

    public function images()
    {
        return $this->hasMany(MachineImages::class, 'machine_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }


    public function station(): BelongsToMany
    {
        return $this->belongsToMany(Station::class, 'stationMachines', 'package_id', 'station_id');
    }



}

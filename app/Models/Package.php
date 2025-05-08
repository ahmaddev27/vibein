<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Package extends Model
{

    protected $table = 'packages';


    protected $fillable = [
        'name',
        'description',
        'price',
        'total',
        'status',
        'tags'

    ];


    public function products(): HasMany
    {
        return $this->hasMany(PackageProduct::class);
    }

    public function alternatives(): HasMany
    {
        return $this->hasMany(PackageProductAlternative::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(PackageImages::class);
    }





    public function station(): BelongsToMany
    {
        return $this->belongsToMany(Station::class, 'stationPackages', 'package_id', 'station_id');
    }




}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'tags',

    ];


    protected static function booted()
    {
        static::addGlobalScope('company', function (Builder $builder) {
            $builder->where('companyId', env('DEFAULT_COMPANY_ID', 31));
        });

        static::saving(function ($model) {
            if (empty($model->companyId)) {
                $model->companyId = env('DEFAULT_COMPANY_ID', 31);
            }
        });
    }

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

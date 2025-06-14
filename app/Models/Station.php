<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Station extends Model
{
    protected $table = 'stations';
    protected $fillable = [
        'name',
        'description',
        'meta_title',
        'meta_description',
        'features',
        'sort_order',
        'is_recommended',

    ];

    protected $casts = [
        'features' => 'array',
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

    public function images()
    {
        return $this->hasMany(StationImages::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_station');
    }


    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class, 'stationpackages', 'station_id', 'package_id');
    }


    public function machines(): BelongsToMany
    {
        return $this->belongsToMany(Machine::class, 'stationmachines', 'station_id', 'machine_id');
    }

}

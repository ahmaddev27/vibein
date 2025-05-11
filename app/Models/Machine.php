<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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

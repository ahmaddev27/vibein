<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Cycle extends Model
{
    protected $table = 'cycles';

    protected $fillable = [
        'days',
        'name',
        'status',
    ];

    protected $cast = [
        'days' => 'array',
        'status' => 'boolean',
    ];

    protected static function booted()
    {
        static::addGlobalScope('company', function (Builder $builder) {
            $builder->where('company_id', env('DEFAULT_COMPANY_ID', 31));
        });

        static::saving(function ($model) {
            if (empty($model->company_id)) {
                $model->company_id = env('DEFAULT_COMPANY_ID', 31);
            }
        });
    }

    protected $casts = [
        'week_days' => 'json',
        'delivers_times' => 'json',
    ];

    public function packages()
    {
        return $this->belongsToMany(Package::class, 'packages_cycles', 'cycle_id', 'package_id')
            ->withPivot('price')
            ->withTimestamps();
    }

}

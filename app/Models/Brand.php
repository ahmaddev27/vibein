<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Brand extends Model
{
    protected $table = 'brand';

    protected $fillable = [
        'companyId',
        'image',
        'showStatus',
        'sortOrder',
        'updatedAt',
        'description',
        'createdAt'
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



    // Custom timestamp column names
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    public function brandTranslation()
    {
        return $this->hasMany(BrandTranslation::class, 'brandId', 'id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'brandId', 'id');
    }
}

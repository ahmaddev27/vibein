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


    public function getImageAttribute($value)
    {
        if (empty($value)) {
            return null;
        }

        // If image is already a full URL (http/https), return as-is
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        // Check if file exists in storage
        if (Storage::disk('public')->exists($value)) {
            return asset(Storage::url($value));
        }

        // Fallback to asset path (for older files or different storage)
        return asset($value);
    }

}

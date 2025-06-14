<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Onboarding extends Model
{
    protected $table = 'onbordings_app';
    protected $fillable = [
        'image',
        'description',
        'title',
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


    public function getImage()
    {
        $value = $this->image;
        if (empty($value)) {
            return null;
        }
        // Check if file exists in storage
        if (Storage::disk('public')->exists($value)) {
            return asset(Storage::url($value));
        }

        // Fallback to asset path (for older files or different storage)
        return asset($value);
    }

}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Sliders extends Model
{
    protected $fillable = ['image'];
    //
    protected $table = 'appSlider';

    protected static function booted()
    {
        static::addGlobalScope('company', function (Builder $builder) {
            $builder->where('companyId', env('DEFAULT_COMPANY_ID', 31)); // 1 كقيمة افتراضية
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


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    protected $table = 'category';

    protected $fillable = [
        'parentCategoryId',
        'companyId',
        'showStatus',
        'sortOrder',
        'image',
        'updatedAt',
        'createdAt'
    ];
    // Custom timestamp column names
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    protected static function booted()
    {
        static::addGlobalScope('company', function (Builder $builder) {
            $builder->where('companyId', 31);
        });
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'productCategories', 'categoryId', 'productId');
    }

    public function CategoryTranslations()
    {
        return $this->hasMany(CategoryTranslations::class, 'categoryId', 'id');
    }


    public function subCategories()
    {
        return $this->hasMany(Category::class, 'parentCategoryId', 'id');
    }


    public function stations()
    {
        return $this->belongsToMany(Station::class, 'category_station');
    }


}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    protected $table = 'category';

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



}

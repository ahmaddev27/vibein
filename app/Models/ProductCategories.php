<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCategories extends Model
{
    protected $table = 'productCategories';


    protected $fillable = [
        'productId',
        'subCategory',
        'categoryId',
        'createdAt',
        'updatedAt'
    ];

    // Custom timestamp column names
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    public function product()
    {
        return $this->belongsTo(Product::class, 'productId', 'id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'categoryId', 'id');
    }
}

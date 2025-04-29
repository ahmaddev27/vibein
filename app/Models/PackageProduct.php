<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageProduct extends Model
{

    protected $table= 'package_products';

    protected $fillable = [
        'package_id',
        'product_id',
        'position',
        'is_selected',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

}

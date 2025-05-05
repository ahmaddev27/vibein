<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageProductAlternative extends Model
{
    protected $table= 'package_product_alternatives';
    protected $fillable = [
        'package_product_id',
        'product_id',
        'add_on'
    ];

//    public function product()
//    {
//        return $this->belongsTo(Product::class);
//    }
//
//    public function package()
//    {
//        return $this->belongsTo(Package::class);
//    }

    public function baseProduct() {
        return $this->belongsTo(PackageProduct::class, 'package_product_id');
    }

    public function addOnProduct() {
        return $this->belongsTo(Product::class, 'product_id');
    }

}

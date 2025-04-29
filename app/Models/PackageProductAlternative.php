<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageProductAlternative extends Model
{
    protected $table= 'package_product_alternatives';
    protected $fillable = [
        'package_id',
        'product_id',
        'position',
        'is_selected',
        'add_on'
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

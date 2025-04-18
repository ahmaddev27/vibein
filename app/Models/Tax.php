<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $table = 'tax';

    public function product()
    {
        return $this->hasMany(Product::class, 'taxId', 'id');
    }

}

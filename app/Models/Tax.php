<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $table = 'tax';

    protected $fillable = [
        'name',
        'value',
        'companyId',
        'createdAt',
        'updatedAt'
    ];

    // Custom timestamp column names
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    public function product()
    {
        return $this->hasMany(Product::class, 'taxId', 'id');
    }

}

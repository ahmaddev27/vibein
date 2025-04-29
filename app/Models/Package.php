<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{

    protected $table= 'packages';
    protected $fillable = [
        'name',
        'description',
        'price',
        'total',
        'status'

    ];


    public function products(): HasMany
    {
        return $this->hasMany(PackageProduct::class);
    }

        public function alternatives(): HasMany
        {
            return $this->hasMany(PackageProductAlternative::class);
        }

    public function images(): HasMany
    {
        return $this->hasMany(PackageImages::class);
    }


}

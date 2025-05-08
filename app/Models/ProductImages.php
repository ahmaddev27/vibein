<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductImages extends Model
{
    protected $table = 'product_images';
    protected $fillable = [
        'image',
        'product_id',
    ];


}


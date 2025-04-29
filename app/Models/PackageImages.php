<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PackageImages extends Model
{
    protected $table = 'package_images';
    protected $fillable = [
        'image',
        'package_id',
    ];


}


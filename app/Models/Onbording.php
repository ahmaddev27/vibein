<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class onbording extends Model
{
    protected $table ='onbordings_app';
    protected $fillable=[
        'image',
        'description',
        'title',
    ];
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Onbording extends Model
{
    protected $table = 'onbordings_app';
    protected $fillable = [
        'image',
        'description',
        'title',
    ];

    public function getAvatar()
    {
//        if ($this->image) {
//            return url('storage/' . $this->image);
//        } else {
//            return url('blank.png');
//        }
//    }

        return  $this->image;
}
}


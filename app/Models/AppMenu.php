<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppMenu extends Model
{
    protected $table ='appMenu';
    protected $hidden=['created_at', 'updated_at','deleted_at'];

    public function children()
    {
        return $this->hasMany(AppMenu::class, 'parentId', 'id');
    }
}

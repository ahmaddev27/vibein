<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class AppMenu extends Model
{

    protected $table = 'appMenu';


    #WEHN GET ALL MENUS FOR CRM
//    protected static function booted()
//    {
//        static::addGlobalScope('appId', function (Builder $builder) {
//            $builder->where('appId', 3)->where('appGroupId', 12)->orderBy('order', 'asc')->where('deletedAt', null);
//        });
//    }


    #WEHN GET ALL MENUS FOR VIBIN

    protected static function booted()
    {
        static::addGlobalScope('appId', function (Builder $builder) {
            $builder->where('appId', 3)->where('appGroupId',18)->orderBy('order', 'asc')->where('deletedAt',null);
        });
    }

    #WEHN GET ALL MENUS FOR APP

//    protected static function booted()
//    {
//        static::addGlobalScope('appId', function (Builder $builder) {
//            $builder->where('appId', 3)->orderBy('order', 'asc')->where('deletedAt',null);
//        });
//    }


    public function children()
    {
        return $this->hasMany(AppMenu::class, 'parentId', 'id');
    }
}

<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AppMenu;
use Illuminate\Support\Facades\Hash;


class MenuController extends Controller
{
    use ApiResponseTrait;

    public function crmMenu()
    {


        $menu = AppMenu::where('parentId', null)->with(['children' => function ($query) {
            $query->with('children');
        }])->get()->toArray();


        return $this->apiRespose(
            $menu,
            'Menu retrieved successfully',
            true,
            200);
    }


}

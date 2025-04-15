<?php

namespace App\Http\Controllers\mobile;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\OnbordingResources;
use App\Models\Onbording;
class HomeController extends Controller
{
    use ApiResponseTrait;

    public function onbording(){

        return $this->apiRespose(
            OnbordingResources::collection(Onbording::orderBy('id')->get()),'successfully', true, 200);
    }

}

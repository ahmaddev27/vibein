<?php

namespace App\Http\Controllers\mobile;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\mobile\CategoryResource;
use App\Http\Resources\OnboardingResources;
use App\Http\Resources\SliderResources;
use App\Models\Category;
use App\Models\Onboarding;
use App\Models\Sliders;

class HomeController extends Controller
{
    use ApiResponseTrait;

    public function onbording()
    {

        return $this->apiRespose(
            OnboardingResources::collection(Onboarding::orderBy('id')->get()), 'successfully', true, 200);
    }


    public function sliders()
    {

        return $this->apiRespose(
            SliderResources::collection(Sliders::orderBy('id')->get()), 'successfully', true, 200);
    }

    public function categories()
    {

        return $this->apiRespose(
            CategoryResource::collection(Category::orderBy('id')->get()), 'successfully', true, 200);
    }

}

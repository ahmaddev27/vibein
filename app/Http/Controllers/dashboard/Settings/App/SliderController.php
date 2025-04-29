<?php

namespace App\Http\Controllers\dashboard\Settings\App;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\SliderResources;
use App\Models\Sliders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        return $this->apiResponse(
            SliderResources::collection(Sliders::orderBy('id')->get()), 'successfully', true, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $slider = new Sliders();
        $slider->image = $request->file('image')->store('sliders', 'public');
        $slider->save();

        return $this->apiResponse(
            new SliderResources($slider),
            'Slider created successfully',
            true,
            201
        );

    }

    public function update(Request $request, $id)
    {
        // Validate request
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);


        // Find the slider or return error
        $slider = Sliders::find($id);

        if (!$slider) {
            return $this->apiResponse(null, 'Slider not found', false, 404);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($slider->image && file_exists(public_path('storage/' . $slider->image))) {
                @unlink(public_path('storage/' . $slider->image));
            }

            // Store new image
            $slider->image = $request->file('image')->store('sliders', 'public');
        }

        // Save updated slider
        $slider->save();

        // Return response
        return $this->apiResponse(
            new SliderResources($slider),
            'Slider updated successfully',
            true,
            200
        );
    }

    public function show($id)
    {

        $slider = Sliders::find($id);
        if (!$slider) {
            return $this->apiResponse(null, 'Slider not found', false, 404);
        }
        return $this->apiResponse(
            new SliderResources($slider),
            'Slider retrieved successfully',
            true,
            200
        );
    }

    public function destroy($id)
    {
        $slider = Sliders::findOrFail($id);

        if ($slider->image) {
            if ($slider->image && Storage::disk('public')->exists($slider->image)) {
                Storage::disk('public')->delete($slider->image);
            }
        }


        $slider->delete();

        return $this->apiResponse(
            null,
            'Slider deleted successfully',
            true,
            200
        );
    }

}

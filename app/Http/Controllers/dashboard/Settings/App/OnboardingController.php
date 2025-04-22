<?php

namespace App\Http\Controllers\dashboard\Settings\App;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\OnboardingResources;
use App\Models\Onboarding;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        return $this->apiResponse(
            OnboardingResources::collection(Onboarding::orderBy('id')->get()), 'successfully', true, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif',
            'title'=> 'required|string|max:255',
            'description'=> 'required|string|max:400',
        ]);



        $slider = new Onboarding();
        $slider->image = $request->file('image')->store('sliders', 'public');
        $slider->title = $request->input('title');
        $slider->description = $request->input('description');
        $slider->save();

        return $this->apiResponse(
            new OnboardingResources($slider),
            'Created successfully',
            true,
            201
        );

    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'title'=> 'required|string|max:255',
            'description'=> 'required|string|max:400',
        ]);


        // Find the slider or return error
        $onbording = Onboarding::find($id);

        if (!$onbording) {
            return $this->apiResponse(null, 'Onboarding not found', false, 404);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($onbording->image && file_exists(public_path('storage/' . $onbording->image))) {
                @unlink(public_path('storage/' . $onbording->image));
            }

            // Store new image
            $onbording->image = $request->file('image')->store('sliders', 'public');
        }

        // Update other fields
        $onbording->title = $request->input('title', $onbording->title);
        $onbording->description = $request->input('description', $onbording->description);
        // Save updated slider
        $onbording->save();

        // Return response
        return $this->apiResponse(
            new OnboardingResources($onbording),
            'Updated successfully',
            true,
            200
        );
    }


    public function destroy($id)
    {
        $onbording = Onboarding::findOrFail($id);

        if ($onbording->image) {
            $oldImagePath = public_path('storage/' . $onbording->image);
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }

        $onbording->delete();

        return $this->apiResponse(
            null,
            'Deleted successfully',
            true,
            200
        );
    }

}

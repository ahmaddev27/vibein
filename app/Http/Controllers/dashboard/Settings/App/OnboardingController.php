<?php

namespace App\Http\Controllers\dashboard\Settings\App;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\OnboardingResources;
use App\Models\Onboarding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:400',
        ]);


        $slider = new Onboarding();
        $slider->image = $request->file('image')->store('onboarding', 'public');
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
        // Validate the request
        $validated = $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:400',
        ]);

        // Find the onboarding record
        $onboarding = Onboarding::find($id);

        if (!$onboarding) {
            return $this->apiResponse(null, 'Onboarding not found', false, 404);
        }

        try {
            // Handle image upload if file exists
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                // Delete old image if exists
                if ($onboarding->image) {
                    Storage::disk('public')->delete($onboarding->image);
                }

                // Store new image
                $onboarding->image = $request->file('image')->store('onboarding', 'public');
            }

            // Update other fields
            $onboarding->title = $validated['title'];
            $onboarding->description = $validated['description'];

            // Save the updated record
            $onboarding->save();

            return $this->apiResponse(
                new OnboardingResources($onboarding),
                'Updated successfully',
                true,
                200
            );
        } catch (\Exception $e) {
            Log::error('Onboarding update failed: ' . $e->getMessage());

            return $this->apiResponse(null, 'Failed to update onboarding.', false, 500);
        }
    }
    public function show($id)
    {
        $onbording = Onboarding::find($id);

        if (!$onbording) {
            return $this->apiResponse(null, 'Onboarding not found', false, 404);
        }

        return $this->apiResponse(
            new OnboardingResources($onbording),
            'successfully',
            true,
            200
        );

    }

    public function destroy($id)
    {
        $onbording = Onboarding::findOrFail($id);

        if ($onbording->image && Storage::disk('public')->exists($onbording->image)) {
            Storage::disk('public')->delete($onbording->image);
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

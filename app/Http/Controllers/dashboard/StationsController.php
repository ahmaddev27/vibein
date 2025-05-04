<?php

namespace App\Http\Controllers\dashboard;


use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\StationRequest;
use App\Http\Resources\StationResource;
use App\Models\Station;
use App\Models\StationImages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class StationsController extends Controller
{

    use ApiResponseTrait;

    public function index(Request $request)
    {
        try {

            $query = Station::with('images');

            if ($request->has('search')) {
                $search = $request->search;
                $search_by = $request->get('search_by', 'name');
                $query->where($search_by, 'like', "%{$search}%");
            }

            // Apply sorting
            $sortField = $request->get('sort_by', 'created_at');
            $sortDirection = $request->get('sort_dir', 'desc');
            $query->orderBy($sortField, $sortDirection);

            if ($request->has('category_id')) {
                $categoryId = $request->category_id;
                $query->whereHas('categories', function ($q) use ($categoryId) {
                    $q->where('category_id', $categoryId);
                });
            }

            // Paginate results
            $perPage = $request->input('per_page', 10);
            $stations = $query->paginate($perPage);
            if ($stations->isEmpty()) {
                return $this->apiRespose(
                    null,
                    'No Stations found',
                    true,
                    200
                );
            }


            return $this->ApiResponsePaginationTrait(
                StationResource::collection($stations),
                'Stations retrieved successfully',
                true,
                200
            );


        } catch (\Exception $e) {
            return $this->apiResponse(
                null,
                'Failed to retrieve  Stations: ' . $e->getMessage(),
                false,
                500
            );
        }
    }

    public function show($id)
    {
        try {
            $station = Station::with('images')->findOrFail($id);
            return $this->apiResponse(
                new StationResource($station),
                'Station retrieved successfully',
                true,
                200
            );
        } catch (\Exception $e) {
            return $this->apiResponse(
                null,
                'Failed to retrieve station: ' . $e->getMessage(),
                false,
                500
            );
        }
    }

    public function store(StationRequest $request)
    {
        DB::beginTransaction();

        try {
            // إنشاء المحطة
            $station = Station::create([
                'name' => $request->name,
                'description' => $request->description,
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'sort_order' => $request->sort_order,
                'is_recommended' => $request->is_recommended,
                'features' => $request->features,
            ]);

            $station->categories()->attach($request->category_ids);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('stations', 'public');
                    $station->images()->create([
                        'image' => $path,
                    ]);
                }
            }

            DB::commit();

            return $this->apiResponse(
                new StationResource($station),
                'Station created successfully',
                true,
                201
            );
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to create station', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->apiResponse(
                null,
                'Failed to create station: ' . $e->getMessage(),
                false,
                500
            );
        }
    }

    public function update(StationRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            $station = Station::find($id);
            if (!$station) {
                return $this->apiResponse(
                    null,
                    'Station not found',
                    false,
                    404
                );
            }

            $station->update([
                'name' => $request->name,
                'description' => $request->description,
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'sort_order' => $request->sort_order,
                'is_recommended' => $request->is_recommended,
                'features' => $request->features,
            ]);

            // تحديث التصنيفات المرتبطة
            $station->categories()->sync($request->category_ids);

            // حفظ الصور
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('stations', 'public');
                    $station->images()->create([
                        'image' => $path,
                    ]);
                }
            }

            DB::commit();

            return $this->apiResponse(
                new StationResource($station->load('categories')),
                'Station updated successfully',
                true,
                200
            );
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to update station', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->apiResponse(
                null,
                'Failed to update station: ' . $e->getMessage(),
                false,
                500
            );
        }
    }

    public function destroy($id)
    {
        $station = Station::find($id);
        if (!$station) {
            return $this->apiResponse(
                null,
                'Station not found',
                false,
                404
            );
        }

        DB::beginTransaction();
        try {
            // Delete associated images
            if ($station->images) {
                foreach ($station->images as $image) {

                    if ($image->image) {
                        Storage::disk('public')->delete($image->image);
                    }

                    $image->delete();
                }
            }

            // Delete the station
            $station->delete();

            DB::commit();

            return $this->apiResponse(
                null,
                'Station deleted successfully',
                true,
                200
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->apiResponse(
                null,
                'Failed to delete station: ' . $e->getMessage(),
                false,
                500
            );
        }

    }

    public function deleteImage($id)
    {
        $image = StationImages::find($id);
        if (!$image) {
            return $this->apiRespose(
                null,
                'Image not found',
                false,
                404
            );
        }

        if ($image->image) {
            Storage::disk('public')->delete($image->image);
        }

        $image->delete();

        return $this->apiRespose(
            null,
            'Image Deleted successfully',
            true,
            200
        );

    }


}

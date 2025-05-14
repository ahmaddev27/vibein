<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBrandRequest;
use App\Http\Resources\dashboard\BrandResource;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        try {

            $query = Brand::with('brandTranslation');

            if ($request->has('status')) {
                $query->where('showStatus', $request->status);
            }


            if ($request->has('search')) {

                $search = $request->search;
                $search_by = $request->get('search_by', 'name');

                $query->whereHas('brandTranslation', function ($q) use ($search, $search_by) {
                    $q->where($search_by, 'like', "%{$search}%");
                });
            }


            // Apply sorting
            $sortField = $request->get('sort_by', 'createdAt');
            $sortDirection = $request->get('sort_dir', 'desc');
            $query->orderBy($sortField, $sortDirection);


            // Paginate results
            $perPage = $request->input('per_page', 10);
            $brands = $query->paginate($perPage);
            if ($brands->isEmpty()) {
                return $this->apiRespose(
                    null,
                    'No Brands found',
                    true,
                    200
                );
            }

            return $this->ApiResponsePaginationTrait(
               BrandResource::collection( $brands),
                'Brands retrieved successfully',
                true,
                200
            );


        } catch (\Exception $e) {
            return $this->apiResponse(
                null,
                'Failed to retrieve brands: ' . $e->getMessage(),
                false,
                500
            );
        }
    }

    public function show($id)
    {
        $brand = Brand::with('brandTranslation')->find($id);
        if (!$brand) {
            return $this->apiResponse(
                null,
                'Brand not found',
                false,
                404
            );
        }

        return $this->apiResponse(
            $brand,
            'Brand retrieved successfully',
            true,
            200
        );

    }

    public function store(StoreBrandRequest $request)
    {
        DB::beginTransaction();

        try {
            $brandData = [
                'companyId' => $request->companyId,
                'showStatus' => 1,
                'sortOrder' => $request->sortOrder ?? 0,
            ];

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('brands', 'public');
                $brandData['image'] = $imagePath;
            }

            $brand = Brand::create($brandData);

            $brand->brandTranslation()->create([
                'name' => $request->name,
                'description' => $request->description,
                'metaTagTitle' => $request->metaTagTitle,
                'metaTagDescription' => $request->metaTagDescription,
                'metaTagKeywords' => $request->metaTagKeywords,
                'languageCode' => $request->languageCode,
                'brandId' => $brand->id
            ]);

            DB::commit();

            return $this->apiResponse(
                new BrandResource($brand->load('brandTranslation')),
                'Brand created successfully',
                true,
                201
            );

        } catch (\Exception $e) {
            DB::rollBack();

            return $this->apiResponse(
                null,
                'Failed to create brand: ' . $e->getMessage(),
                false,
                500
            );
        }
    }

    public function update(StoreBrandRequest $request, $id)
    {

        $brand = Brand::find($id);
        if (!$brand) {
            return $this->apiResponse(
                null,
                'Brand not found',
                false,
                404
            );
        }

        DB::beginTransaction();
        try {
            $brandData = [
                'companyId' => $request->companyId,
                'showStatus' => 1,
                'sortOrder' => $request->sortOrder ?? 0,
            ];

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('brands', 'public');
                $brandData['image'] = $imagePath;
            }

            $brand->update($brandData);

            $brand->brandTranslation()->update([
                'name' => $request->name,
                'description' => $request->description,
                'metaTagTitle' => $request->metaTagTitle,
                'metaTagDescription' => $request->metaTagDescription,
                'metaTagKeywords' => $request->metaTagKeywords,
                'languageCode' => $request->languageCode,
            ]);

            DB::commit();

            return $this->apiResponse(
                new BrandResource($brand->load('brandTranslation')),
                'Brand updated successfully',
                true,
                200
            );

        } catch (\Exception $e) {
            DB::rollBack();

            return $this->apiResponse(
                null,
                'Failed to update brand: ' . $e->getMessage(),
                false,
                500
            );
        }

    }

    public function destroy($id)
    {

        $brand = Brand::find($id);
        if (!$brand) {
            return $this->apiResponse(
                null,
                'Brand not found',
                false,
                404
            );
        }

        // Delete the brand
        $brand->delete();

        return $this->apiResponse(
            null,
            'Brand deleted successfully',
            true,
            200
        );


    }


    public function deleteImage($id)
    {

        try {
            $brand = Brand::find($id);
            if (!$brand) {
                return $this->apiResponse(
                    null,
                    'Brand not found',
                    false,
                    404
                );
            }

            // Delete the category image if it exists
            if ($brand->image) {
                Storage::disk('public')->delete($brand->image);
                $brand->image = null;
                $brand->save();
            }

            return $this->apiResponse(
                null,
                'Image deleted successfully',
                true,
                200
            );


        }catch (\Exception $e) {
            Log::error('Image deletion error: ' . $e->getMessage(), [
                'exception' => $e,
                'id' => $id
            ]);

            return $this->apiResponse(
                null,
                'Error deleting image',
                false,
                500
            );
        }


    }

}

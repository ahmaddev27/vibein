<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\PackageRequest;
use App\Http\Resources\PackegeResource;
use App\Models\Package;
use App\Models\PackageImages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PackageController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        try {
            $query = Package::with(['products.product.productTranslations', 'alternatives.product.productTranslations', 'images'])->orderBy('id', 'desc');
            $perPage = $request->input('per_page', 10);
            $packages = $query->paginate($perPage);


            if ($packages->isEmpty()) {
                return $this->ApiResponsePaginationTrait(
                    PackegeResource::collection($packages),
                    'No Packages found',
                    true,
                    200
                );
            }

            return $this->ApiResponsePaginationTrait(
                PackegeResource::collection($packages),
                'Packages retrieved successfully',
                true,
                200
            );
        } catch (\Exception $e) {
            // Log the error with stack trace
            Log::error('Packages index error: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->apiRespose(
                ['error' => 'Server error occurred'],
                'Error retrieving Packages',
                false,
                500
            );
        }
    }


    public function store(PackageRequest $request)
    {


        DB::beginTransaction();

        try {
            $package = Package::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'total' => $request->price,
                'status' => $request->input('status', 'Active'),
                'tags' => $request->input('tags'),
            ]);

            foreach ($request->input('products', []) as $product) {
                $package->products()->create([
                    'product_id' => $product['product_id'],
                    'position' => $product['position'],
                    'is_selected' => $product['is_selected'],
                ]);
            }


            $addOnTotal = 0;
            foreach ($request->input('alternatives', []) as $alt) {
                $addOnTotal += floatval($alt['add_on']);
                $package->alternatives()->create([
                    'product_id' => $alt['product_id'],
                    'position' => $alt['position'],
                    'is_selected' => $alt['is_selected'],
                    'add_on' => $alt['add_on'],
                ]);
            }

            $package->update([
                'total' => $package->price + $addOnTotal,
            ]);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('packages', 'public');
                    $package->images()->create([
                        'image' => $path,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Package created successfully',
                'data' => new PackegeResource($package->load(['products.product', 'alternatives.product', 'images']))
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error creating package',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function show($id)
    {
        $package = Package::with(['products.product.productTranslations', 'alternatives.product.productTranslations', 'images'])->find($id);

        if (!$package) {
            return $this->apiRespose(
                null,
                'Package not found',
                false,
                404
            );
        }

        return $this->apiRespose(
            new PackegeResource($package),
            'Package retrieved successfully',
            true,
            200
        );
    }

    public function update(PackageRequest $request, $id)
    {

        DB::beginTransaction();

        try {
            $package = Package::find($id);
            if (!$package) {
                return $this->apiRespose(
                    null,
                    'Package not found',
                    false,
                    404
                );
            }

            $package->update([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'total' => $request->price,
                'tags' => $request->tags,
            ]);

            $package->products()->delete();
            foreach ($request->input('products', []) as $product) {
                $package->products()->create([
                    'product_id' => $product['product_id'],
                    'position' => $product['position'],
                    'is_selected' => $product['is_selected'],
                ]);
            }

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $package->images()->create([
                        'image' => $image->store('package', 'public'),
                    ]);
                }
            }

            $addOnTotal = 0;
            $package->alternatives()->delete();

            foreach ($request->input('alternatives', []) as $alt) {
                $addOnTotal += floatval($alt['add_on']); // accumulate add_on value
                $package->alternatives()->create([
                    'product_id' => $alt['product_id'],
                    'position' => $alt['position'],
                    'is_selected' => $alt['is_selected'],
                    'add_on' => $alt['add_on'],
                ]);
            }

            // Now update the total
            $package->update([
                'total' => $package->price + $addOnTotal,
            ]);

            DB::commit();

            return $this->apiRespose(
                new PackegeResource($package),
                'Package updated successfully',
                true,
                200
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->apiRespose(
                null,
                'Error updating package',
                false,
                500
            );
        }

    }


    public function destroy($id)
    {

        $package = Package::find($id);


        if (!$package) {
            return $this->apiRespose(
                null,
                'Package not found',
                false,
                404
            );
        }

        if ($package->images) {
            foreach ($package->images as $image) {

                if ($image->image) {
                    Storage::disk('public')->delete($image->image);
                }

                $image->delete();
            }
        }


        $package->delete();

        return $this->apiRespose(
            null,
            'Package deleted successfully',
            true,
            200
        );
    }


    public function deleteImage($id)
    {
        $image = PackageImages::find($id);
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


    public function customize(Request $request, Package $package)
    {


        $request->validate([
            'position' => 'required|integer|min:1',
            'product_alternative_id' => 'required|exists:package_product_alternatives,id',
        ]);

        try {
            $result = $package->toggleSelectedProduct(
                $request->position,
                $request->product_alternative_id,
            );

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'selected product updated successfully',
                    'data' => new PackegeResource($package->fresh(['products.product', 'alternatives.product', 'images']))
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to update selected product',
            ], 404);
        } catch (\Exception $e) {

            Log::error('Failed to customize package product: ' . $e->getMessage(), [
                'exception' => $e,
                'package_id' => $package->id,
                'position' => $request->position,
                'product_alternative_id' => $request->product_alternative_id,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}

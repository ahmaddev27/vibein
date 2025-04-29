<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\PackegeResource;
use App\Models\Package;
use App\Models\PackageImages;
use Illuminate\Http\Request;
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


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
//            'total' => 'required|numeric',
            'products' => 'required|array',
            'alternatives' => 'required|array',
            'images' => 'required|array',
        ]);


        $package = Package::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'total' => $request->price,
        ]);

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


//        foreach ($request->images as $image) {
//
//        }

        return $this->apiRespose(
            new PackegeResource($package),
            'Packages Created successfully',
            true,
            200
        );
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

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'products' => 'required|array',
            'alternatives' => 'required|array',
            'images' => 'array',
        ]);

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

        return $this->apiRespose(
            new PackegeResource($package),
            'Package updated successfully',
            true,
            200
        );

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

}

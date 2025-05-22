<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\PackageRequest;
use App\Http\Resources\PackageResource;
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
            $query = Package::with(['products.product.productTranslations', 'images'])->orderBy('id', 'desc');
            $perPage = $request->input('per_page', 10);
            $packages = $query->paginate($perPage);


            if ($packages->isEmpty()) {
                return $this->apiRespose(
                    null,
                    'No Packages found',
                    true,
                    200
                );
            }

            return $this->ApiResponsePaginationTrait(
                PackageResource::collection($packages),
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
        $data = $request->validated();

        DB::beginTransaction();

        try {
            // 2. إنشاء السجل الرئيسي للباكيج
            $package = Package::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'price' => $data['price'],
                'total' => $data['total'] ?? 0,
                'status' => 'Active',
                'tags' => $data['tags'] ?? null,
            ]);


            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('packages', 'public');
                    $package->images()->create(['image' => $path]);
                }
            }

            // 3. إضافة كل منتج داخل الباكيج
            foreach ($data['products'] as $prod) {
                $packageProduct = $package->products()->create([
                    'product_id' => $prod['product_id'],
                ]);

                // 4. إضافة البدائل (الإضافات) لهذا المنتج إن وجدت
                if (!empty($prod['alternatives'])) {
                    foreach ($prod['alternatives'] as $alt) {
                        $packageProduct->alternatives()->create([
                            'product_id' => $alt['product_id'],
                            'add_on' => $alt['add_on'], // السعر الإضافي
                        ]);
                    }
                }
            }


            DB::commit();

            return $this->apiRespose(
                new PackageResource($package),
                'Package Created successfully',
                true,
                201
            );


        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error Creating : ' . $e->getMessage(),
            ], 500);
        }
    }


    public function show($id)
    {
        $package = Package::with(['products.product.productTranslations', 'images'])->find($id);

        if (!$package) {
            return $this->apiRespose(
                null,
                'Package not found',
                false,
                404
            );
        }

        return $this->apiRespose(
            new PackageResource($package),
            'Package retrieved successfully',
            true,
            200
        );
    }


    public function update(PackageRequest $request, $id)
    {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            // 2. تحميل الباكيج الموجود وتحديثه
            $package = Package::findOrFail($id);
            $package->update([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'price' => $data['price'],
                'tags' => $data['tags'] ?? null,
            ]);


            $package->products()->each(function ($pp) {
                $pp->alternatives()->delete();
            });
            $package->products()->delete();

            // 4. إعادة إنشاء المنتجات والبدائل من جديد
            foreach ($data['products'] as $prod) {
                $pp = $package->products()->create([
                    'product_id' => $prod['product_id'],
                ]);

                if (!empty($prod['images'])) {
                    foreach ($prod['images'] as $image) {
                        $path = $image->store('packages', 'public');
                        $pp->images()->create(['image' => $path]);
                    }
                }

                if (!empty($prod['alternatives'])) {
                    foreach ($prod['alternatives'] as $alt) {
                        $pp->alternatives()->create([
                            'product_id' => $alt['product_id'],
                            'position' => $alt['position'] ?? null,
                            'is_selected' => $alt['is_selected'] ?? false,
                            'add_on' => $alt['add_on'],
                        ]);
                    }
                }
            }

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('packages', 'public');
                    $package->images()->create(['image' => $path]);
                }
            }

            DB::commit();

            return $this->apiRespose(
                new PackageResource($package->load('products.alternatives')),
                'Package updated successfully',
                true,
                200
            );

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error Updating: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function destroy($id)
    {
        $package = Package::with('products.alternatives', 'images')->find($id);

        if (!$package) {
            return $this->apiRespose(
                null,
                'Package not found',
                false,
                404
            );
        }


        $hasStations = DB::table('stationpackages')->where('package_id', $id)->exists();
        if ($hasStations) {
            return $this->apiRespose(
                null,
                'Package cannot be deleted because it is associated with stations',
                false,
                400
            );
        }


        foreach ($package->products as $product) {
            foreach ($product->alternatives as $alternative) {
                $alternative->delete();
            }
            $product->delete();
        }


        // Delete package images
        foreach ($package->images as $image) {
            if ($image->image) {
                Storage::disk('public')->delete($image->image);
            }
            $image->delete();
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

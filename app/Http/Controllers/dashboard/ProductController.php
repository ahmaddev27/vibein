<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Resources\dashboard\ProductResource;
use App\Models\Product;
use App\Models\ProductCategories;
use App\Models\ProductImages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        try {
            $query = Product::select('id', 'status') // تحديد الأعمدة
            ->with([
                'images:id,product_id,image',
                'categories:id',
                'categories.CategoryTranslations',
                'productVariants',
                'productTranslations',
                'Brand',
                'Brand.brandTranslation',
            ]);

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $search_by = $request->get('search_by', 'name');
                $query->whereHas('productTranslations', function ($q) use ($search, $search_by) {
                    $q->where($search_by, 'ilike', "%{$search}%");
                });
            }

            if ($request->filled('category_id')) {
                $query->whereHas('categories', function ($q) use ($request) {
                    $q->where('categoryId', $request->category_id);
                });
            }

            $sortField = $request->get('sort_by', 'createdAt');
            $sortDirection = $request->get('sort_dir', 'desc');
            $query->orderBy($sortField, $sortDirection);

            $perPage = $request->input('per_page', 10);


                 $products=$query->paginate($perPage);

            if ($products->isEmpty()) {
                return $this->apiRespose(null, 'No products found', true, 200);
            }

            return $this->ApiResponsePaginationTrait(
                ProductResource::collection($products),
                'Products retrieved successfully',
                true,
                200
            );
        } catch (\Exception $e) {
            Log::error('Product index error: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->apiRespose(['error' => 'Server error occurred'], 'Error retrieving products', false, 500);
        }
    }

    public function store(StoreProductRequest $request)
    {
        return DB::transaction(function () use ($request) {

            // Create Product
            $product = Product::create([
                'brandId' => $request->brandId,
                'lable' => $request->label,
                'status' => 'Active', // Default status
            ]);

            // Create Translation
            $product->productTranslations()->create([
                'languageCode' => 'en', // Default language
                'name' => $request->name,
                'description' => $request->description,
                'tags' => $request->tags,
                'metaTagTitle' => $request->metaTagTitle
            ]);

            $variants = $product->ProductVariants()->create([
                'productId' => $product->id,
//                'quantity' => $request->quantity,
                'status' => 'Active', // Default status
                'createdAt' => now(),
                'updatedAt' => now()
            ]);

            // Handle Image Uploads
            if ($request->hasFile('images') && count($request->file('images')) > 0) {
                foreach ($request->file('images') as $image) {
                    ProductImages::create([
                        'image' => $image->store('products', 'public'),
                        'product_id' => $product->id
                    ]);
                }
            }


            if ($request->has('prices') && count($request->prices) > 0) {
                $prices = [];

                foreach ($request->prices as $key => $price) {
                    $prices[] = [
                        'id' => $key + 1,
                        'weight' => $price['weight'],
                        'price' => $price['price'],
                        'quantity' => $price['quantity']
                    ];
                }

                $variants->update([
                    'prices' => $prices // Laravel will automatically cast this to JSON
                ]);
            }


            if ($request->has('category_id') && count($request->category_id) > 0) {
                // Attach Categories
                foreach ($request->category_id as $categoryId) {
                    ProductCategories::create([
                        'productId' => $product->id,
                        'categoryId' => $categoryId,
                        'subCategory' => false
                    ]);
                }

            }

//
//            if ($request->has('sub_category_id') && count($request->sub_category_id) > 0) {
//
//                // Attach Categories
//                foreach ($request->sub_category_id as $scategoryId) {
//                    ProductCategories::create([
//                        'productId' => $product->id,
//                        'categoryId' => $scategoryId,
//                        'subCategory' => true
//                    ]);
//                }
//            }


            return $this->apiResponse(
                new ProductResource($product),

                'Product created successfully',
                true,
                201
            );

        });
    }


    public function show($id)
    {


        $product = Product::with([
            'images',
            'categories.CategoryTranslations',
            'Subcategories.CategoryTranslations',
            'productVariants',
            'productTranslations',
            'productTax',
            'Brand.brandTranslation'
        ])->find($id);


        if (!$product) {
            return $this->apiResponse(
                null,
                'Product not found',
                false,
                404
            );
        }

        return $this->apiResponse(
            new ProductResource($product),

            'Product retrieved successfully',
            true,
            200
        );
    }

    public function update(StoreProductRequest $request, $id)
    {

        try {

            DB::beginTransaction();

            // Fetch the Product
            $product = Product::find($id);

            // Update Product
            $product->update([
                'brandId' => $request->brandId,
                'lable' => $request->label,
                'status' => $request->status ?? 'Active', // Use provided status or default
            ]);

            // Update or Create Translation
            $translation = $product->productTranslations()->where('languageCode', 'en')->first();
            if ($translation) {
                $translation->update([
                    'name' => $request->name,
                    'description' => $request->description,
                    'tags' => $request->tags,
                    'metaTagTitle' => $request->metaTagTitle
                ]);
            } else {
                $product->productTranslations()->create([
                    'languageCode' => 'en',
                    'name' => $request->name,
                    'description' => $request->description,
                    'tags' => $request->tags,
                    'metaTagTitle' => $request->metaTagTitle
                ]);
            }

            // Update Variant
            $variant = $product->ProductVariants()->first();
            if ($variant) {
                $variant->update([
//                    'quantity' => $request->quantity,
                    'status' => $request->status ?? 'Active',
                    'updatedAt' => now()
                ]);
            }

            // Handle Image Uploads
            if ($request->hasFile('images') && count($request->file('images')) > 0) {
                foreach ($request->file('images') as $image) {
                    ProductImages::create([
                        'image' => $image->store('products', 'public'),
                        'product_id' => $product->id
                    ]);
                }
            }

            // Update Prices
            if ($request->has('prices') && count($request->prices) > 0) {
                $prices = [];
                foreach ($request->prices as $key => $price) {
                    $prices[] = [
                        'id' => $key + 1,
                        'weight' => $price['weight'],
                        'price' => $price['price'],
                        'quantity' => $price['quantity']
                    ];
                }

                $variant->update([
                    'prices' => $prices // JSON casting
                ]);
            }

            // Sync Categories
            ProductCategories::where('productId', $product->id)->delete();

            if ($request->has('category_id') && count($request->category_id) > 0) {
                foreach ($request->category_id as $categoryId) {
                    ProductCategories::create([
                        'productId' => $product->id,
                        'categoryId' => $categoryId,
                        'subCategory' => false
                    ]);
                }
            }

//            if ($request->has('sub_category_id') && count($request->sub_category_id) > 0) {
//                foreach ($request->sub_category_id as $scategoryId) {
//                    ProductCategories::create([
//                        'productId' => $product->id,
//                        'categoryId' => $scategoryId,
//                        'subCategory' => true
//                    ]);
//                }
//            }

            DB::commit();
            return $this->apiResponse(
                new ProductResource($product),
                'Product updated successfully',
                true,
                200
            );


        } catch (\Exception $e) {

            DB::rollBack();
            Log::error('Product not found: ' . $e->getMessage(), [
                'exception' => $e,
                'id' => $id
            ]);

            return $this->apiResponse(
                null,
                'Error updating product',
                false,
                404
            );
        }
    }

    public function destroy($id)
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return $this->apiResponse(
                    null,
                    'Product not found',
                    false,
                    404
                );
            }

            $isInPackage = DB::table('package_products')->where('product_id', $id)->exists();
            $isAlternative = DB::table('package_product_alternatives')->where('product_id', $id)->exists();

            if ($isInPackage || $isAlternative) {
                return $this->apiResponse(
                    null,
                    'Product cannot be deleted as it is part of a package product or an alternative.',
                    false,
                    400
                );
            }

            foreach ($product->images as $image) {
                if (Storage::disk('public')->exists($image->image)) {
                    Storage::disk('public')->delete($image->image);
                }
            }

            $product->delete();

            return $this->apiResponse(
                null,
                'Product deleted successfully',
                true,
                200
            );
        } catch (\Exception $e) {
            Log::error('Product deletion error: ' . $e->getMessage(), [
                'exception' => $e,
                'id' => $id
            ]);

            return $this->apiResponse(
                null,
                'Error deleting  Product deletion error: ' . $e->getMessage(),
                false,
                500
            );
        }
    }

    public function deleteImage($imageId)
    {
        try {
            $image = ProductImages::findOrFail($imageId);

            // حذف الملف من التخزين
            if (Storage::disk('public')->exists($image->image)) {
                Storage::disk('public')->delete($image->image);
            }

            // حذف السجل من قاعدة البيانات
            $image->delete();

            return $this->apiResponse(
                null,
                'Image deleted successfully',
                true,
                200
            );
        } catch (\Exception $e) {
            Log::error('Image deletion error: ' . $e->getMessage(), [
                'exception' => $e,
                'id' => $imageId
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


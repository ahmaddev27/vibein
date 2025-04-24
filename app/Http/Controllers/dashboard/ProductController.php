<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use App\Models\ProductCategories;
use App\Models\ProductVariants;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        try {
            $query = Product::with([
                'categories.CategoryTranslations',
                'Subcategories.CategoryTranslations',
                'productVariants',
                'productTranslations',
                'productTax',
                'Brand.brandTranslation'
            ])->orderBy('id', 'desc');


            $perPage = $request->input('per_page', 10);
            $products = $query->paginate($perPage);

            if ($products->isEmpty()) {
                return $this->ApiResponsePaginationTrait(
                    $products,
                    'No products found',
                    true,
                    200
                );
            }

            return $this->ApiResponsePaginationTrait(
                $products,
                'Products retrieved successfully',
                true,
                200
            );
        } catch (\Exception $e) {
            // Log the error with stack trace
            Log::error('Product index error: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->apiRespose(
                ['error' => 'Server error occurred'],
                'Error retrieving products',
                false,
                500
            );
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
                'companyId' => 31 // From global scope
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
                'quantity' => $request->quantity,
                'status' => 'Active', // Default status
                'createdAt' => now(),
                'updatedAt' => now()
            ]);

// Handle Image Uploads
            $imagePaths = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('products', 'public');
                    $imagePaths[] = "'" . addslashes($path) . "'"; // Use single quotes for string literals
                }

                // Construct the array literal
                $arrayLiteral = "ARRAY[" . implode(',', $imagePaths) . "]";
                Log::info("Generated array literal: " . $arrayLiteral); // Log the array literal

// Use DB::raw to construct the array literal
                DB::table('productVariant')
                    ->where('id', $variants->id)
                    ->update([
                        'images' => DB::raw($arrayLiteral),
                        'updatedAt' => now(),
                    ]);
            }



            if ($request->has('prices')) {
                $prices = [];

                foreach ($request->prices as $price) {
                    $prices[] = [
                        'weight' => $price['weight'],
                        'price' => $price['price']
                    ];
                }

                $variants->update([
                    'prices' => $prices // Laravel will automatically cast this to JSON
                ]);
            }


            // Attach Categories
            foreach ($request->category_id as $categoryId) {
                ProductCategories::create([
                    'productId' => $product->id,
                    'categoryId' => $categoryId,
                    'subCategory' => false
                ]);
            }


            // Attach Categories
            foreach ($request->sub_category_id as $scategoryId) {
                ProductCategories::create([
                    'productId' => $product->id,
                    'categoryId' => $scategoryId,
                    'subCategory' => true
                ]);
            }


            return $this->apiResponse(
                $product->load('ProductVariants','productTranslations'),
                'Product created successfully',
                true,
                201
            );

        });
    }


}


<?php

namespace App\Http\Controllers\mobile;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\mobile\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {

        try {
            $query = Product::with([
                'images',
                'categories.CategoryTranslations',
                'Subcategories.CategoryTranslations',
                'productVariants',
                'productTranslations',
                'productTax',
                'Brand.brandTranslation',

            ])->where('status', 'Active')->orderBy('id', 'desc');

            if ($request->has('search')) {
                $search = $request->search;
                $query->whereHas('productTranslations', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            if ($request->has('category_id')) {
                $query->whereHas('categories', function ($q) use ($request) {
                    $q->whereIn('category.id', $request->input('category_id'));
                });
            }


            $perPage = $request->input('per_page', 10);
            $products = $query->paginate($perPage);


            if ($products->isEmpty()) {
                return $this->apiRespose(
                    null,
                    'No products found',
                    true,
                    200
                );
            }

            return $this->ApiResponsePaginationTrait(
                ProductResource::collection($products),
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

    public function best(Request $request)
    {
        try {
            $query = Product::with([
                'images',
                'categories.CategoryTranslations',
                'Subcategories.CategoryTranslations',
                'productVariants',
                'productTranslations',
                'productTax',
                'Brand.brandTranslation',

            ])->where('status', 'Active')->inRandomOrder();


            $perPage = $request->input('per_page', 10);
            $products = $query->paginate($perPage);


            if ($products->isEmpty()) {
                return $this->ApiResponsePaginationTrait(
                    null,
                    'No products found',
                    true,
                    200
                );
            }

            return $this->ApiResponsePaginationTrait(
                ProductResource::collection($products),
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
            $product,
            'Product retrieved successfully',
            true,
            200
        );
    }


}


<?php

namespace App\Http\Controllers\Vibein;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\dashboard\ProductResource;
use App\Models\Product;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        try {
            $query = Product::select('id', 'status')->where('status','Active') // تحديد الأعمدة
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



}


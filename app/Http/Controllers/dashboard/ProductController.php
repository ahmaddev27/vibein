<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\ApiResponsePaginationTrait;
use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    use ApiResponsePaginationTrait;
    use ApiResponseTrait;

    public function index(Request $request)
    {
        try {
            $query = Product::with(['categories.CategoryTranslations', 'productVariants', 'productTranslations', 'productBrand', 'productTax']);

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

}

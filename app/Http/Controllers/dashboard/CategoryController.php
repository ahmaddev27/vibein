<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\ApiResponsePaginationTrait;
use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    use ApiResponsePaginationTrait;
    use ApiResponseTrait;

    public function index(Request $request)
    {
        try {
            $query = Category::withCount('products')->with(['CategoryTranslations']);
            $perPage = $request->input('per_page', 10);
            $products = $query->paginate($perPage);

            if ($products->isEmpty()) {
                return $this->ApiResponsePaginationTrait(
                    $products,
                    'No Categories found',
                    true,
                    200
                );
            }

            return $this->ApiResponsePaginationTrait(
                $products,
                'Categories retrieved successfully',
                true,
                200
            );
        } catch (\Exception $e) {
            // Log the error with stack trace
            Log::error('Categories index error: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->apiRespose(
                ['error' => 'Server error occurred'],
                'Error retrieving Categories',
                false,
                500
            );
        }
    }

}

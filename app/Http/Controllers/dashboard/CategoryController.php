<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\ApiResponsePaginationTrait;
use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Models\Category;
use App\Models\CategoryTranslations;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        try {
            $query = Category::whereNot('parentCategoryId',null)->withCount('products')->with(['CategoryTranslations'])->orderBy('id','desc');
            $perPage = $request->input('per_page', 10);
            $paginator = $query->paginate($perPage);

            if ($paginator->isEmpty()) {
                return $this->ApiResponsePaginationTrait(
                    $paginator,
                    'No Categories found',
                    true,
                    200
                );
            }

            return $this->ApiResponsePaginationTrait(
                $paginator,
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

    public function SubCategoriesIndex(Request $request)
    {
        try {
            $query = Category::whereNot('parentCategoryId',null)->withCount('products')->with(['CategoryTranslations']);
            $perPage = $request->input('per_page', 10);
            $paginator = $query->paginate($perPage);

            if ($paginator->isEmpty()) {
                return $this->ApiResponsePaginationTrait(
                    $paginator,
                    'No Sub Categories found',
                    true,
                    200
                );
            }

            return $this->ApiResponsePaginationTrait(
                $paginator,
                'Sub Categories retrieved successfully',
                true,
                200
            );
        } catch (\Exception $e) {
            // Log the error with stack trace
            Log::error('Sub Categories index error: ' . $e->getMessage(), [
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


    public function store(StoreCategoryRequest $request)
    {
        // Prepare category data
        $categoryData = [
            'parentCategoryId' => $request->parentCategoryId,
            'companyId' => $request->companyId,
            'showStatus' => $request->showStatus,
            'sortOrder' => $request->sortOrder ?? 0,
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            $categoryData['image'] = $request->file('image')->store('categories', 'public');
        }

        // Create category
        $category = Category::create($categoryData);

        // Create translation
        $translationData = [
            'categoryId' => $category->id,
            'name' => $request->name,
            'description' => $request->description,
            'metaTagTitle' => $request->metaTagTitle,
            'metaTagDescription' => $request->metaTagDescription,
            'metaTagKeywords' => $request->metaTagKeywords,
            'languageCode' => $request->languageCode,
        ];

       CategoryTranslations::create($translationData);

        return $this->apiResponse(
            $category->load('CategoryTranslations'),
            'Category created successfully',
            true,
            201
        );

    }

}

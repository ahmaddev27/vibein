<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Models\Category;
use App\Models\CategoryTranslations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {


        try {
            $categories = Category::where('parentCategoryId', null)->withCount('products')->with(['CategoryTranslations']);


            if ($request->has('status')) {
                $categories->where('showStatus', $request->status);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $categories->whereHas('CategoryTranslations', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }


            // Apply sorting
            $sortField = $request->get('sort_by', 'createdAt');
            $sortDirection = $request->get('sort_dir', 'desc');
            $categories->orderBy($sortField, $sortDirection);


            $perPage = $request->input('per_page', 10);
            $paginator = $categories->paginate($perPage);

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

    public function show($id)
    {

        $category = Category::with(['CategoryTranslations'])->find($id);
        if (!$category) {
            return $this->apiResponse(
                null,
                'Category not found',
                false,
                404
            );
        }
        return $this->apiResponse(
            $category,
            'Category retrieved successfully',
            true,
            200
        );
    }


    public function update(StoreCategoryRequest $request, $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return $this->apiResponse(
                null,
                'Category not found',
                false,
                404
            );
        }
        // Prepare category data
        $categoryData = [
            'parentCategoryId' => $request->parentCategoryId,
            'companyId' => $request->companyId,
            'showStatus' => $request->showStatus,
            'sortOrder' => $request->sortOrder ?? 0,
        ];
        // Handle image upload
        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }

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
            'Category updated successfully',
            true,
            200
        );

    }

    public function destroy($id)
    {

        $category = Category::find($id);
        if (!$category) {
            return $this->apiResponse(
                null,
                'Category not found',
                false,
                404
            );
        }

        // Delete the category image if it exists
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        // Delete the category
        $category->delete();

        return $this->apiResponse(
            null,
            'Category deleted successfully',
            true,
            200
        );


    }
}



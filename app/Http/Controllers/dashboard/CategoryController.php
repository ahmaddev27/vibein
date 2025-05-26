<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Resources\dashboard\CategoryResource;
use App\Models\Category;
use App\Models\CategoryTranslations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        try {
            $categories = Category::select('id', 'showStatus', 'image') // تحديد الأعمدة فقط
            ->whereNull('parentCategoryId')
                ->withCount('products')
                ->with('CategoryTranslations');

            if ($request->filled('status')) {
                $categories->where('showStatus', $request->status);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $search_by = $request->get('search_by', 'name');

                $categories->whereHas('CategoryTranslations', function ($q) use ($search, $search_by) {
                    $q->where($search_by, 'ilike', "%{$search}%");
                });
            }

            // Sorting
            $allowedSortFields = ['created_at', 'name', 'products_count'];
            $sortField = in_array($request->get('sort_by'), $allowedSortFields) ? $request->get('sort_by') : 'createdAt';
            $sortDirection = in_array(strtolower($request->get('sort_dir')), ['asc', 'desc']) ? $request->get('sort_dir') : 'desc';

            $categories->orderBy($sortField, $sortDirection);

            // Pagination
            $perPage = (int)$request->input('per_page', 10);
            $paginator = $categories->paginate($perPage);

            if ($paginator->isEmpty()) {
                return $this->apiRespose(null, 'No Categories found', true, 200);
            }

            return $this->ApiResponsePaginationTrait(
                CategoryResource::collection($paginator),
                'Categories retrieved successfully',
                true,
                200
            );
        } catch (\Exception $e) {
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
//            'parentCategoryId' => $request->parentCategoryId,
            'showStatus'=>   $request->showStatus,

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
            new CategoryResource($category->load('CategoryTranslations')),
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
            new CategoryResource($category->load('CategoryTranslations')),
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

        DB::beginTransaction();
        try {
            // Prepare category data
            $categoryData = [
//            'parentCategoryId' => $request->parentCategoryId,
                'showStatus'=>   $request->showStatus,


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
            $category->update($categoryData);

            $category->CategoryTranslations()->update([
                'name' => $request->name,
                'description' => $request->description,
                'metaTagTitle' => $request->metaTagTitle,
                'metaTagDescription' => $request->metaTagDescription,
                'metaTagKeywords' => $request->metaTagKeywords,
                'languageCode' => $request->languageCode,
            ]);


            DB::commit();

            return $this->apiResponse(
                new CategoryResource($category->load('CategoryTranslations')),
                'Category updated successfully',
                true,
                200
            );

        } catch (\Exception $e) {
            DB::rollBack();

            return $this->apiResponse(
                null,
                'Failed to update Category: ' . $e->getMessage(),
                false,
                500
            );
        }
    }

    public function destroy($id)
    {


        $category = Category::find($id);
        $hasProduct = DB::table('productCategories')
            ->where('categoryId', $id)
            ->exists();


        if (!$category) {
            return $this->apiResponse(
                null,
                'Category not found',
                false,
                404
            );
        }

        if ($hasProduct) {
            return $this->apiResponse(
                null,
                'Category cannot be deleted because it is associated with products',
                false,
                400
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

    public function deleteImage($id)
    {

        try {
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
                $category->image = null;
                $category->save();
            }

            return $this->apiResponse(
                null,
                'Image deleted successfully',
                true,
                200
            );


        } catch (\Exception $e) {
            Log::error('Image deletion error: ' . $e->getMessage(), [
                'exception' => $e,
                'id' => $id
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



<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\QuickPickRequest;
use App\Http\Resources\dashboard\QuickPickResource;
use App\Models\QuickPick;
use Illuminate\Http\Request;


class QuickPickController extends Controller
{
    use ApiResponseTrait;


    public function index(Request $request)
    {

        $query = QuickPick::query();


//        if ($request->status) {
//            $query->where('status', $request->status);
//        }


        if ($request->search) {
            $search = $request->search;
            $search_by = $request->get('search_by', 'days');
            $query->where($search_by, 'ilike', "%{$search}%");
        }


        // Apply sorting
        $sortField = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_dir', 'desc');
        $query->orderBy($sortField, $sortDirection);


        $perPage = $request->input('per_page', 10);
        $cycles = $query->paginate($perPage);


        if ($cycles->isEmpty()) {
            return $this->apiResponse(
                null,
                'No Quick Picks found',
                true,
                200
            );
        }

        return $this->ApiResponsePaginationTrait(
            QuickPickResource::collection($cycles),
            'Quick Picks retrieved successfully',
            true,
            200
        );
    }


    public function store(QuickPickRequest $request)
    {

        $quickPick = QuickPick::create($request->only([
            'name', 'title', 'description', 'meta_title', 'meta_description'
        ]));


        if ($request->filled('products')) {
            $syncData = collect($request->products)->mapWithKeys(function ($product) {
                return [$product['id'] => ['count' => $product['count']]];
            })->toArray();

            $quickPick->products()->sync($syncData);
        }

        return $this->apiResponse(

            new QuickPickResource($quickPick),
            'Quick Pick created successfully',
            true,
            200
        );

    }


    public function update(QuickPickRequest $request, $id)
    {
        $quickPick = QuickPick::find($id);

        if (!$quickPick) {
            return $this->apiResponse(
                null,
                'Quick Pick not found',
                false,
                404
            );
        }

        $quickPick->update($request->only([
            'name',
            'title',
            'description',
            'meta_title',
            'meta_description'
        ]));


        if ($request->filled('products')) {
            $syncData = collect($request->products)->mapWithKeys(function ($product) {
                return [$product['id'] => ['count' => $product['count']]];
            })->toArray();

            $quickPick->products()->sync($syncData);
        }

        return $this->apiResponse(
            new QuickPickResource($quickPick),
            'Quick Pick updated successfully',
            true,
            200
        );
    }


    public function show($id)
    {
        $cycle = QuickPick::find($id);
        if (!$cycle) {
            return $this->apiResponse(
                null,
                'Quick Pick not found',
                false,
                404
            );
        }
        return $this->apiResponse(
            new QuickPickResource($cycle),
            'Quick Pick retrieved successfully',
            true,
            200
        );


    }


    public function destroy($id)
    {
        $cycle = QuickPick::find($id);
        if (!$cycle) {
            return $this->apiResponse(  
                null,
                'Quick Pick not found',
                false,
                404
            );
        }


        $cycle->delete();
        return $this->apiResponse(
            null,
            'Quick Pick deleted successfully',
            true,
            200
        );

    }


}

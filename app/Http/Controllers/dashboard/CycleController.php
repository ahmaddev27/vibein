<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCycleRequest;
use App\Http\Resources\CycleResource;
use App\Models\Cycle;
use Illuminate\Http\Request;


class CycleController extends Controller
{
    use ApiResponseTrait;


    public function index(Request $request)
    {

        $query = Cycle::query();


        if ($request->status) {
            $query->where('status', $request->status);
        }


        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
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
                'No Cycles found',
                true,
                200
            );
        }

        return $this->ApiResponsePaginationTrait(
            CycleResource::collection($cycles),
            'Cycles retrieved successfully',
            true,
            200
        );
    }


    public function store(StoreCycleRequest $request)
    {

        $created = Cycle::create([
            'status' => $request->status,
            'days' => json_encode($request->days),
            'name' => count($request->days) . ' Day' . (count($request->days) > 1 ? 's' : '') . ' per Week',
        ]);


        return $this->apiResponse(

            new CycleResource($created),
            'Cycle created successfully',
            true,
            200
        );

    }


    public function show($id)
    {
        $cycle = Cycle::find($id);
        if (!$cycle) {
            return $this->apiResponse(
                null,
                'Cycle not found',
                false,
                404
            );
        }
        return $this->apiResponse(
            new CycleResource($cycle),
            'Cycle retrieved successfully',
            true,
            200
        );


    }


//    public function update(StoreCycleRequest $request, $id)
//    {
//        $cycle = Cycle::find($id);
//        if (!$cycle) {
//            return $this->apiResponse(
//                null,
//                'Cycle not found',
//                false,
//                404
//            );
//        }
//
//        $cycle->update([
//            'status' => $request->status,
//            'days' => json_encode($request->days),
//            'name' => count($request->days) . ' Day' . (count($request->days) > 1 ? 's' : '') . ' per Week',
//
//        ]);
//
//        return $this->apiResponse(
//            new CycleResource($cycle),
//            'Cycle updated successfully',
//            true,
//            200
//        );
//
//    }


    public function destroy($id)
    {
        $cycle = Cycle::find($id);
        if (!$cycle) {
            return $this->apiResponse(
                null,
                'Cycle not found',
                false,
                404
            );
        }

        if ($cycle->packages()->count() > 0) {
            return $this->apiResponse(
                null,
                'Cycle cannot be deleted because it is associated with one or more packages',
                false,
                400
            );
        }

        $cycle->delete();
        return $this->apiResponse(
            null,
            'Cycle deleted successfully',
            true,
            200
        );

    }


}

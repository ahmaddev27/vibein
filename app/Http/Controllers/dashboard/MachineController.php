<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\MachineRequest;
use App\Http\Resources\MachineResource;
use App\Models\Machine;
use App\Models\MachineImages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MachineController extends Controller
{
    use ApiResponseTrait;


    public function index(Request $request)
    {

        $machines = Machine::with('images', 'category');
        if ($request->has('status')) {
            $machines->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $search_by = $request->get('search_by', 'name');
            $machines->where(function ($query) use ($search, $search_by) {
                $query->where($search_by, 'like', "%{$search}%");
            });
        }

        if ($request->has('category_id')) {
            $machines->where('category_id', $request->category_id);
        }

        // Apply sorting
        $sortField = $request->get('sort_by', 'createdAt');
        $sortDirection = $request->get('sort_dir', 'desc');
        $machines->orderBy($sortField, $sortDirection);

        // Paginate results
        $perPage = $request->input('per_page', 10);
        $machines = $machines->paginate($perPage);


        if ($machines->isEmpty()) {
            return $this->apiRespose(
                null,
                'No Machines found',
                true,
                200
            );
        }


        return $this->ApiResponsePaginationTrait(
            MachineResource::collection($machines),
            'Machine retrieved successfully',
            true,
            200
        );


    }

    public function store(MachineRequest $request)
    {
        DB::beginTransaction();
        try {

            $machine = Machine::create([
                'name' => $request->name,
                'description' => $request->description,
                'status' => $request->status,
                'size' => $request->size,
                'meta_title' => $request->meta_title,
                'category_id' => $request->category_id,
            ]);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('machines', 'public');
                    $machine->images()->create([
                        'image' => $path,
                    ]);
                }
            }

            DB::commit();
            return $this->apiResponse(
                new MachineResource($machine),
                'Machine created successfully',
                true,
                201
            );


        } catch (\Exception $e) {

            DB::rollBack();
            return $this->apiResponse(
                [$e->getMessage()],
                'Internal Server Error',
                false,
                500
            );

        }
    }

    public function update(MachineRequest $request, $id)
    {

        DB::beginTransaction();
        try {
            $machine = Machine::find($id);
            if (!$machine) {
                return $this->apiResponse(
                    null,
                    'Machine not found',
                    false,
                    404
                );
            }

            $machine->update([
                'name' => $request->name,
                'description' => $request->description,
                'status' => $request->status,
                'size' => $request->size,
                'meta_title' => $request->meta_title,
                'category_id' => $request->category_id,
            ]);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('machines', 'public');
                    $machine->images()->create([
                        'image' => $path,
                    ]);
                }
            }

            DB::commit();
            return $this->apiResponse(
                new MachineResource($machine),
                'Machine updated successfully',
                true,
                200
            );

        } catch (\Exception $e) {

            DB::rollBack();
            return $this->apiResponse(
                [$e->getMessage()],
                'Internal Server Error',
                false,
                500
            );
        }
    }

    public function show($id)
    {
        $machine = Machine::with('images', 'category')->find($id);

        if (!$machine) {
            return $this->apiResponse(
                null,
                'Machine not found',
                false,
                404
            );
        }

        return $this->apiResponse(
            new MachineResource($machine),
            'Machine retrieved successfully',
            true,
            200
        );

    }

    public function destroy($id)
    {
        $machine = Machine::find($id);
        if (!$machine) {
            return $this->apiResponse(
                null,
                'Machine not found',
                false,
                404
            );
        }

        if ($machine->images) {
            foreach ($machine->images as $image) {

                if ($image->image) {
                    Storage::disk('public')->delete($image->image);
                }

                $image->delete();
            }
        }

        $machine->delete();
        return $this->apiResponse(
            null,
            'Machine deleted successfully',
            true,
            200
        );


    }

    public function deleteImage($id)
    {
        $image = MachineImages::find($id);
        if (!$image) {
            return $this->apiResponse(
                null,
                'Image not found',
                false,
                404
            );
        }

        if ($image->image) {
            Storage::disk('public')->delete($image->image);
        }
        $image->delete();
        return $this->apiResponse(
            null,
            'Image deleted successfully',
            true,
            200
        );
    }

}

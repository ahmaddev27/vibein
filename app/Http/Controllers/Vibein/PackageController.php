<?php

namespace App\Http\Controllers\Vibein;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\dashboard\PackageResource;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PackageController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        try {
            $query = Package::with(['products.product.productTranslations', 'images'])->where('status', 1);
            $perPage = $request->input('per_page', 10);

            // Apply search filter if provided

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }


            if ($request->search) {
                $search = $request->search;
                $search_by = $request->get('search_by', 'name');
                $query->where($search_by, 'ilike', "%{$search}%");
            }

            // Apply sorting
            $sortField = $request->get('sort_by', 'created_at');
            $sortDirection = $request->get('sort_dir', 'desc');
            $query->orderBy($sortField, $sortDirection);

            $packages = $query->paginate($perPage);


            if ($packages->isEmpty()) {
                return $this->apiRespose(
                    null,
                    'No Packages found',
                    true,
                    200
                );
            }

            return $this->ApiResponsePaginationTrait(
                PackageResource::collection($packages),
                'Packages retrieved successfully',
                true,
                200
            );
        } catch (\Exception $e) {
            // Log the error with stack trace
            Log::error('Packages index error: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->apiRespose(
                ['error' => 'Server error occurred'],
                'Error retrieving Packages',
                false,
                500
            );
        }
    }


    public function show($id)
    {
        $package = Package::with(['products.product.productTranslations', 'images'])->find($id);

        if (!$package) {
            return $this->apiRespose(
                null,
                'Package not found',
                false,
                404
            );
        }

        return $this->apiRespose(
            new PackageResource($package),
            'Package retrieved successfully',
            true,
            200
        );
    }


}

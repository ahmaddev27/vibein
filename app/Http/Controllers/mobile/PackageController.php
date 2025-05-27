<?php

namespace App\Http\Controllers\mobile;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\PackageResource;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PackageController extends Controller
{
    use ApiResponseTrait;


    public function index(Request $request)
    {
        try {
            $query = Package::with(['products.product.productTranslations', 'images'])->orderBy('id', 'desc');


            if ($request->filled('search')) {
                $searchTerm = '%' . $request->input('search') . '%';
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', $searchTerm)
                        ->orWhere('description', 'like', $searchTerm);
                });
            }


            $perPage = $request->input('per_page', 10);
            $packages = $query->paginate($perPage);

            if ($packages->isEmpty()) {
                return $this->apiRespose(
                    [],
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


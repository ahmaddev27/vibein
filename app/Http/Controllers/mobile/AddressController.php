<?php

namespace App\Http\Controllers\mobile;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\mobile\AddressResource;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {

        $address = auth()->user()->address()->get();

        if ($address) {
            return $this->apiRespose(
                AddressResource::collection($address),
                'Address retrieved successfully',
                true,
                200
            );
        } else {
            return $this->apiRespose(
                [],
                'No Address found',
                true,
                200
            );
        }
    }

    public function store(Request $request)
    {

        $request->validate([
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'area' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'building_number' => 'required|string|max:255',
            'is_default' => 'boolean'
        ]);
        $address = auth()->user()->address()->create($request->all());


        if ($request->is_default) {
            auth()->user()->address()->update(['is_default' => false]);
            $address->update(['is_default' => true]);
        } else {
            $address->update(['is_default' => false]);
        }

        if ($address) {
            return $this->apiRespose(
                new AddressResource($address),
                'Address created successfully',
                true,
                200
            );
        } else {
            return $this->apiRespose(
                [],
                'Failed to create address',
                false,
                500
            );
        }

    }

    public function update(Request $request)
    {
        $request->validate([
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'area' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'building_number' => 'required|string|max:255',
            'is_default' => 'boolean'
        ]);

        $address = auth()->user()->address()->find($request->id);

        if (!$address) {
            return $this->apiRespose(
                [],
                'Address not found',
                false,
                404
            );
        }

        $address->update($request->all());

        if ($request->is_default) {
            auth()->user()->address()->update(['is_default' => false]);
            $address->update(['is_default' => true]);
        } else {
            $address->update(['is_default' => false]);
        }

        return $this->apiRespose(
            new AddressResource($address),
            'Address updated successfully',
            true,
            200
        );

    }

    public function destroy($id)
    {

        $address = auth()->user()->address()->find($id);

        if (!$address) {
            return $this->apiRespose(
                [],
                'Address not found',
                false,
                404
            );
        }

        $address->delete();

        return $this->apiRespose(
            [],
            'Address deleted successfully',
            true,
            200
        );
    }

    public function show($id)
    {
        $address = auth()->user()->address()->find($id);
        if (!$address) {
            return $this->apiRespose(
                [],
                'Address not found',
                false,
                404
            );
        }
        return $this->apiRespose(
            new AddressResource($address),
            'Address retrieved successfully',
            true,
            200
        );
    }

    public function setDefault($id)
    {
        $address = auth()->user()->address()->find($id);

        if (!$address) {
            return $this->apiRespose(
                [],
                'Address not found',
                false,
                404
            );
        }

        auth()->user()->address()->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return $this->apiRespose(
            new AddressResource($address),
            'Address set as default successfully',
            true,
            200
        );


}
}

<?php

namespace App\Http\Resources\dashboard;

use App\Http\Resources\CycleResource;
use App\Http\Resources\mobile\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status ? 1 : 0,
            'tags' => $this->tags,
            'products' => $this->products->map(function ($packageProduct) {
                // المنتج الأساسي
                $prod = $packageProduct->product;

                return [
                    'product' => new ProductResource($prod), // تم تعديل هذا السطر
                    'alternatives' => $packageProduct->alternatives->map(function ($alt) {
                        $altProd = $alt->addOnProduct; // علاقة belongsTo(Product::class, 'product_id')
                        return new ProductResource($altProd, $alt->add_on);
                    }),
                ];
            }),
            'images' => $this->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'url' => url('storage/' . $image->image),
                ];
            }),
//            'cycles' => $this->cycles->map(function ($cycle) {
//                return array_merge(
//                    (new CycleResource($cycle))->toArray(request()),
//                    ['price' => $cycle->pivot->price]
//                );
//            }),

            'cycles' => $this->cycles->map(function ($cycle) {
                return array_merge(
                    (new CycleResource($cycle))->toArray(request()),
                    ['price' => $cycle->pivot->price]
                );
            })->when($this->one_time == 1, function ($cycles) {
                // نضيف سايكل "one_time" مهجنة
                $oneTimeCycle = [
                    'id' => 0,
                    'name' => 'one time',
                    'status' => 1,
                    'days' => [],
                    'days_count' => 0,
                    'price' => $this->one_time_price,
                ];
                return $cycles->push($oneTimeCycle);
            })->sortBy('id')->values()->all(),

        ];
    }
}

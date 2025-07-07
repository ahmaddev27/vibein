<?php

namespace App\Http\Resources\dashboard;

use App\Http\Resources\CycleResource;
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
            'one_time'=>$this->one_time,
            'one_time_price'=>$this->one_time_price,
            'products' => $this->products->map(function ($packageProduct) {
                // المنتج الأساسي
                $prod = $packageProduct->product;

                return [
                    'product' => new ProductResource($prod),
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
            })

        ];
    }
}

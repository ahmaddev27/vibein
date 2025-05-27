<?php

namespace App\Http\Resources;

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
//            'price' => number_format($this->price, 2), // deleted need
//            'total' => number_format($this->total + $this->price, 2), // deleted need
            'status' => $this->status ? 1 : 0,
            'tags' => $this->tags,
            'products' => $this->products->map(function ($packageProduct) {
                // المنتج الأساسي
                $prod = $packageProduct->product;

                return [
                    'id' => $prod->id,
                    'name' => optional($prod->productTranslations->first())->name,
                    'image' => $prod->images->first() ? url('storage/' . $prod->images->first()->image) : null,

                    // هنا نضمّن البدائل الخاصة بكل منتج
                    'alternatives' => $packageProduct->alternatives->map(function ($alt) {
                        $altProd = $alt->addOnProduct; // علاقة belongsTo(Product::class, 'product_id')
                        return [
                            'id' => $altProd->id,
                            'name' => optional($altProd->productTranslations->first())->name,
                            'image' => $altProd->images->first() ? url('storage/' . $altProd->images->first()->image) : null,

                            'add_on' => number_format($alt->add_on, 2),


                        ];
                    }),
                ];
            }),
            'images' => $this->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'url' => url('storage/' . $image->image),
                ];
            }),


            'cycles' => $this->cycles->map(function ($cycle) {
                return array_merge(
                    (new CycleResource($cycle))->toArray(request()),
                    ['price' => $cycle->pivot->price]
                );
            }),

        ];
    }
}

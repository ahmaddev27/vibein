<?php

namespace App\Http\Resources\mobile;

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
            'price' => $this->price,
            'total' => $this->total + $this->price,
            'status' => $this->status,
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

                            'add_on' => $alt->add_on, // السعر الإضافي
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

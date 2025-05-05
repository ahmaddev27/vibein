<?php

namespace App\Http\Resources\mobile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'name' => $this?->productTranslations->first()->name,
            'description' => $this?->productTranslations->first()->description,
            'label' => $this->lable,


            'images' => $this->images?->map(function ($image) {
                return
                    url('storage/' . $image->image);

            }),


            'category' => new CategoryResource($this->categories->first()),
            'brand' => new BrandResource($this->Brand),


            'prices' => collect($this->productVariants?->first()?->prices)->map(function ($variant) {
                return [
                    'id' => $variant['id'] ?? null,  // Mapping the weight
                    'weight' => $variant['weight'],  // Mapping the weight
                    'price' => $variant['price'],    // Mapping the price
                    'quantity' => $variant['quantity'] ?? null,    // Mapping the quantity
                ];
            }) ?: [],  // إرجاع مصفوفة فارغة إذا لم تكن هناك أي أسعار


        ];
    }
}

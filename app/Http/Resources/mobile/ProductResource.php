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
                return [
                    $image->image,
                ];
            }),

            'categories' => $this->categories?->map(function ($category) use ($request) {
                // تحقق إذا كانت هناك ترجمة موجودة
                $translation = $category?->CategoryTranslations->first();

                return [
                    $translation ? $translation->name : '',
                ];
            }),




            'prices' => collect($this->productVariants?->first()?->prices)->map(function ($variant) {
                return [
                    'weight' => $variant['weight'],  // Mapping the weight
                    'price' => $variant['price'],    // Mapping the price
                    'quantity' => $variant['quantity']??null,    // Mapping the quantity
                ];
            }) ?: [],  // إرجاع مصفوفة فارغة إذا لم تكن هناك أي أسعار

            'brand' => $this->Brand?->brandTranslation->first()->name ?? null,



        ];
    }
}

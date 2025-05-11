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
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
            'price'       => $this->price,
            'total'       => $this->total + $this->price,
            'status'      => $this->status,
            'tags'        => $this->tags,
            'products'    => $this->products->map(function ($packageProduct) {
                // المنتج الأساسي
                $prod = $packageProduct->product;

                return [
                    'id'           => $prod->id,
                    'name'         => optional($prod->productTranslations->first())->name,

                    // هنا نضمّن البدائل الخاصة بكل منتج
                    'alternatives' => $packageProduct->alternatives->map(function ($alt) {
                        $altProd = $alt->addOnProduct; // علاقة belongsTo(Product::class, 'product_id')
                        return [
                            'id'       => $altProd->id,
                            'name'     => optional($altProd->productTranslations->first())->name,
                            'add_on'   => $alt->add_on, // السعر الإضافي
                        ];
                    }),
                ];
            }),
            'images' => $this->images->map(function ($image) {
                return [
                    'id'    => $image->id,
                    'url'   => url('storage/' . $image->image),
                ];
            }),
        ];
    }
}

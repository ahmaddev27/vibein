<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackegeResource extends JsonResource
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
            'total' => $this->total,
            'products' => $this->products->map(function ($product) {
                return [
                    'id' => $product->product->id,
                    'name' => $product->product->productTranslations->first()->name,
                    'position' => $product->position,
                    'is_selected' => $product->is_selected,

                ];
            }),

            'alternatives' => $this->alternatives->map(function ($alternative) {
                return [
                    'id' => $alternative->product->id,
                    'name' => $alternative->product->productTranslations->first()->name,
                    'position' => $alternative->position,
                    'is_selected' => $alternative->is_selected,
                    'add_on'=> $alternative->add_on,
                ];
            }),

            'images' => $this->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'image' => url('storage/' . $image->image),
                ];
            }),
        ];

    }
}

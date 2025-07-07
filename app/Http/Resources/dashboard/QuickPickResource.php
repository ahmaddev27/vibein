<?php

namespace App\Http\Resources\dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuickPickResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'image' => $this->image ? url('storage') . '/' . $this->image : null,
            'features'=>$this->features,

            'products' => $this->products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'product' => new ProductResource($product),
                    'count' => $product->pivot->count,
                ];
            }),
        ];
    }
}

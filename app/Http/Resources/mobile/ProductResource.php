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
    protected $add_on;

    public function __construct($resource, $add_on = null)
    {
        parent::__construct($resource);
        $this->add_on = $add_on;
    }


    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->productTranslations?->first()?->name,
            'description' => $this->productTranslations?->first()?->description,
            'label' => $this->label ?? null,

            'images' => $this->images?->map(function ($image) {
                    return url('storage/' . $image->image);
                }) ?? [],

            'category' => new CategoryResource($this->categories?->first()),
            'brand' => new BrandResource($this->brand),

            'prices' => collect($this->productVariants?->first()?->prices ?? [])->map(function ($variant) {
                return [
                    'id' => $variant['id'] ?? null,
                    'weight' => $variant['weight'] ?? null,
                    'price' => $variant['price'] ?? null,
                    'quantity' => $variant['quantity'] ?? null,
                ];
            })->values()->all(),
        ];

        if ($this->add_on) {
            $data['add_on'] = $this->add_on;
        }

        return $data;
    }

}
